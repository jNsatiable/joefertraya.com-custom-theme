<?php
/**
 * Comments — hand-hardened native comments on blog posts ONLY (2026-07-17).
 *
 * History: this file began as a site-wide shutdown (theme-native replacement
 * for the WPCode "Completely Disable Comments" snippet) because a bare theme
 * doesn't close anything — bots POST straight to wp-comments-post.php and the
 * REST endpoints without touching a template. That concern still stands, so
 * the reopening is deliberately narrow: published posts only, gated by the
 * same three spam layers as the contact form (honeypot + time-trap with
 * silent fake success, Cloudflare Turnstile reusing the Settings > JT Theme
 * keys), and EVERY non-moderator comment is held for moderation regardless
 * of wp-admin discussion settings. Pages, CPTs, pings, and trackbacks stay
 * closed. Commenter cookies are disabled so the privacy policy's "one thing
 * in your browser" claim stays true; avatars are off in the template for the
 * same reason (no Gravatar third-party requests).
 *
 * Anonymous REST comment creation is disabled by WP core default (requires
 * the rest_allow_anonymous_comments opt-in, which we never give), so
 * wp-comments-post.php is the only anonymous door — and it's gated below.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==========================================================================
   1. Scope — comments exist on published posts, nowhere else
   ========================================================================== */

function jt_comments_open_posts_only( $open, $post_id ) {
	$post = get_post( $post_id );
	return $post && 'post' === $post->post_type && 'publish' === $post->post_status;
}
add_filter( 'comments_open', 'jt_comments_open_posts_only', 20, 2 );

add_filter( 'pings_open', '__return_false', 20 );

// Never surface stored comments outside posts, whatever a template asks for.
function jt_comments_array_posts_only( $comments, $post_id ) {
	return 'post' === get_post_type( $post_id ) ? $comments : array();
}
add_filter( 'comments_array', 'jt_comments_array_posts_only', 10, 2 );

function jt_scope_comment_support() {
	foreach ( get_post_types() as $post_type ) {
		remove_post_type_support( $post_type, 'trackbacks' );
		if ( 'post' !== $post_type ) {
			remove_post_type_support( $post_type, 'comments' );
		}
	}
}
add_action( 'init', 'jt_scope_comment_support', 100 );

/* ==========================================================================
   2. Spam gates on wp-comments-post.php — same layers as the contact form
   ========================================================================== */

function jt_comment_spam_gate( $comment_post_id ) {
	// Honeypot / time-trap: bots get bounced back to the post as if nothing
	// happened, so they learn nothing. A missing timestamp is also a bot —
	// the real form always includes one.
	$ts = absint( $_POST['jt_ts'] ?? 0 );
	if ( ! empty( $_POST['jt_hp'] ) || ! $ts || ( time() - $ts ) < 3 ) {
		wp_safe_redirect( get_permalink( $comment_post_id ) . '#comments' );
		exit;
	}

	// Turnstile — enforced only once a secret is configured (same keys as
	// the contact form, Settings > JT Theme). A real person can legitimately
	// fail this (expired widget, network hiccup), so it gets a visible error
	// with a back link instead of the silent treatment.
	$turnstile_secret = get_option( 'jt_turnstile_secret_key' );
	if ( $turnstile_secret ) {
		$turnstile_token = sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ?? '' ) );
		if ( ! $turnstile_token || ! jt_verify_turnstile( $turnstile_token, $turnstile_secret ) ) {
			wp_die(
				'CAPTCHA verification failed. Please go back and try submitting your comment again.',
				'Comment blocked',
				array(
					'response'  => 403,
					'back_link' => true,
				)
			);
		}
	}
}
add_action( 'pre_comment_on_post', 'jt_comment_spam_gate' );

/* ==========================================================================
   3. Moderation — every non-moderator comment is held, in code, regardless
      of the Discussion settings screen
   ========================================================================== */

function jt_hold_all_comments( $approved, $commentdata ) {
	if ( current_user_can( 'moderate_comments' ) ) {
		return $approved;
	}
	if ( 'spam' === $approved ) {
		return $approved;
	}
	return '0';
}
add_filter( 'pre_comment_approved', 'jt_hold_all_comments', 20, 2 );

// Name + email required, enforced server-side whatever the option says.
add_filter( 'pre_option_require_name_email', '__return_true' );

/* ==========================================================================
   4. Privacy — no commenter cookies (keeps the privacy policy's "one thing
      in your browser" claim true; commenters just retype name/email)
   ========================================================================== */

function jt_disable_comment_cookies() {
	remove_action( 'set_comment_cookies', 'wp_set_comment_cookies' );
}
add_action( 'init', 'jt_disable_comment_cookies' );

/* ==========================================================================
   5. Threaded-reply script — core's comment-reply.js moves the form under
      the comment being replied to
   ========================================================================== */

function jt_comment_reply_script() {
	if ( is_singular( 'post' ) && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'jt_comment_reply_script' );
