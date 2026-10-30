<?php
/**
 * Kegiatan komunitas (§3.5): 3–6 foto asli dari tim MEA.
 * Tersembunyi sampai minimal satu foto diisi di Customizer.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

$km_ids = km_kegiatan_ids();
if ( ! $km_ids ) {
	return;
}
?>
<section class="section wrap kegiatan" aria-labelledby="kegiatan-title">
	<h2 id="kegiatan-title">Kegiatan komunitas</h2>
	<ul class="km-gallery">
		<?php foreach ( $km_ids as $km_id ) : ?>
			<li><figure>
				<?php
				echo wp_get_attachment_image(
					$km_id,
					'medium_large',
					false,
					array(
						'loading' => 'lazy',
						'sizes'   => '(max-width: 820px) 50vw, 380px',
					)
				);
				$km_caption = wp_get_attachment_caption( $km_id );
				if ( $km_caption ) :
					?>
					<figcaption><?php echo esc_html( $km_caption ); ?></figcaption>
				<?php endif; ?>
			</figure></li>
		<?php endforeach; ?>
	</ul>
</section>
