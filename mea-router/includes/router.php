<?php
/**
 * Penentuan jalur (seller / kreator / ganda / off) per post.
 *
 * Aturan (brief §4):
 * 1. Override per post (_mea_router_path) selalu menang.
 * 2. Sub-kategori lebih menentukan daripada induk: kalau post punya induk dan
 *    anaknya sekaligus, induknya diabaikan.
 * 3. Kategori yang tidak dipetakan mewarisi jalur induk terdekat yang dipetakan.
 * 4. Seller + kreator sekaligus → ganda. Satu jalur spesifik → jalur itu, walau
 *    ada juga kategori umum ber-jalur ganda (mis. Sosial Media). Hanya kategori
 *    ganda → ganda. Tidak ada yang terpetakan → default_path (bawaan ganda).
 *
 * @package mea-router
 */

defined( 'ABSPATH' ) || exit;

/**
 * Jalur valid.
 */
function mea_router_paths() {
	return array( 'seller', 'kreator', 'ganda' );
}

/**
 * Jalur sebuah kategori, mewarisi induk kalau kategori itu sendiri tidak dipetakan.
 *
 * @param int $term_id ID kategori.
 * @return string seller|kreator|ganda|'' (tidak terpetakan).
 */
function mea_router_category_path( $term_id ) {
	$settings = mea_router_settings();
	$map      = array();
	foreach ( mea_router_paths() as $path ) {
		foreach ( wp_parse_id_list( $settings[ 'cat_' . $path ] ) as $id ) {
			if ( ! isset( $map[ $id ] ) ) {
				$map[ $id ] = $path;
			}
		}
	}
	$chain = array_merge( array( (int) $term_id ), get_ancestors( (int) $term_id, 'category', 'taxonomy' ) );
	foreach ( $chain as $id ) {
		if ( isset( $map[ $id ] ) ) {
			return $map[ $id ];
		}
	}
	return '';
}

/**
 * Jalur otomatis dari kategori (tanpa melihat override).
 *
 * @return string seller|kreator|ganda
 */
function mea_router_auto_path( $post_id ) {
	$cats = wp_get_post_categories( $post_id );

	// Buang kategori yang merupakan induk dari kategori lain milik post ini.
	$ancestors = array();
	foreach ( $cats as $cat ) {
		$ancestors = array_merge( $ancestors, get_ancestors( $cat, 'category', 'taxonomy' ) );
	}
	$cats = array_diff( $cats, $ancestors );

	$found = array();
	foreach ( $cats as $cat ) {
		$path = mea_router_category_path( $cat );
		if ( $path ) {
			$found[ $path ] = true;
		}
	}

	if ( isset( $found['seller'] ) && isset( $found['kreator'] ) ) {
		$path = 'ganda';
	} elseif ( isset( $found['seller'] ) ) {
		$path = 'seller';
	} elseif ( isset( $found['kreator'] ) ) {
		$path = 'kreator';
	} elseif ( isset( $found['ganda'] ) ) {
		$path = 'ganda';
	} else {
		$settings = mea_router_settings();
		$path     = in_array( $settings['default_path'], array( 'seller', 'kreator', 'ganda', 'off' ), true ) ? $settings['default_path'] : 'ganda';
	}

	return $path;
}

/**
 * Jalur final sebuah post.
 *
 * @return string seller|kreator|ganda|off
 */
function mea_router_post_path( $post_id ) {
	$override = get_post_meta( $post_id, '_mea_router_path', true );
	if ( in_array( $override, array( 'seller', 'kreator', 'ganda', 'off' ), true ) ) {
		$path = $override;
	} else {
		$path = mea_router_auto_path( $post_id );
	}
	return apply_filters( 'mea_router_post_path', $path, $post_id );
}
