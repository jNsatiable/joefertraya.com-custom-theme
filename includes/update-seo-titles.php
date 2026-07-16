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
	if ( get_option( 'jt_seo_titles_updated' ) ) {
		return;
	}

	// About (6): the chosen custom title.
	update_post_meta( 6, '_genesis_title', wp_slash( 'About J — Joefer Traya, Multifaceted Virtual Assistant' ) );

	// Portfolio (705) + Contact (375): drop the stale overrides, let the
	// SEO plugin auto-generate from the current Site Title.
	delete_post_meta( 705, '_genesis_title' );
	delete_post_meta( 375, '_genesis_title' );

	// Contact description typo: "hearbeat" -> "heartbeat".
	$desc = get_post_meta( 375, '_genesis_description', true );
	if ( is_string( $desc ) && false !== strpos( $desc, 'hearbeat' ) ) {
		update_post_meta( 375, '_genesis_description', wp_slash( str_replace( 'hearbeat', 'heartbeat', $desc ) ) );
	}

	update_option( 'jt_seo_titles_updated', gmdate( 'Y-m-d' ) );
}
add_action( 'init', 'jt_update_seo_titles_once' );
