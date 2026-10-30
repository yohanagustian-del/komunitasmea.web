<?php
/**
 * Komentar.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments" aria-labelledby="comments-title">
	<?php if ( have_comments() ) : ?>
		<h2 id="comments-title"><?php echo esc_html( get_comments_number() ); ?> komentar</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 40,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php else : ?>
		<h2 id="comments-title" class="screen-reader-text">Komentar</h2>
	<?php endif; ?>
	<?php
	comment_form(
		array(
			'title_reply'  => 'Tulis komentar',
			'label_submit' => 'Kirim komentar',
		)
	);
	?>
</section>
