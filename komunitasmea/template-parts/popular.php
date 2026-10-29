<?php
/**
 * Paling banyak dibaca (§3.6): 2 kolom × 6 artikel kurasi, tanpa thumbnail.
 * Daftar ID diatur di Settings → MEA Router (option mea_router_popular_kreator
 * dan mea_router_popular_jualan), diperbarui MEA SEO Room tiap audit bulanan.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

$km_columns = array(
	array(
		'group' => 'kreator',
		'title' => 'Untuk kreator',
		'class' => 'k',
		'more'  => array( 5713, '/category/cara-memulai-affiliate/', 'Semua artikel kreator' ),
	),
	array(
		'group' => 'jualan',
		'title' => 'Untuk jualan',
		'class' => 's',
		'more'  => array( 904, '/category/tips-jualan-di-marketplace/', 'Semua artikel jualan' ),
	),
);

$km_lists = array();
foreach ( $km_columns as $km_col ) {
	$km_lists[ $km_col['group'] ] = km_popular_posts( $km_col['group'] );
}
if ( ! array_filter( $km_lists ) ) {
	return;
}
?>
<section class="section wrap reads" id="artikel" aria-labelledby="artikel-title">
	<header>
		<h2 id="artikel-title">Paling banyak dibaca</h2>
		<p>Dipilih dari artikel dengan klik Google tertinggi 6 bulan terakhir, diperbarui tiap bulan.</p>
	</header>
	<div class="cols">
		<?php
		foreach ( $km_columns as $km_col ) :
			$km_posts = $km_lists[ $km_col['group'] ];
			if ( ! $km_posts ) {
				continue;
			}
			$km_term     = get_term( $km_col['more'][0], 'category' );
			$km_more_url = $km_term instanceof WP_Term ? get_category_link( $km_term ) : home_url( $km_col['more'][1] );
			?>
			<div>
				<h3 class="colh colh--<?php echo esc_attr( $km_col['class'] ); ?>"><?php echo esc_html( $km_col['title'] ); ?></h3>
				<ol class="list">
					<?php
					foreach ( $km_posts as $km_post ) :
						$km_cat = km_primary_category( $km_post->ID );
						?>
						<li><a href="<?php echo esc_url( get_permalink( $km_post ) ); ?>">
							<span class="cat"><?php echo $km_cat ? esc_html( $km_cat->name ) : ''; ?></span>
							<span class="t"><?php echo esc_html( get_the_title( $km_post ) ); ?></span>
						</a></li>
					<?php endforeach; ?>
				</ol>
				<a class="more" href="<?php echo esc_url( $km_more_url ); ?>"><?php echo esc_html( $km_col['more'][2] ); ?></a>
			</div>
		<?php endforeach; ?>
	</div>
</section>
