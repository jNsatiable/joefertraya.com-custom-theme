<?php
/**
 * Joefer Traya theme setup and asset loading.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JT_THEME_VERSION', '0.2.0' );

function jt_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'joefertraya' ),
			'footer'  => __( 'Footer Menu', 'joefertraya' ),
		)
	);
}
add_action( 'after_setup_theme', 'jt_theme_setup' );

function jt_enqueue_assets() {
	// Google Fonts: the four families from the design tokens. Weights expand as pages need them.
	wp_enqueue_style(
		'jt-fonts',
		'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Raleway:wght@400;600&family=Outfit:wght@400;500&family=Averia+Serif+Libre:wght@500&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'jt-tokens',
		get_template_directory_uri() . '/assets/css/tokens.css',
		array(),
		JT_THEME_VERSION
	);

	wp_enqueue_style(
		'jt-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'jt-tokens' ),
		JT_THEME_VERSION
	);

	if ( is_front_page() ) {
		wp_enqueue_style(
			'jt-home-hero',
			get_template_directory_uri() . '/assets/css/home-hero.css',
			array( 'jt-tokens' ),
			JT_THEME_VERSION
		);

		// No wp_add_inline_script() on this handle — an 'after' inline script
		// silently cancels the defer strategy (WP core refuses to combine them).
		wp_enqueue_script(
			'jt-home-hero',
			get_template_directory_uri() . '/assets/js/home-hero.js',
			array(),
			JT_THEME_VERSION,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'jt_enqueue_assets' );
