<?php
/**
 * Tangga seller (§3.3), sapaan "Anda": Belajar → Didampingi → Dikelola.
 * Ordal vs MEA Agency dibedakan dengan wording, bukan omzet.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

$km_steps = array(
	array(
		'key'     => 'skilskul',
		'title'   => 'Belajar',
		'text'    => 'Baru mulai atau ingin merapikan dasar jualan di Shopee dan TikTok Shop? Belajar bertahap bersama SkilSkul.',
		'button'  => 'Mulai belajar di SkilSkul',
		'content' => 'seller_belajar',
	),
	array(
		'key'     => 'ordal',
		'title'   => 'Didampingi',
		'text'    => 'Sudah jualan dan mau didampingi? Anda tetap menjalankan toko sendiri, strateginya disusun bersama Ordal.',
		'button'  => 'Didampingi Ordal',
		'content' => 'seller_didampingi',
	),
	array(
		'key'     => 'meagency',
		'title'   => 'Dikelola',
		'text'    => 'Mau toko Anda dikerjakan tim MEA? MEA Agency menangani iklan, kelola toko, konten, live, dan affiliate.',
		'button'  => 'Serahkan ke MEA Agency',
		'content' => 'seller_dikelola',
	),
);
?>
<section class="section wrap ladder ladder--s" id="tangga-seller" aria-labelledby="tangga-seller-title">
	<header>
		<p class="who">Untuk seller</p>
		<h2 id="tangga-seller-title">Naik dari belajar sampai toko Anda dikelola</h2>
	</header>
	<ol>
		<?php
		foreach ( $km_steps as $km_i => $km_step ) :
			$km_dest = km_dest( $km_step['key'] );
			?>
			<li class="step">
				<span class="n" aria-hidden="true"><?php echo (int) $km_i + 1; ?></span>
				<div>
					<h3><?php echo esc_html( $km_step['title'] ); ?></h3>
					<p><?php echo esc_html( $km_step['text'] ); ?></p>
					<a class="go" href="<?php echo esc_url( km_utm( $km_dest['url'], 'homepage_tangga', $km_step['content'] ) ); ?>" target="_blank" rel="noopener"<?php echo km_track_attrs( $km_step['key'], 'seller', 'tangga' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $km_step['button'] ); ?><span class="screen-reader-text"> (buka tab baru)</span></a>
				</div>
			</li>
		<?php endforeach; ?>
	</ol>
</section>
