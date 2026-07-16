<?php
/**
 * One-time cleanup: trash content orphaned by the Elementor/Ultimate Member
 * removal (approved list, 2026-07-16). Uses wp_trash_post(), not deletion —
 * everything is recoverable from wp-admin Trash for 30 days.
 *
 * The UM form/directory entries can't be reached from wp-admin at all
 * anymore (their post types are no longer registered with the plugin off),
 * which is why this runs as code. Same flag-guarded init pattern as the
 * post-2411 migration.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jt_trash_orphan_content_once() {
	if ( get_option( 'jt_orphans_trashed' ) ) {
		return;
	}

	$orphan_ids = array(
		11,   // Site Under Construction (old LightStart maintenance page)
		2641, // About-new (abandoned draft)
		2645, // Member Portal
		2652, // Member Login
		2664, // Login Form
		2681, // UM Default Registration form
		2682, // UM Default Login form
		2683, // UM Default Profile form
		2684, // UM Members directory
		2685, // User
		2686, // Login
		2687, // Register
		2688, // Members
		2689, // Logout
		2690, // Account
		2691, // Password Reset
	);

	$results = array();
	foreach ( $orphan_ids as $id ) {
		if ( ! get_post( $id ) ) {
			$results[ $id ] = 'not-found';
			continue;
		}
		$results[ $id ] = wp_trash_post( $id ) ? 'trashed' : 'failed';
	}

	update_option( 'jt_orphans_trashed', wp_json_encode( $results ) );
}
add_action( 'init', 'jt_trash_orphan_content_once' );
