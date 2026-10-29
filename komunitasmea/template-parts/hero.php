<?php
/**
 * Hero homepage: H1 teks + kartu ilustrasi dengan 6 ruangan yang bisa diklik.
 *
 * Poster (640×986) adalah elemen LCP. Video baru disisipkan hero.js setelah
 * load + idle, hanya di layar ≥ 821px dan tanpa prefers-reduced-motion.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

$km_hero = get_template_directory_uri() . '/assets/hero/';

/*
 * Posisi dalam persen terhadap kartu: left, top, width, height.
 * Ruangan oranye = seller, teal = kreator.
 */
$km_rooms = array(
	array( 'gudang', 'meagency', 's', array( 0, 0, 50, 32 ), 'Dikelola', 'seller_dikelola' ),
	array( 'toko', 'ordal', 's', array( 0, 33, 33, 33 ), 'Didampingi', 'seller_didampingi' ),
	array( 'rumah', 'skilskul', 's', array( 0, 67, 50, 33 ), 'Belajar', 'seller_belajar' ),
	array( 'studio', 'meamcn', 'k', array( 67, 0, 33, 38 ), 'Gabung', 'kreator_gabung' ),
	array( 'kos', 'affiliateakademi', 'k', array( 67, 39, 33, 28 ), 'Mulai', 'kreator_mulai' ),
	array( 'warung', 'meago', 'k', array( 67, 68, 33, 32 ), 'Kreator kuliner', 'kreator_meago' ),
);
?>
<section class="hero wrap" aria-labelledby="hero-title">
	<div class="hero__copy">
		<h1 id="hero-title">Rumah bagi <span>12.000+</span> kreator dan <span>3.000+</span> seller</h1>
		<p class="lede">Mulai dari ruang tamu atau kamar kos, naik sampai toko yang dikelola tim atau studio live sendiri. Pilih jalurmu.</p>
		<nav class="paths" aria-label="Pilih jalur">
			<a class="path path--s" href="#tangga-seller" data-f="s"><strong>Saya Seller</strong><span>Belajar, didampingi, dikelola</span></a>
			<a class="path path--k" href="#tangga-kreator" data-f="k"><strong>Saya Affiliator</strong><span>Mulai, lalu gabung MCN MEA</span></a>
		</nav>
		<p class="hint">Klik ruangan di gambar untuk langsung ke program yang sesuai.</p>
	</div>
	<div class="frame" id="hero-frame" data-webm="<?php echo esc_url( $km_hero . 'loop.webm' ); ?>" data-mp4="<?php echo esc_url( $km_hero . 'loop.mp4' ); ?>">
		<picture>
			<source media="(min-width: 821px)" type="image/webp" srcset="<?php echo esc_url( $km_hero . 'poster.webp' ); ?> 640w, <?php echo esc_url( $km_hero . 'hero-1092.webp' ); ?> 1092w" sizes="520px">
			<img src="<?php echo esc_url( $km_hero . 'poster.webp' ); ?>" width="640" height="986" fetchpriority="high" alt="Ilustrasi rumah Komunitas MEA: gudang, toko, dan rumah untuk seller; studio live, kamar kos, dan warung untuk kreator.">
		</picture>
		<?php
		foreach ( $km_rooms as $km_room ) :
			list( $km_slug, $km_key, $km_side, $km_box, $km_step, $km_content ) = $km_room;
			$km_dest = km_dest( $km_key );
			if ( ! $km_dest ) {
				continue;
			}
			$km_style = vsprintf( 'left:%d%%;top:%d%%;width:%d%%;height:%d%%', $km_box );
			?>
			<a class="spot spot--<?php echo esc_attr( $km_side ); ?>" data-room="<?php echo esc_attr( $km_slug ); ?>" href="<?php echo esc_url( km_utm( $km_dest['url'], 'homepage_hero', $km_content ) ); ?>" style="<?php echo esc_attr( $km_style ); ?>" target="_blank" rel="noopener"<?php echo km_track_attrs( $km_key, 's' === $km_side ? 'seller' : 'kreator', 'hero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><span class="chip"><strong><?php echo esc_html( $km_step ); ?></strong> <?php echo esc_html( $km_dest['label'] ); ?></span><span class="screen-reader-text"> (buka tab baru)</span></a>
		<?php endforeach; ?>
	</div>
</section>
