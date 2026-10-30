<?php
/**
 * Menu header dan footer.
 *
 * Footer memakai 4 lokasi menu WordPress supaya bisa diedit tanpa kode. Selama
 * lokasi belum diisi, isi bawaan (sesuai brief §3.9) yang tampil. Tautan keluar
 * di footer otomatis diberi UTM utm_campaign=footer&utm_content=<brand>.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lokasi menu footer → jalur, untuk atribut data-mea-path.
 */
function km_footer_locations() {
	return array(
		'footer-komunitas' => 'komunitas',
		'footer-seller'    => 'seller',
		'footer-kreator'   => 'kreator',
		'footer-ekosistem' => 'ekosistem',
	);
}

/**
 * URL tautan footer keluar: UTM footer + atribut pelacakan.
 *
 * @return array{href:string,attrs:string}
 */
function km_footer_link( $url, $location ) {
	$locations = km_footer_locations();
	if ( ! km_is_external( $url ) ) {
		return array( 'href' => $url, 'attrs' => '' );
	}
	$brand = km_dest_key_from_url( $url );
	$path  = isset( $locations[ $location ] ) ? $locations[ $location ] : 'footer';
	return array(
		'href'  => km_utm( $url, 'footer', $brand ),
		'attrs' => km_track_attrs( $brand, $path, 'footer' ),
	);
}

/* Tautan dari menu WordPress di lokasi footer: tambah UTM dan data-mea-*. */
add_filter(
	'nav_menu_link_attributes',
	function ( $atts, $item, $args ) {
		$location = isset( $args->theme_location ) ? $args->theme_location : '';
		$locations = km_footer_locations();
		if ( ! isset( $locations[ $location ] ) || empty( $atts['href'] ) || ! km_is_external( $atts['href'] ) ) {
			return $atts;
		}
		if ( false !== strpos( $atts['href'], 'utm_source=' ) ) {
			return $atts; // UTM sudah diisi manual di menu.
		}
		$brand                = km_dest_key_from_url( $atts['href'] );
		$atts['href']         = km_utm( $atts['href'], 'footer', $brand );
		$atts['data-mea-dest'] = $brand;
		$atts['data-mea-path'] = $locations[ $location ];
		$atts['data-mea-pos']  = 'footer';
		return $atts;
	},
	10,
	3
);

/**
 * Menu header bawaan (kalau lokasi "primary" belum diisi).
 */
function km_primary_menu_fallback() {
	$home  = is_front_page() ? '' : home_url( '/' );
	$items = array(
		array( $home . '#tangga-seller', 'Untuk seller' ),
		array( $home . '#tangga-kreator', 'Untuk kreator' ),
		array( $home . '#artikel', 'Artikel' ),
		array( home_url( '/?s=' ), 'Cari' ),
	);
	echo '<ul>';
	foreach ( $items as $item ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $item[0] ), esc_html( $item[1] ) );
	}
	echo '</ul>';
}

/**
 * Isi bawaan kolom footer.
 *
 * @param string $location Lokasi menu footer.
 * @return array<int, array{0:string,1:string}> [url, label]
 */
function km_footer_default_items( $location ) {
	$dest = function ( $key ) {
		$d = km_dest( $key );
		return $d ? array( $d['url'], $d['label'] ) : null;
	};
	$cat = function ( $id, $label ) {
		$term = get_term( $id, 'category' );
		if ( ! $term instanceof WP_Term ) {
			return null;
		}
		return array( get_category_link( $term ), $label );
	};

	switch ( $location ) {
		case 'footer-komunitas':
			$items = array( array( home_url( '/about-us/' ), 'Tentang kami' ) );
			break;
		case 'footer-seller':
			$items = array(
				$dest( 'skilskul' ),
				$dest( 'ordal' ),
				$dest( 'meagency' ),
				$cat( 5710, 'Artikel Shopee Seller' ),
				$cat( 5711, 'Artikel TikTok Seller' ),
				$cat( 904, 'Tips jualan di marketplace' ),
			);
			break;
		case 'footer-kreator':
			$items = array(
				$dest( 'affiliateakademi' ),
				$dest( 'meamcn' ),
				$dest( 'meago' ),
				$cat( 5708, 'Artikel TikTok Affiliate' ),
				$cat( 5709, 'Artikel Shopee Affiliate' ),
				$cat( 5713, 'Cara memulai affiliate' ),
			);
			break;
		case 'footer-ekosistem':
			$items = array(
				$dest( 'linkreator' ),
				$dest( 'sebari' ),
				array( home_url( '/shopeasytools/' ), 'Shopeasytools' ),
				$dest( 'tiktok' ),
				$dest( 'instagram' ),
			);
			break;
		default:
			$items = array();
	}
	return array_values( array_filter( $items ) );
}

/**
 * Fallback wp_nav_menu untuk lokasi footer.
 *
 * @param array $args Argumen wp_nav_menu (theme_location dipakai).
 */
function km_footer_menu_fallback( $args ) {
	$location = is_array( $args ) ? $args['theme_location'] : $args->theme_location;
	$items    = km_footer_default_items( $location );
	if ( ! $items ) {
		return;
	}
	echo '<ul>';
	foreach ( $items as $item ) {
		$link = km_footer_link( $item[0], $location );
		printf( '<li><a href="%s"%s>%s</a></li>', esc_url( $link['href'] ), $link['attrs'], esc_html( $item[1] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attrs di-escape di km_track_attrs().
	}
	echo '</ul>';
}

/**
 * Cetak satu kolom menu footer.
 */
function km_footer_menu( $location ) {
	wp_nav_menu(
		array(
			'theme_location' => $location,
			'container'      => false,
			'depth'          => 1,
			'fallback_cb'    => 'km_footer_menu_fallback',
		)
	);
}

/**
 * URL Kebijakan Privasi kalau halamannya sudah publish, selain itu ''.
 * Halaman ID 104983 (masih draft per 29 Okt 2026); bisa diganti di Customizer.
 */
function km_privacy_url() {
	$ids = array( (int) get_theme_mod( 'km_privacy_page_id', 104983 ), (int) get_option( 'wp_page_for_privacy_policy' ) );
	foreach ( array_filter( $ids ) as $id ) {
		if ( 'publish' === get_post_status( $id ) ) {
			return get_permalink( $id );
		}
	}
	return '';
}
