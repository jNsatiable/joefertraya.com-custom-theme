<?php
/**
 * Authenticated outgoing mail (Rentl pattern).
 *
 * wp_mail() falls back to PHP's unauthenticated mail() unless something
 * wires PHPMailer to a real SMTP account — that failure is invisible even
 * with fully correct SPF/DKIM/DMARC DNS, since none of those records matter
 * unless something actually signs through them. Confirmed live 2026-07-18:
 * a test submission landed in the inbox but Gmail showed a "via
 * srv1367.main-hosting.eu" trust tag, meaning neither SPF nor DKIM aligned.
 *
 * Credentials live as wp-config.php constants (JT_SMTP_*), never in this
 * repo. If they're not defined, this file no-ops and mail falls back to
 * WordPress's default behavior — a missing config degrades, it doesn't
 * break the contact form or comment notifications.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jt_smtp_configured() {
	return defined( 'JT_SMTP_HOST' ) && defined( 'JT_SMTP_USER' ) && defined( 'JT_SMTP_PASS' );
}

function jt_phpmailer_init( $phpmailer ) {
	if ( ! jt_smtp_configured() ) {
		return;
	}
	$phpmailer->isSMTP();
	$phpmailer->Host       = JT_SMTP_HOST;
	$phpmailer->Port       = defined( 'JT_SMTP_PORT' ) ? JT_SMTP_PORT : 587;
	$phpmailer->SMTPAuth   = true;
	$phpmailer->Username   = JT_SMTP_USER;
	$phpmailer->Password   = JT_SMTP_PASS;
	$phpmailer->SMTPSecure = 'tls';
}
add_action( 'phpmailer_init', 'jt_phpmailer_init' );

/**
 * The visible From must match the account actually authenticating, or DKIM
 * alignment breaks even with a working SMTP connection (Framework doc §2).
 */
function jt_mail_from( $email ) {
	return jt_smtp_configured() ? JT_SMTP_USER : $email;
}
add_filter( 'wp_mail_from', 'jt_mail_from' );

function jt_mail_from_name( $name ) {
	return jt_smtp_configured() ? 'Joefer Traya' : $name;
}
add_filter( 'wp_mail_from_name', 'jt_mail_from_name' );
