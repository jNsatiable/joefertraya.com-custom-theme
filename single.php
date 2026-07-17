<?php
/**
 * Single post. Existing posts were built with Elementor (still active as a
 * plugin), so the_content() renders their layout — including their own H1 —
 * via Elementor's frontend. Only print a title for non-Elementor posts.
 */

get_header();
?>

<article <?php post_class( 'page-section post-article' ); ?>>
	<div class="container post-article__inner">
		<?php
		while ( have_posts() ) {
			the_post();
			$is_elementor = 'builder' === get_post_meta( get_the_ID(), '_elementor_edit_mode', true );
			if ( ! $is_elementor ) {
				echo '<h1 class="page-title">' . esc_html( get_the_title() ) . '</h1>';
			}
			the_content();
			comments_template();
		}
		?>
	</div>
</article>

<?php
get_footer();
