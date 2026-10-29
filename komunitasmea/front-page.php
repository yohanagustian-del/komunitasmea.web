<?php
/**
 * Homepage: rumah Komunitas MEA.
 *
 * Urutan (disetujui 29 Okt 2026): hero → tentang → tangga seller → tangga kreator
 * → paling banyak dibaca → kegiatan → testimoni → FAQ → fat footer.
 * Isi halaman "homepage" (ID 102135) tidak dirender; halaman itu tetap dipakai
 * sebagai Front Page supaya URL dan meta Rank Math-nya tetap.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main home-main">
	<?php
	get_template_part( 'template-parts/hero' );
	get_template_part( 'template-parts/about' );
	get_template_part( 'template-parts/ladder-seller' );
	get_template_part( 'template-parts/ladder-kreator' );
	get_template_part( 'template-parts/popular' );
	get_template_part( 'template-parts/kegiatan' );
	get_template_part( 'template-parts/testimoni' );
	get_template_part( 'template-parts/faq' );
	?>
</main>
<?php
get_footer();
