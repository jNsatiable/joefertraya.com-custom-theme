<?php
/**
 * Privacy Policy — hardcoded copy per the site content strategy.
 * Covers what the site actually does: contact form (DB + email),
 * Google Analytics, Cloudflare Turnstile, security IP logging,
 * dark-mode localStorage, Flickr embeds. Page created by
 * includes/seed-privacy-page.php.
 */

get_header();
?>

<section class="page-hero">
	<div class="container">
		<h1 class="page-title">Privacy Policy</h1>
		<p class="page-hero__intro">This site is run by me, Joefer Traya. I keep things simple, and that includes how I handle your data. Here's the full picture.</p>
	</div>
</section>

<section class="page-section">
	<div class="container">
		<div class="privacy-content">
			<p class="privacy-content__updated">Last updated: July 17, 2026</p>

			<h2>What I collect, and why</h2>
			<p><em>The contact form.</em> If you send me a message, I receive what you typed: your name, email address, your website if you chose to share it, the service you selected, and the message itself. It's stored on this site and delivered to my inbox so I can reply. That's its only use. I don't sell it, share it, or add you to any mailing list.</p>
			<p><em>Blog comments.</em> If you comment on a post, I receive the name and email you provide along with the comment itself, and WordPress records the IP address it was sent from. Comments are held for moderation before appearing, your email is never displayed publicly, and the same only-use rule applies: it exists so I can manage the discussion, nothing more.</p>
			<p><em>Analytics.</em> I use Google Analytics to understand how visitors find and use the site: which pages get read, roughly where visitors are from, and what devices they use. This relies on cookies and similar identifiers set by Google. The data reaches me only in aggregate; I can't identify you personally from it. You can learn more in <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Google's Privacy Policy</a>, or block analytics entirely with <a href="https://tools.google.com/dlpage/gaoptout" target="_blank" rel="noopener">Google's opt-out browser add-on</a>.</p>
			<p><em>Spam and security.</em> The contact form is protected by Cloudflare Turnstile, which runs a quick technical check in your browser to tell humans from bots (see <a href="https://www.cloudflare.com/privacypolicy/" target="_blank" rel="noopener">Cloudflare's Privacy Policy</a>). The site also runs security software that logs IP addresses of requests to detect and block malicious activity.</p>

			<h2>Cookies and local storage</h2>
			<p>Beyond the Google Analytics cookies mentioned above, this site stores exactly one thing in your browser: your light/dark mode preference. It never leaves your device and identifies nothing about you.</p>

			<h2>Embedded content</h2>
			<p>The Portfolio page displays photo galleries embedded from Flickr. When you view that page, Flickr may collect data as if you had visited flickr.com directly, per <a href="https://www.flickr.com/help/privacy" target="_blank" rel="noopener">Flickr's Privacy Policy</a>.</p>

			<h2>How long I keep things</h2>
			<p>Messages sent through the contact form are kept until I no longer need them, and you can ask me to delete yours at any time. Analytics data is retained under Google's standard retention settings.</p>

			<h2>Your rights</h2>
			<p>Want to know what I have from you, or want it deleted? Email me at <a href="mailto:j@joefertraya.com">j@joefertraya.com</a> and I'll sort it out. No forms, no runaround.</p>

			<h2>Changes</h2>
			<p>If how this site handles data changes, this page changes with it, along with the date at the top.</p>
		</div>
	</div>
</section>

<?php
get_footer();
