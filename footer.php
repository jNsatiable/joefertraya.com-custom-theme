</main>

<footer class="site-footer">
	<div class="container site-footer__inner">
		<a class="site-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2024/01/joefertraya-icon-fullname-split-colors.png' ) ); ?>" alt="JoeferTraya.com" width="220" height="46">
		</a>
		<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer', 'joefertraya' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
			<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a>
			<a href="<?php echo esc_url( home_url( '/portfolio/' ) ); ?>">Portfolio</a>
			<a href="<?php echo esc_url( home_url( '/contact-form/' ) ); ?>">Contact</a>
			<a href="<?php echo esc_url( home_url( '/category/blog/' ) ); ?>">Blog</a>
		</nav>
		<?php jt_social_icons( 'site-footer__social' ); ?>
		<h6 class="site-footer__tagline"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></h6>
		<a class="site-footer__top" href="#top" aria-label="<?php esc_attr_e( 'Back to top', 'joefertraya' ); ?>">
			<svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M8 13l4-4 4 4"></path></svg>
		</a>
		<p class="site-footer__copyright">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> JoeferTraya.com</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
