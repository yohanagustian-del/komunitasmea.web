<?php
/**
 * Tangga kreator (§3.4), sapaan "kamu": Mulai → Gabung MCN MEA.
 * MEAGO! tampil sebagai cabang samping, bukan anak tangga ke-3.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

$km_steps = array(
	array(
		'key'     => 'affiliateakademi',
		'title'   => 'Mulai',
		'text'    => 'Baru mulai jadi affiliator atau belum punya 1.000 follower? Belajar dari nol di Affiliate Akademi.',
		'req'     => '',
		'button'  => 'Gabung Affiliate Akademi',
		'content' => 'kreator_mulai',
	),
	array(
		'key'     => 'meamcn',
		'title'   => 'Gabung MCN MEA',
		'text'    => 'Akunmu sudah tumbuh? Saatnya gabung MCN MEA.',
		'req'     => 'Syarat: minimal 1.000 follower, berlaku untuk MCN Shopee maupun TikTok.',
		'button'  => 'Daftar MCN MEA',
		'content' => 'kreator_gabung',
	),
);
$km_meago = km_dest( 'meago' );
?>
<section class="section wrap ladder ladder--k" id="tangga-kreator" aria-labelledby="tangga-kreator-title">
	<header>
		<p class="who">Untuk kreator dan affiliator</p>
		<h2 id="tangga-kreator-title">Mulai dari nol sampai gabung MCN</h2>
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
					<p><?php echo esc_html( $km_step['text'] ); ?>
						<?php if ( $km_step['req'] ) : ?>
							<br><span class="req"><?php echo esc_html( $km_step['req'] ); ?></span>
						<?php endif; ?>
					</p>
					<a class="go" href="<?php echo esc_url( km_utm( $km_dest['url'], 'homepage_tangga', $km_step['content'] ) ); ?>" target="_blank" rel="noopener"<?php echo km_track_attrs( $km_step['key'], 'kreator', 'tangga' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $km_step['button'] ); ?><span class="screen-reader-text"> (buka tab baru)</span></a>
				</div>
			</li>
		<?php endforeach; ?>
	</ol>
	<aside class="branch" aria-label="Cabang kreator kuliner">
		<div>
			<h3>Kreator kuliner atau ikut TikTok GO?</h3>
			<p>Jalurmu ada di MEAGO!, partner agency TikTok GO untuk kreator dan merchant.</p>
		</div>
		<a class="go" href="<?php echo esc_url( km_utm( $km_meago['url'], 'homepage_tangga', 'kreator_meago' ) ); ?>" target="_blank" rel="noopener"<?php echo km_track_attrs( 'meago', 'kreator', 'tangga' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>Ke MEAGO!<span class="screen-reader-text"> (buka tab baru)</span></a>
	</aside>
</section>
