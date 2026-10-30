<?php
/**
 * Kartu artikel untuk arsip, pencarian, dan "Baca juga".
 *
 * Argumen: heading (h2|h3, bawaan h2).
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

$km_heading = ( isset( $args['heading'] ) && 'h3' === $args['heading'] ) ? 'h3' : 'h2';
$km_cat     = km_primary_category();
?>
<article <?php post_class( 'card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="card__img">
			<?php the_post_thumbnail( 'km-card', array( 'sizes' => '(max-width: 600px) calc(100vw - 2.5rem), 360px', 'alt' => '' ) ); ?>
		</div>
	<?php endif; ?>
	<div class="card__body">
		<?php if ( $km_cat ) : ?>
			<span class="card__cat"><?php echo esc_html( $km_cat->name ); ?></span>
		<?php endif; ?>
		<<?php echo $km_heading; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></<?php echo $km_heading; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<p class="card__ex"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
		<span class="card__date"><?php echo km_entry_date(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
	</div>
</article>
