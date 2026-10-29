<?php
/**
 * Tentang komunitas (§3.2). Copy final diisi di Customizer; selama kosong,
 * draf bawaan tampil dan admin melihat tanda "draf".
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

$km_about = trim( (string) get_theme_mod( 'km_about_html', '' ) );
?>
<section class="section wrap about" aria-labelledby="tentang-title">
	<h2 id="tentang-title">Komunitas tempat seller dan kreator naik kelas bareng</h2>
	<div class="about__body">
		<?php if ( '' !== $km_about ) : ?>
			<?php echo wp_kses_post( wpautop( $km_about ) ); ?>
		<?php else : ?>
			<p>Komunitas MEA dibangun oleh tim MEA sejak 2020 di Bandung. Isinya orang-orang yang sama-sama jualan dan bikin konten di Shopee dan TikTok Shop: ada yang baru mulai dari ruang tamu, ada yang tokonya sudah dikelola tim.</p>
			<p>Di sini kamu tidak dipaksa lompat. Pilih jalur, kerjakan satu anak tangga, lalu naik ke berikutnya saat siap. <?php echo km_placeholder( '[draf copy — perlu persetujuan Yohan]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<?php endif; ?>
		<p class="facts"><strong>12.000+</strong> kreator <span aria-hidden="true">/</span> <strong>3.000+</strong> seller <span aria-hidden="true">/</span> sejak <strong>2020</strong></p>
	</div>
</section>
