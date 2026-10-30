<?php
/**
 * Arsip kategori: judul, deskripsi, sub-kategori, daftar artikel.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

get_header();

$km_term     = get_queried_object();
$km_children = $km_term instanceof WP_Term ? get_terms(
	array(
		'taxonomy'   => 'category',
		'parent'     => $km_term->term_id,
		'hide_empty' => true,
	)
) : array();
?>
<main id="main" class="site-main wrap archive-wrap">
	<header class="page-head">
		<?php
		if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
			rank_math_the_breadcrumbs();
		}
		?>
		<h1><?php single_cat_title(); ?></h1>
		<?php
		$km_desc = category_description();
		if ( $km_desc ) :
			?>
			<div class="desc"><?php echo wp_kses_post( $km_desc ); ?></div>
		<?php endif; ?>
		<?php if ( $km_children && ! is_wp_error( $km_children ) ) : ?>
			<ul class="chips" aria-label="Sub-kategori">
				<?php foreach ( $km_children as $km_child ) : ?>
					<li><a href="<?php echo esc_url( get_category_link( $km_child ) ); ?>"><?php echo esc_html( $km_child->name ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</header>
	<?php get_template_part( 'template-parts/loop' ); ?>
</main>
<?php
get_footer();
