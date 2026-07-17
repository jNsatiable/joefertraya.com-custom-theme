<?php
/**
 * Hand-built contact form backend — replaces the JotForm embed (2026-07-17).
 *
 * Four pieces: a small settings page for the Turnstile keys (Rentl pattern —
 * keys live in the options table, never in this public repo), a private
 * Submissions post type so no message is ever lost to a spam-foldered email,
 * the Turnstile script enqueue, and the admin-post submission handler.
 *
 * Spam defense is layered: honeypot + time-trap always run (silent success,
 * so bots learn nothing); Cloudflare Turnstile is enforced only once a secret
 * key is configured in Settings > JT Theme — until then the form works with
 * the invisible checks alone, and a Cloudflare outage degrades rather than
 * bricks the form.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==========================================================================
   1. Settings — Turnstile keys (Settings > JT Theme)
   ========================================================================== */

function jt_add_settings_page() {
	add_options_page( 'JT Theme Settings', 'JT Theme', 'manage_options', 'jt-theme', 'jt_render_settings_page' );
}
add_action( 'admin_menu', 'jt_add_settings_page' );

function jt_register_contact_settings() {
	register_setting(
		'jt_settings_group',
		'jt_turnstile_site_key',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);
	register_setting(
		'jt_settings_group',
		'jt_turnstile_secret_key',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);

	add_settings_section(
		'jt_contact_form',
		'Contact form',
		function () {
			echo '<p>Cloudflare Turnstile keys for the contact form. Leave both blank to run with the invisible honeypot and timing checks only.</p>';
		},
		'jt-theme'
	);

	add_settings_field(
		'jt_turnstile_site_key',
		'Turnstile Site Key',
		'jt_settings_field_text',
		'jt-theme',
		'jt_contact_form',
		array(
			'name' => 'jt_turnstile_site_key',
			'type' => 'text',
			'desc' => 'From your Cloudflare Turnstile widget. Public — rendered into the page.',
		)
	);
	add_settings_field(
		'jt_turnstile_secret_key',
		'Turnstile Secret Key',
		'jt_settings_field_text',
		'jt-theme',
		'jt_contact_form',
		array(
			'name' => 'jt_turnstile_secret_key',
			'type' => 'password',
			'desc' => 'Kept server-side, used to verify the widget response with Cloudflare.',
		)
	);
}
add_action( 'admin_init', 'jt_register_contact_settings' );

function jt_settings_field_text( $args ) {
	$value = get_option( $args['name'], '' );
	printf(
		'<input type="%s" name="%s" value="%s" class="regular-text" autocomplete="off">',
		esc_attr( $args['type'] ),
		esc_attr( $args['name'] ),
		esc_attr( $value )
	);
	if ( ! empty( $args['desc'] ) ) {
		printf( '<p class="description">%s</p>', esc_html( $args['desc'] ) );
	}
}

