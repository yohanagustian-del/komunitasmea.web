<?php
/**
 * Cadangan untuk semua tampilan lain (mis. halaman blog).
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main wrap archive-wrap">
	<header class="page-head">
		<h1><?php echo is_home() && get_option( 'page_for_posts' ) ? esc_html( get_the_title( get_option( 'page_for_posts' ) ) ) : 'Artikel terbaru'; ?></h1>
	</header>
	<?php get_template_part( 'template-parts/loop' ); ?>
</main>
<?php
get_footer();
