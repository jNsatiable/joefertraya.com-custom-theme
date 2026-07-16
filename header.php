<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> id="top">
<?php wp_body_open(); ?>

<header class="site-header">
	<div class="container site-header__inner">
		<a class="site-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Joefer Traya — home', 'joefertraya' ); ?>">
			<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2024/01/joefertraya-icon-gray-bg-150x150.png' ) ); ?>" alt="Joefer Traya logo" width="56" height="56">
		</a>
		<nav class="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'joefertraya' ); ?>">
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
	</div>
</header>

<main class="site-main">
