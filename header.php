<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> id="top">
<?php wp_body_open(); ?>

<a href="#main" class="skip-link"><?php esc_html_e( 'Skip to content', 'joefertraya' ); ?></a>

<header class="site-header" role="banner">
	<div class="container">
		<div class="site-header__bar">
			<a class="site-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Joefer Traya — home', 'joefertraya' ); ?>">
				<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2024/01/joefertraya-icon-gray-bg-150x150.png' ) ); ?>" alt="Joefer Traya logo" width="40" height="40">
			</a>
			<nav class="site-nav" id="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'joefertraya' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'site-nav__list',
						'fallback_cb'    => 'jt_primary_menu_fallback',
					)
				);
				?>
			</nav>
			<a class="jt-btn site-header__cta" href="<?php echo esc_url( home_url( '/contact-form/' ) ); ?>">Get in touch</a>
			<button
				class="site-header__hamburger"
				type="button"
				aria-label="<?php esc_attr_e( 'Open navigation', 'joefertraya' ); ?>"
				aria-expanded="false"
				aria-controls="site-nav"
			>
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false">
					<line x1="3" y1="6" x2="21" y2="6"></line>
					<line x1="3" y1="12" x2="21" y2="12"></line>
					<line x1="3" y1="18" x2="21" y2="18"></line>
				</svg>
			</button>
		</div>
	</div>
</header>

<main class="site-main" id="main" tabindex="-1">
