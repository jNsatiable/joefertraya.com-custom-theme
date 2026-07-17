<?php
/**
 * One-time SEO meta cleanup (2026-07-17). The pages carried stale per-page
 * custom titles in _genesis_title (The SEO Framework's title field) still
 * branded "CyberNurse" — the site-wide title change doesn't touch per-page
 * overrides, so tabs and search snippets kept the old brand.
 *
 * About gets the title Joefer chose in session; Portfolio and Contact have
 * their stale overrides deleted so The SEO Framework auto-generates clean
 * titles from the current Site Title. Contact's meta description also gets
 * its "hearbeat" typo fixed. Same flag-guarded init pattern as the other
 * one-time migrations.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jt_update_seo_titles_once() {
	// v3: Portfolio's auto-generated title measured "far too short" (24
	// chars) in the SEO meter; it now gets a custom title too — deliberately
	// broad per Joefer (not skewed toward retouching/photography), echoing
	// About's "multifaceted" vocabulary. 41 chars; 56 with the auto-suffix.
	// v4: Blog category title updated to match the on-page "The J Files"
	// rebrand (hybrid naming, 2026-07-17).
	// v5: verified live that v4 rendered with no site-name suffix — the
	// term's stored settings carried a "remove site title" toggle from the
	// old title (which had "Joefer Traya" manually embedded), and the merge
	// preserved it. Cleared so the auto-suffix applies like the other pages.
	// All operations are idempotent, so the flag simply re-runs the lot.
	if ( get_option( 'jt_seo_titles_updated_v5' ) ) {
		return;
	}

	// About (6): the chosen custom title, name left to the auto-suffix.
	update_post_meta( 6, '_genesis_title', wp_slash( 'About J — Multifaceted Virtual Assistant' ) );

	// Portfolio (705): broad custom title chosen in session.
	update_post_meta( 705, '_genesis_title', wp_slash( 'Portfolio — The Work of a Multifaceted VA' ) );

	// Contact (375): drop the stale override, let the SEO plugin
	// auto-generate from the current Site Title.
	delete_post_meta( 375, '_genesis_title' );

	// Contact description typo: "hearbeat" -> "heartbeat".
	$desc = get_post_meta( 375, '_genesis_description', true );
	if ( is_string( $desc ) && false !== strpos( $desc, 'hearbeat' ) ) {
		update_post_meta( 375, '_genesis_description', wp_slash( str_replace( 'hearbeat', 'heartbeat', $desc ) ) );
	}

	// Blog category: the tab/search title still carried the old "J's Blog"
	// branding after the on-page rebrand to "The J Files". Terms store TSF
	// overrides in one meta array (not _genesis_title like posts) — merge so
	// any other stored term settings survive. Name left to the auto-suffix,
	// same as the page titles above. Looked up by slug, not a hardcoded ID.
	$jt_blog_cat = get_category_by_slug( 'blog' );
	if ( $jt_blog_cat ) {
		$jt_tsf_meta = get_term_meta( $jt_blog_cat->term_id, 'autodescription-term-settings', true );
		$jt_tsf_meta = is_array( $jt_tsf_meta ) ? $jt_tsf_meta : array();
		$jt_tsf_meta['doctitle']           = 'The J Files — Blog';
		$jt_tsf_meta['title_no_blog_name'] = 0;
		update_term_meta( $jt_blog_cat->term_id, 'autodescription-term-settings', wp_slash( $jt_tsf_meta ) );
	}

	update_option( 'jt_seo_titles_updated_v5', gmdate( 'Y-m-d' ) );
}
add_action( 'init', 'jt_update_seo_titles_once' );
