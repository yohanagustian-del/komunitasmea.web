<?php
/**
 * Halaman tidak ditemukan.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main wrap archive-wrap">
	<header class="page-head">
		<h1>Halaman tidak ditemukan</h1>
		<div class="desc">
			<p>Halaman yang kamu cari mungkin sudah dipindah. Coba cari artikelnya, atau mulai dari jalur <a href="<?php echo esc_url( home_url( '/#tangga-seller' ) ); ?>">seller</a> atau <a href="<?php echo esc_url( home_url( '/#tangga-kreator' ) ); ?>">kreator</a>.</p>
		</div>
		<?php get_search_form(); ?>
	</header>
	<?php get_template_part( 'template-parts/popular' ); ?>
</main>
<?php
get_footer();
