<?php
/**
 * Inline search box — used by 404.php and search.php. Hand-built instead
 * of get_search_form() so it matches the site's own .jt-form__field
 * styling rather than WordPress's generic default markup.
 */
?>
<form class="jt-search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="jt-form__field jt-search-form__field">
		<input type="search" name="s" aria-label="<?php esc_attr_e( 'Search the site', 'joefertraya' ); ?>" placeholder="<?php esc_attr_e( 'Search the site…', 'joefertraya' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
	</div>
	<button type="submit" class="jt-btn"><?php esc_html_e( 'Search', 'joefertraya' ); ?></button>
</form>
