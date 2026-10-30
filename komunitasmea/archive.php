<?php
/**
 * Arsip (tag, penulis, tanggal). Kategori memakai category.php.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main wrap archive-wrap">
	<header class="page-head">
		<h1><?php echo wp_kses_post( get_the_archive_title() ); ?></h1>
		<?php
		$km_desc = get_the_archive_description();
		if ( $km_desc ) :
			?>
			<div class="desc"><?php echo wp_kses_post( $km_desc ); ?></div>
		<?php endif; ?>
	</header>
	<?php get_template_part( 'template-parts/loop' ); ?>
</main>
<?php
get_footer();
