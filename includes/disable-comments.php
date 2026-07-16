<?php
/**
 * Site-wide comments shutdown — theme-native replacement for the WPCode
 * "Completely Disable Comments" snippet that lived unversioned in the DB.
 *
 * The templates never render a comment form, but that alone doesn't close
 * anything: bots POST straight to wp-comments-post.php and the REST comment
 * endpoints without touching a template. Closing comments_open/pings_open
 * makes core itself reject those submissions ("comments are closed"), which
 * is what actually shuts the side doors.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Core rejection point: submissions via wp-comments-post.php and the REST
// API both consult these before accepting anything.
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );

// Never surface existing comments, whatever a template asks for.
add_filter( 'comments_array', '__return_empty_array', 10 );

function jt_remove_comment_support() {
	foreach ( get_post_types() as $post_type ) {
		if ( post_type_supports( $post_type, 'comments' ) ) {
			remove_post_type_support( $post_type, 'comments' );
			remove_post_type_support( $post_type, 'trackbacks' );
		}
	}
}
add_action( 'init', 'jt_remove_comment_support', 100 );

function jt_remove_comment_admin_menu() {
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'jt_remove_comment_admin_menu' );

function jt_redirect_comment_admin_pages() {
	global $pagenow;
	if ( 'edit-comments.php' === $pagenow || 'options-discussion.php' === $pagenow ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
}
add_action( 'admin_init', 'jt_redirect_comment_admin_pages' );

function jt_remove_comment_dashboard_widget() {
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}
add_action( 'wp_dashboard_setup', 'jt_remove_comment_dashboard_widget' );

function jt_remove_comment_admin_bar_node( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'jt_remove_comment_admin_bar_node', 999 );
