<?php
/**
 * Testimoni (§3.7): hanya testimoni asli yang sudah diverifikasi.
 * Tersembunyi sampai minimal satu testimoni diisi di Customizer.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

$km_items = km_testimonials();
if ( ! $km_items ) {
	return;
}
?>
<section class="section wrap testimoni" aria-labelledby="testimoni-title">
	<h2 id="testimoni-title">Cerita member</h2>
	<ul class="testi">
		<?php foreach ( $km_items as $km_item ) : ?>
			<li><blockquote>
				<p><?php echo esc_html( $km_item['quote'] ); ?></p>
				<?php if ( $km_item['name'] || $km_item['role'] ) : ?>
					<footer>
						<?php if ( $km_item['name'] ) : ?>
							<cite><?php echo esc_html( $km_item['name'] ); ?></cite>
						<?php endif; ?>
						<?php if ( $km_item['role'] ) : ?>
							<br><?php echo esc_html( $km_item['role'] ); ?>
						<?php endif; ?>
					</footer>
				<?php endif; ?>
			</blockquote></li>
		<?php endforeach; ?>
	</ul>
</section>
