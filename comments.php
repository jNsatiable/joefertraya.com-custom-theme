<?php
/**
 * Comments template — list + form for blog posts. Spam-gate fields
 * (honeypot, timestamp, Turnstile) ride the submit_field so they always
 * land inside the form without hook-order gymnastics; validation lives in
 * includes/comments.php. Fields reuse the contact form's .jt-form__field
 * styling. Avatars deliberately off (no Gravatar third-party requests).
 */

if ( post_password_required() ) {
	return;
}

$jt_site_key = get_option( 'jt_turnstile_site_key' );

$jt_gate_fields  = '<p class="jt-form__hp" aria-hidden="true"><label>Leave this field empty<input type="text" name="jt_hp" tabindex="-1" autocomplete="off"></label></p>';
$jt_gate_fields .= '<input type="hidden" name="jt_ts" value="' . esc_attr( time() ) . '">';
if ( $jt_site_key ) {
	$jt_gate_fields .= '<div class="cf-turnstile" data-sitekey="' . esc_attr( $jt_site_key ) . '"></div>';
}
?>

<section class="post-comments" id="comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="post-comments__title">
			<?php
			$jt_count = get_comments_number();
			echo esc_html( 1 === (int) $jt_count ? '1 comment' : $jt_count . ' comments' );
			?>
		</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'avatar_size' => 0,
					'short_ping'  => true,
				)
			);
			?>
		</ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'          => 'Leave a comment',
			'title_reply_before'   => '<h2 id="reply-title" class="post-comments__form-title">',
			'title_reply_after'    => '</h2>',
			'comment_notes_before' => '<p class="post-comments__note">Your email stays private. Comments are held for moderation, so yours will appear once approved.</p>',
			'fields'               => array(
				'author' => '<p class="comment-form-author jt-form__field"><label for="author">Name <span class="jt-form__req">*</span></label><input id="author" name="author" type="text" placeholder="Your name" autocomplete="name" required></p>',
				'email'  => '<p class="comment-form-email jt-form__field"><label for="email">Email <span class="jt-form__req">*</span></label><input id="email" name="email" type="email" placeholder="you@company.com" autocomplete="email" required></p>',
			),
			'comment_field'        => '<p class="comment-form-comment jt-form__field"><label for="comment">Comment <span class="jt-form__req">*</span></label><textarea id="comment" name="comment" rows="5" placeholder="Say hello, share a fix of your own, or tell me what I missed..." required></textarea></p>',
			'class_submit'         => 'jt-btn jt-form__submit',
			'label_submit'         => 'POST COMMENT',
			'submit_field'         => $jt_gate_fields . '<p class="form-submit">%1$s %2$s</p>',
		)
	);
	?>
</section>
