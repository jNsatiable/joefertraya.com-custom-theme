<?php
/**
 * One-time seeder (2026-07-17): create the Privacy Policy page so
 * /privacy-policy/ resolves. Content is hardcoded in
 * page-privacy-policy.php (site content strategy — templates never call
 * the_content()), so the WP page itself is just a slug-holder. Also
 * registers the page as WordPress's designated privacy page
 * (Settings > Privacy), which adds the standard link on wp-login.php.
 * Same flag-guarded init pattern as the other one-time seeders.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jt_seed_privacy_page_once() {
	if ( get_option( 'jt_privacy_page_created' ) ) {
		return;
	}

	$existing = get_page_by_path( 'privacy-policy' );
	if ( $existing ) {
		$page_id = $existing->ID;
	} else {
		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'Privacy Policy',
				'post_name'    => 'privacy-policy',
				'post_content' => '',
			)
		);
	}

	if ( $page_id && ! is_wp_error( $page_id ) ) {
		update_option( 'wp_page_for_privacy_policy', $page_id );
		update_option( 'jt_privacy_page_created', gmdate( 'Y-m-d' ) );
	}
}
add_action( 'init', 'jt_seed_privacy_page_once' );
