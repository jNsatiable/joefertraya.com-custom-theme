<?php
/**
 * Joefer Traya theme setup and asset loading.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JT_THEME_VERSION', '0.4.8' );

require_once get_template_directory() . '/includes/migrate-post-2411.php';
require_once get_template_directory() . '/includes/disable-comments.php';
require_once get_template_directory() . '/includes/cleanup-orphan-content.php';

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
		'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Raleway:wght@400;600&family=Outfit:wght@400;500&family=Averia+Serif+Libre:wght@500;700&display=swap',
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

	wp_enqueue_style(
		'jt-chrome',
		get_template_directory_uri() . '/assets/css/chrome.css',
		array( 'jt-tokens' ),
		JT_THEME_VERSION
	);

	// Same rule as jt-home-hero: never wp_add_inline_script() onto this
	// handle, or the defer strategy silently drops.
	wp_enqueue_script(
		'jt-chrome',
		get_template_directory_uri() . '/assets/js/chrome.js',
		array(),
		JT_THEME_VERSION,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
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
	} else {
		wp_enqueue_style(
			'jt-pages',
			get_template_directory_uri() . '/assets/css/pages.css',
			array( 'jt-tokens' ),
			JT_THEME_VERSION
		);
	}
}
add_action( 'wp_enqueue_scripts', 'jt_enqueue_assets' );

/**
 * Force this theme's page templates for the replicated pages.
 *
 * The pages carry stale _wp_page_template meta pointing at the Elementor
 * plugin's own templates (elementor_header_footer etc.), and a page
 * template set in meta outranks page-{slug}.php in the hierarchy — so
 * without this, Elementor (still active) hijacks the whole document.
 * front-page.php is unaffected (it outranks page templates), which is why
 * Home worked while About/Portfolio/Contact didn't.
 */
function jt_force_page_templates( $template ) {
	$map = array(
		'about'        => 'page-about.php',
		'portfolio'    => 'page-portfolio.php',
		'contact-form' => 'page-contact-form.php',
	);
	if ( is_page() ) {
		$slug = get_post_field( 'post_name', get_queried_object_id() );
		if ( isset( $map[ $slug ] ) ) {
			$file = get_template_directory() . '/' . $map[ $slug ];
			if ( file_exists( $file ) ) {
				return $file;
			}
		}
	}
	return $template;
}
add_filter( 'template_include', 'jt_force_page_templates', 99 );

/**
 * Fallback for the primary menu until one is assigned in wp-admin —
 * mirrors the live site's nav so the header never renders empty.
 */
function jt_primary_menu_fallback() {
	$items = array(
		array( home_url( '/' ), 'Home', is_front_page() ),
		array( home_url( '/about/' ), 'About', is_page( 'about' ) ),
		array( home_url( '/portfolio/' ), 'Portfolio', is_page( 'portfolio' ) ),
		array( home_url( '/category/blog/' ), 'Blog', is_category( 'blog' ) || is_singular( 'post' ) ),
	);
	echo '<ul class="site-nav__list">';
	foreach ( $items as $item ) {
		list( $url, $label, $current ) = $item;
		$aria = $current ? ' aria-current="page"' : '';
		echo '<li><a href="' . esc_url( $url ) . '"' . $aria . '>' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

/**
 * Social icon row (Upwork, Instagram, Flickr, GitHub) — used in the footer
 * and the Portfolio hero. Inline SVGs, no icon-font dependency.
 */
function jt_social_icons( $class = '' ) {
	$upwork_logo = home_url( '/wp-content/uploads/2024/01/upwork-logo-white-font.svg' );
	?>
	<div class="jt-social <?php echo esc_attr( $class ); ?>">
		<a href="https://www.upwork.com/freelancers/~0142984b3c47f365d6" target="_blank" rel="noopener" aria-label="Upwork profile">
			<img src="<?php echo esc_url( $upwork_logo ); ?>" alt="" width="60" height="18">
		</a>
		<a href="https://www.instagram.com/retouch_by_j/" target="_blank" rel="noopener" aria-label="Instagram">
			<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.2" cy="6.8" r="1.2" fill="currentColor" stroke="none"></circle></svg>
		</a>
		<a href="https://www.flickr.com/photos/164769429@N03/" target="_blank" rel="noopener" aria-label="Flickr">
			<svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true"><circle cx="7" cy="12" r="4.5"></circle><circle cx="17" cy="12" r="4.5"></circle></svg>
		</a>
		<a href="https://github.com/jNsatiable" target="_blank" rel="noopener" aria-label="GitHub">
			<svg viewBox="0 0 16 16" width="22" height="22" fill="currentColor" aria-hidden="true"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8z"></path></svg>
		</a>
	</div>
	<?php
}
