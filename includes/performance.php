<?php
/**
 * Performance: strip WordPress core boilerplate this theme never uses.
 * Confirmed live 2026-07-17 via direct HTML inspection (PageSpeed's own
 * report page renders lab data client-side and the public API was rate-
 * limited, so this was verified by fetching the live markup directly) —
 * same class of issue as Rentl's 2026-07 audit fix #2.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Disable WordPress's built-in emoji-detection script/styles — unnecessary
 * on any modern browser (all have native emoji support), otherwise loads
 * wp-emoji-release.min.js + inline CSS on every single page. Ported
 * verbatim from Rentl's rentl_disable_emojis().
 */
add_action( 'init', 'jt_disable_emojis' );

function jt_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'jt_disable_emojis_tinymce' );
	add_filter( 'wp_resource_hints', 'jt_disable_emojis_dns_prefetch', 10, 2 );
}

function jt_disable_emojis_tinymce( $plugins ) {
	return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
}

function jt_disable_emojis_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' === $relation_type ) {
		$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/' );
		$urls          = array_diff( $urls, array( $emoji_svg_url ) );
	}
	return $urls;
}

/**
 * Dequeue Gutenberg's block-library / classic-theme-styles / global-styles
 * CSS (~8.8KB inlined into every <head>). Safe here: every template is
 * hand-coded PHP, and the one post that used to be Elementor-built was
 * migrated to plain HTML (includes/migrate-post-2411.php) — nothing on
 * this site renders core blocks. Revisit if a future page ever uses
 * the block editor.
 */
add_action( 'wp_enqueue_scripts', 'jt_dequeue_unused_block_styles', 100 );

function jt_dequeue_unused_block_styles() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'global-styles' );
}
