</main>

<footer class="site-footer">
	<div class="container">
		<nav class="footer-nav" aria-label="<?php esc_attr_e( 'Footer', 'joefertraya' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
