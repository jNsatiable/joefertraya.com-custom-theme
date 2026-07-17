<?php
/**
 * Joefer Traya theme setup and asset loading.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JT_THEME_VERSION', '0.9.9' );

require_once get_template_directory() . '/includes/migrate-post-2411.php';
require_once get_template_directory() . '/includes/disable-comments.php';
require_once get_template_directory() . '/includes/cleanup-orphan-content.php';
require_once get_template_directory() . '/includes/update-seo-titles.php';
require_once get_template_directory() . '/includes/performance.php';

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

/**
 * Inline the base + page-specific CSS instead of enqueuing as separate
 * <link> requests. PageSpeed measured these as render-blocking requests
 * costing ~430ms on mobile despite each file being under 2.5KB — the
 * bottleneck was round-trip latency per request, not payload size, so
 * Lighthouse's own fix ("deferring or inlining") means eliminating the
 * requests entirely rather than shrinking them further.
 *
 * Font FILES (assets/fonts/*.woff2) stay as separate, cacheable, binary
 * requests — only the small @font-face-declaring stylesheet is inlined
 * alongside the rest. Its `url('../fonts/...')` references are relative
 * to the CSS FILE's own location and would resolve wrong once moved into
 * the HTML document (relative to the page URL instead) — rewritten to
 * absolute template-dir URLs before printing.
 *
 * JT_THEME_VERSION cache-busting no longer applies to these files (an
 * inlined block has no URL to append ?ver= to, and always reflects
 * current file content on every server render) — it still matters for
 * the two enqueued JS files above. Note: a full-page HTTP cache (e.g.
 * WP-Optimize) could still serve stale inlined CSS until its own cache
 * entry is purged/expires, same as it would for any other markup change.
 */
add_action( 'wp_head', 'jt_inline_styles', 5 );

function jt_inline_styles() {
	$base = array( 'tokens.css', 'fonts.css', 'main.css', 'chrome.css' );
	$page = is_front_page() ? 'home-hero.css' : 'pages.css';

	echo '<style id="jt-inline-css">';
	foreach ( array_merge( $base, array( $page ) ) as $file ) {
		$path = get_template_directory() . '/assets/css/' . $file;
		if ( ! file_exists( $path ) ) {
			continue;
		}
		$css = file_get_contents( $path );
		if ( 'fonts.css' === $file ) {
			$css = str_replace( "url('../fonts/", "url('" . esc_url( get_template_directory_uri() ) . '/assets/fonts/', $css );
		}
		echo $css . "\n";
	}
	echo '</style>' . "\n";
}

/**
 * Set data-theme in <head> before any CSS renders — prevents a flash of the
 * wrong theme on load (Rentl pattern). Deliberately a raw wp_head print at
 * priority 1, NOT wp_add_inline_script(): attaching inline JS to a deferred
 * handle silently cancels its defer strategy (Framework gotcha), and this
 * must run before paint anyway.
 */
function jt_theme_bootstrap_script() {
	echo '<script>!function(){var s;try{s=localStorage.getItem("jt-theme")}catch(e){}if("dark"!==s&&"light"!==s)s=window.matchMedia("(prefers-color-scheme:dark)").matches?"dark":"light";document.documentElement.setAttribute("data-theme",s)}();</script>' . "\n";
}
add_action( 'wp_head', 'jt_theme_bootstrap_script', 1 );

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
