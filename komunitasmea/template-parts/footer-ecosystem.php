<?php
/**
 * Fat footer: Komunitas MEA, Untuk seller, Untuk kreator, Ekosistem MEA Grup.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

$km_contact = trim( (string) get_theme_mod( 'km_footer_contact', '' ) );
$km_privacy = km_privacy_url();
?>
<footer class="site-footer">
	<div class="wrap fcols">
		<div>
			<p class="fbrand"><i aria-hidden="true"></i>Komunitas MEA</p>
			<p>Komunitas belajar seller dan kreator affiliate Shopee dan TikTok Shop. Dikelola oleh PT MEA Digital Marketing.</p>
			<?php km_footer_menu( 'footer-komunitas' ); ?>
			<?php if ( '' !== $km_contact ) : ?>
				<p class="fcontact"><?php echo wp_kses_post( $km_contact ); ?></p>
			<?php elseif ( km_placeholder() ) : ?>
				<p class="fcontact">Kontak: <?php echo km_placeholder(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
		</div>
		<div>
			<h2>Untuk seller</h2>
			<?php km_footer_menu( 'footer-seller' ); ?>
		</div>
		<div>
			<h2>Untuk kreator</h2>
			<?php km_footer_menu( 'footer-kreator' ); ?>
		</div>
		<div>
			<h2>Ekosistem MEA Grup</h2>
			<?php km_footer_menu( 'footer-ekosistem' ); ?>
		</div>
	</div>
	<div class="wrap fbot">
		<span>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> PT MEA Digital Marketing</span>
		<?php if ( $km_privacy ) : ?>
			<a href="<?php echo esc_url( $km_privacy ); ?>">Kebijakan privasi</a>
		<?php endif; ?>
	</div>
</footer>
