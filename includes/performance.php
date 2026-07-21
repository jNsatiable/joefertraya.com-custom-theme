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
 * Dequeue Gutenberg's block-library / classic-theme-styles CSS (~8KB
 * inlined into every <head>). Safe here: every template is hand-coded
 * PHP, and the one post that used to be Elementor-built was migrated to
 * plain HTML (includes/migrate-post-2411.php) — nothing on this site
 * renders core blocks. Revisit if a future page ever uses the block
 * editor.
 */
add_action( 'wp_enqueue_scripts', 'jt_dequeue_unused_block_styles', 100 );

function jt_dequeue_unused_block_styles() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
}

/**
 * 'global-styles' (the :root{--wp--preset--*} custom-properties block,
 * ~1-3KB) resisted wp_dequeue_style() from two different hooks
 * (wp_enqueue_scripts priority 100, wp_head priority 1) — confirmed live
 * both attempts left <style id="global-styles-inline-css"> in place while
 * block-library/classic-theme-styles both dequeued cleanly from the same
 * first hook, so it's being re-enqueued after the dequeue window rather
 * than skipping the dequeue mechanism entirely. Also can't reach it via
 * style_loader_tag: that filter only covers a handle's own <link>/<style
 * src> tag — WP_Styles::print_inline_style() builds the separate
 * "{handle}-inline-css" block through its own, unfiltered code path.
 *
 * Buffering and stripping the one specific tag by id is the only
 * mechanism that reaches it regardless of when/how it's (re-)added — it
 * operates on the final output text, not the enqueue system.
 *
 * Originally scoped to just the wp_head action (start at priority -9999,
 * end at 9999) — confirmed working live 2026-07-17, then found live
 * 2026-07-21 to have stopped stripping the tag despite the regex still
 * matching it byte-for-byte when tested against the unstripped output.
 *
 * First attempt widened this to wrap the entire front-end response
 * (start on template_redirect, end on shutdown) on the theory that
 * something else was touching output buffering inside the narrow
 * wp_head window. Confirmed via a throwaway marker (#101) that the
 * deploy pipeline and this file were never the problem — the marker's
 * own wp_head output showed up live immediately, but global-styles
 * still wasn't stripped, meaning jt_buffer_head_end's replacement
 * never actually reached the browser. Root cause: WP core registers
 * its own safety-net output-buffer flush as a raw shutdown function
 * (wp_ob_end_flush_all(), outside the 'shutdown' action's own priority
 * system entirely) to catch buffers left open by mistake — it can run
 * before our 'shutdown'-hooked callback does, sending our buffer's
 * original unmodified content before we get a chance to clean it.
 *
 * Ending back on wp_head sidesteps that race entirely (core's shutdown
 * flush is a non-issue if there's no buffer left open by the time
 * shutdown runs). Starting point stays on template_redirect, ending at
 * PHP_INT_MAX instead of a fixed 9999 to sit after anything else that
 * might hook wp_head at a very late priority.
 */
add_action( 'template_redirect', 'jt_buffer_head_start', -9999 );
add_action( 'wp_head', 'jt_buffer_head_end', PHP_INT_MAX );

function jt_buffer_head_start() {
	ob_start();
}

function jt_buffer_head_end() {
	$page = ob_get_clean();
	echo preg_replace( "#<style id=['\"]global-styles-inline-css['\"][^>]*>.*?</style>\s*#s", '', $page );
}

/**
 * Preconnect to the Portfolio page's third-party origins — Flickr
 * (live.staticflickr.com for the embed cover images, embedr.flickr.com
 * for the enhancement script) and Google Tag Manager. Self-hosting isn't
 * an option for the Flickr images (the Portfolio gallery open item in
 * CLAUDE.md: the embed stays pointed at Flickr on purpose, to keep its
 * own reach/discoverability), so this is the lighter-touch equivalent of
 * what self-hosting did for fonts — skip the DNS+TLS round trip's
 * latency even though the request itself still has to happen.
 */
add_filter( 'wp_resource_hints', 'jt_portfolio_preconnect_hints', 10, 2 );

function jt_portfolio_preconnect_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type && is_page( 'portfolio' ) ) {
		$urls[] = 'https://live.staticflickr.com';
		$urls[] = 'https://embedr.flickr.com';
		$urls[] = 'https://www.googletagmanager.com';
	}
	return $urls;
}
