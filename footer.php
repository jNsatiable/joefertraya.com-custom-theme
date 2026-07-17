</main>

<footer class="site-footer">
	<div class="container">
		<div class="site-footer__grid">

			<div class="site-footer__brand">
				<a class="site-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'JoeferTraya.com — home', 'joefertraya' ); ?>">
					<span class="site-footer__lockup" aria-hidden="true"><span class="site-footer__lockup-first">Joefer</span><span class="site-footer__lockup-last">Traya</span></span>
				</a>
				<p class="site-footer__tagline"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
				<?php jt_social_icons( 'site-footer__social' ); ?>
			</div>

			<nav class="site-footer__col" aria-label="<?php esc_attr_e( 'Explore', 'joefertraya' ); ?>">
				<p class="site-footer__heading">Explore</p>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
					<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a></li>
					<li><a href="<?php echo esc_url( home_url( '/portfolio/' ) ); ?>">Portfolio</a></li>
					<li><a href="<?php echo esc_url( home_url( '/category/blog/' ) ); ?>">Blog</a></li>
				</ul>
			</nav>

			<nav class="site-footer__col" aria-label="<?php esc_attr_e( 'Contact', 'joefertraya' ); ?>">
				<p class="site-footer__heading">Contact</p>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/contact-form/' ) ); ?>">Get in touch</a></li>
					<li><a href="https://www.upwork.com/freelancers/~0142984b3c47f365d6" target="_blank" rel="noopener">Upwork</a></li>
					<li><a href="https://www.instagram.com/retouch_by_j/" target="_blank" rel="noopener">Instagram</a></li>
				</ul>
			</nav>

		</div>

		<div class="site-footer__bottom">
			<p class="site-footer__copyright">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> JoeferTraya.com &middot; <a class="site-footer__privacy" href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">Privacy Policy</a></p>
			<a class="site-footer__top" href="#top">
				<?php esc_html_e( 'Back to top', 'joefertraya' ); ?>
				<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M8 13l4-4 4 4"></path></svg>
			</a>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
