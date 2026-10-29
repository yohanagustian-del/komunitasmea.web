<?php
/**
 * Hasil pencarian.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

get_header();
$km_query = get_search_query();
?>
<main id="main" class="site-main wrap archive-wrap">
	<header class="page-head">
		<h1><?php echo '' !== $km_query ? 'Hasil pencarian: ' . esc_html( $km_query ) : 'Cari artikel'; ?></h1>
		<?php get_search_form(); ?>
	</header>
	<?php
	if ( '' !== $km_query ) {
		get_template_part( 'template-parts/loop' );
	}
	if ( '' === $km_query || ! have_posts() ) {
		get_template_part( 'template-parts/popular' );
	}
	?>
</main>
<?php
get_footer();