function jt_render_settings_page() {
	?>
	<div class="wrap">
		<h1>JT Theme Settings</h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'jt_settings_group' );
			do_settings_sections( 'jt-theme' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/* ==========================================================================
   2. Submissions post type — every message stored in wp-admin, so a
      spam-foldered notification email never loses the actual message.
   ========================================================================== */

function jt_register_submission_cpt() {
	register_post_type(
		'jt_submission',
		array(
			'labels'              => array(
				'name'          => 'Submissions',
				'singular_name' => 'Submission',
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-email-alt',
			'menu_position'       => 25,
			'supports'            => array( 'title', 'editor' ),
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_in_rest'        => false,
			// Submissions arrive via the form handler only — no manual "Add New".
			'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'        => true,
		)
	);
}
add_action( 'init', 'jt_register_submission_cpt' );

/* ==========================================================================
   3. Turnstile widget script — contact page only, and only once a site key
      is configured. External script, so no JT_THEME_VERSION query arg.
   ========================================================================== */

function jt_contact_scripts() {
	if ( ! is_page( 'contact-form' ) || ! get_option( 'jt_turnstile_site_key' ) ) {
		return;
	}
	wp_enqueue_script(
		'cf-turnstile',
		'https://challenges.cloudflare.com/turnstile/v0/api.js',
		array(),
		null,
		array(
			'strategy'  => 'async',
			'in_footer' => true,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'jt_contact_scripts' );

/* ==========================================================================
   4. Submission handler
   ========================================================================== */

function jt_contact_redirect( $args ) {
	wp_safe_redirect( home_url( '/contact-form/' ) . $args . '#contact-form' );
	exit;
}

/**
 * Verify a Cloudflare Turnstile response token server-side (Rentl pattern).
 */
function jt_verify_turnstile( $token, $secret ) {
	$response = wp_remote_post(
		'https://challenges.cloudflare.com/turnstile/v0/siteverify',
		array(
			'timeout' => 10,
			'body'    => array(
				'secret'   => $secret,
				'response' => $token,
				'remoteip' => sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) ),
			),
		)
	);
	if ( is_wp_error( $response ) ) {
		return false;
	}
	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	return ! empty( $data['success'] );
}

function jt_handle_contact_submit() {
	if (
		! isset( $_POST['jt_contact_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jt_contact_nonce'] ) ), 'jt_contact_submit' )
	) {
		jt_contact_redirect( '?jt_error=expired' );
	}

	// Honeypot + time-trap: bots get a silent "success" so they learn nothing.
	$ts = absint( $_POST['jt_ts'] ?? 0 );
	if ( ! empty( $_POST['jt_hp'] ) || ( $ts && ( time() - $ts ) < 3 ) ) {
		jt_contact_redirect( '?jt_sent=1' );
	}

	// Turnstile — enforced only once a secret is configured. Unlike the
	// honeypot, a real person can legitimately fail this (expired widget,
	// network hiccup), so it gets a visible error instead of silence.
	$turnstile_secret = get_option( 'jt_turnstile_secret_key' );
	if ( $turnstile_secret ) {
		$turnstile_token = sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ?? '' ) );
		if ( ! $turnstile_token || ! jt_verify_turnstile( $turnstile_token, $turnstile_secret ) ) {
			jt_contact_redirect( '?jt_error=captcha' );
		}
	}

	$name    = sanitize_text_field( wp_unslash( $_POST['jt_name'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['jt_email'] ?? '' ) );
	$website = esc_url_raw( wp_unslash( $_POST['jt_website'] ?? '' ) );
	$service = sanitize_text_field( wp_unslash( $_POST['jt_service'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['jt_message'] ?? '' ) );

	if ( '' === $name || ! is_email( $email ) || '' === trim( $message ) ) {
		jt_contact_redirect( '?jt_error=fields' );
	}

	$body = implode(
		"\n",
		array(
			'Name: ' . $name,
			'Email: ' . $email,
			'Website: ' . ( $website ? $website : '—' ),
			'Service: ' . ( $service ? $service : '—' ),
			'',
			$message,
		)
	);

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'jt_submission',
			'post_status'  => 'private',
			'post_title'   => wp_slash( $name . ' — ' . $email ),
			'post_content' => wp_slash( $body ),
		)
	);

	$sent = wp_mail(
		get_option( 'admin_email' ),
		'New message from ' . $name . ' — joefertraya.com',
		$body,
		array( 'Reply-To: ' . $name . ' <' . $email . '>' )
	);

	// Stored OR mailed counts as received; only total failure surfaces an error.
	if ( ! $post_id && ! $sent ) {
		jt_contact_redirect( '?jt_error=send' );
	}
	jt_contact_redirect( '?jt_sent=1' );
}
add_action( 'admin_post_jt_contact_submit', 'jt_handle_contact_submit' );
add_action( 'admin_post_nopriv_jt_contact_submit', 'jt_handle_contact_submit' );
