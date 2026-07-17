<?php
/**
 * Contact page — hand-built form (replaced the JotForm embed, 2026-07-17).
 * Backend (settings, Submissions CPT, Turnstile, handler) lives in
 * includes/contact-form.php. Intro copy and links carried over from the
 * original JotForm verbatim.
 */

get_header();

$jt_site_key = get_option( 'jt_turnstile_site_key' );
$jt_sent     = isset( $_GET['jt_sent'] );
$jt_error    = isset( $_GET['jt_error'] ) ? sanitize_key( wp_unslash( $_GET['jt_error'] ) ) : '';

$jt_error_messages = array(
	'expired' => 'The form expired. Please try sending again.',
	'captcha' => 'CAPTCHA verification failed. Please try again.',
	'fields'  => 'Please fill in your name, a valid email, and a message.',
	'send'    => 'Something went wrong sending your message. Please try again, or reach me on Upwork instead.',
);
?>

<section class="contact-hero">
	<div class="container">
		<h1 class="page-title">Contact Me</h1>
		<p class="contact-hero__intro">Got questions, a project, or simply want to say hello? Drop J a message and he'll get back to you in a heartbeat. For more urgent matters, here's a link to <a href="https://calendar.app.google/69rBvEiEAxNCw7NC6" target="_blank" rel="noopener">his Calendar</a>. Additionally, here's a link to his <a href="https://www.upwork.com/freelancers/~0142984b3c47f365d6" target="_blank" rel="noopener">Upwork profile</a> if you're there, too.</p>
	</div>
</section>

<section class="page-section contact-section" id="contact-form">
	<div class="container">
		<?php if ( $jt_sent ) : ?>
			<div class="jt-notice">Message received! I'll get back to you within a day &#x1F642;</div>
		<?php elseif ( $jt_error && isset( $jt_error_messages[ $jt_error ] ) ) : ?>
			<div class="jt-notice jt-notice--error"><?php echo esc_html( $jt_error_messages[ $jt_error ] ); ?></div>
		<?php endif; ?>

		<?php if ( ! $jt_sent ) : ?>
		<form class="jt-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="jt_contact_submit">
			<?php wp_nonce_field( 'jt_contact_submit', 'jt_contact_nonce' ); ?>
			<input type="hidden" name="jt_ts" value="<?php echo esc_attr( time() ); ?>">
			<p class="jt-form__hp" aria-hidden="true">
				<label>Leave this field empty<input type="text" name="jt_hp" tabindex="-1" autocomplete="off"></label>
			</p>

			<div class="jt-form__grid">
				<p class="jt-form__field">
					<label for="jt-name">Name <span class="jt-form__req">*</span></label>
					<input type="text" id="jt-name" name="jt_name" autocomplete="name" placeholder="Your name" required>
				</p>
				<p class="jt-form__field">
					<label for="jt-email">Email <span class="jt-form__req">*</span></label>
					<input type="email" id="jt-email" name="jt_email" autocomplete="email" placeholder="you@company.com" required>
				</p>
			</div>

			<div class="jt-form__grid">
				<p class="jt-form__field">
					<label for="jt-website">Your Website <span class="jt-form__opt">(optional)</span></label>
					<input type="text" id="jt-website" name="jt_website" inputmode="url" autocomplete="url" placeholder="www.example.com">
					<span class="jt-form__hint">So I can get in touch using your preferred channels.</span>
				</p>
				<p class="jt-form__field">
					<label for="jt-service">What can I help with?</label>
					<select id="jt-service" name="jt_service">
						<option value="">Select a service (optional)</option>
						<option>Administrative Assistance</option>
						<option>Tech Support</option>
						<option>Data Entry and Analytics</option>
						<option>Web Development and Automations</option>
						<option>Photo Editing and Retouching</option>
						<option>Something else</option>
					</select>
				</p>
			</div>

			<p class="jt-form__field">
				<label for="jt-message">Message <span class="jt-form__req">*</span></label>
				<textarea id="jt-message" name="jt_message" rows="7" placeholder="Tell me about your project, timeline, and anything else I should know..." required></textarea>
			</p>

			<?php if ( $jt_site_key ) : ?>
				<div class="cf-turnstile" data-sitekey="<?php echo esc_attr( $jt_site_key ); ?>"></div>
			<?php endif; ?>

			<p><button type="submit" class="jt-btn jt-form__submit">SEND MESSAGE</button></p>
			<p class="jt-form__note">Goes straight to my inbox &mdash; usually answered within 24 hours.</p>
		</form>
		<?php endif; ?>
	</div>
</section>

<script>
(function () {
	var form = document.querySelector('.jt-form');
	if (!form) return;
	function track(name) {
		if (typeof gtag === 'function') gtag('event', name, { transport_type: 'beacon' });
	}
	var started = false;
	form.addEventListener('focusin', function () {
		if (!started) { started = true; track('jt_form_start'); }
	});
	form.addEventListener('submit', function () { track('jt_form_submit'); });
})();
</script>

<?php
get_footer();
