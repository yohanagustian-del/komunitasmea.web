<?php
/**
 * Data tujuan, UTM, placeholder, dan helper kecil lain.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tujuan konversi dan tautan ekosistem. Kunci array dipakai sebagai utm_content
 * di footer dan sebagai nilai data-mea-dest untuk event GA4.
 *
 * @return array<string, array{url:string,label:string,path:string}>
 */
function km_destinations() {
	return apply_filters(
		'komunitasmea_destinations',
		array(
			'skilskul'         => array( 'url' => 'https://skilskul.co.id/', 'label' => 'SkilSkul', 'path' => 'seller' ),
			'ordal'            => array( 'url' => 'https://ordal.co.id/', 'label' => 'Ordal', 'path' => 'seller' ),
			'meagency'         => array( 'url' => 'https://meagency.co.id/', 'label' => 'MEA Agency', 'path' => 'seller' ),
			'affiliateakademi' => array( 'url' => 'https://affiliateakademi.id/?ref=AFAKADEMI-2992', 'label' => 'Affiliate Akademi', 'path' => 'kreator' ),
			'meamcn'           => array( 'url' => 'https://meamcn.com/', 'label' => 'MCN MEA', 'path' => 'kreator' ),
			'meago'            => array( 'url' => 'https://meago.id/', 'label' => 'MEAGO!', 'path' => 'kreator' ),
			'linkreator'       => array( 'url' => 'https://linkreator.id/', 'label' => 'Linkreator', 'path' => 'ekosistem' ),
			'sebari'           => array( 'url' => 'https://sebari.co.id/', 'label' => 'Sebari', 'path' => 'ekosistem' ),
			'tiktok'           => array( 'url' => 'https://www.tiktok.com/@101bisnis', 'label' => 'TikTok @101bisnis', 'path' => 'ekosistem' ),
			'instagram'        => array( 'url' => 'https://www.instagram.com/yohanagustian/', 'label' => 'Instagram @yohanagustian', 'path' => 'ekosistem' ),
		)
	);
}

/**
 * @param string $key Kunci dari km_destinations().
 * @return array{url:string,label:string,path:string}|null
 */
function km_dest( $key ) {
	$all = km_destinations();
	return isset( $all[ $key ] ) ? $all[ $key ] : null;
}

/**
 * Tambahkan UTM standar komunitasmea ke URL. Query yang sudah ada
 * (mis. ref=AFAKADEMI-2992) dipertahankan; UTM ditambahkan dengan &.
 *
 * @param string $url      URL tujuan.
 * @param string $campaign utm_campaign.
 * @param string $content  utm_content.
 * @return string URL mentah (belum di-escape).
 */
function km_utm( $url, $campaign, $content ) {
	return add_query_arg(
		array(
			'utm_source'   => 'komunitasmea',
			'utm_medium'   => 'referral',
			'utm_campaign' => rawurlencode( $campaign ),
			'utm_content'  => rawurlencode( $content ),
		),
		$url
	);
}

/**
 * Atribut data-mea-* untuk listener klik GA4 (dari plugin mea-router).
 *
 * @return string Atribut HTML yang sudah di-escape, diawali spasi.
 */
function km_track_attrs( $dest, $path, $pos ) {
	return sprintf(
		' data-mea-dest="%s" data-mea-path="%s" data-mea-pos="%s"',
		esc_attr( $dest ),
		esc_attr( $path ),
		esc_attr( $pos )
	);
}

/**
 * Cari kunci tujuan dari host URL (dipakai untuk tautan menu footer).
 *
 * @return string Kunci km_destinations(), atau label pertama host.
 */
function km_dest_key_from_url( $url ) {
	$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
	$host = preg_replace( '/^www\./', '', $host );
	foreach ( km_destinations() as $key => $dest ) {
		$dest_host = preg_replace( '/^www\./', '', strtolower( (string) wp_parse_url( $dest['url'], PHP_URL_HOST ) ) );
		if ( $dest_host === $host ) {
			return $key;
		}
	}
	$parts = explode( '.', $host );
	return sanitize_key( $parts[0] );
}

/**
 * Apakah URL mengarah keluar situs ini.
 */
function km_is_external( $url ) {
	$host = wp_parse_url( $url, PHP_URL_HOST );
	if ( ! $host ) {
		return false;
	}
	$home = wp_parse_url( home_url(), PHP_URL_HOST );
	return preg_replace( '/^www\./', '', strtolower( $host ) ) !== preg_replace( '/^www\./', '', strtolower( (string) $home ) );
}

/**
 * Placeholder [ISI: Yohan]. Hanya tampil untuk admin yang login (staging/review);
 * pengunjung tidak melihat apa pun.
 *
 * @param string $label Teks placeholder.
 * @return string HTML.
 */
function km_placeholder( $label = '[ISI: Yohan]' ) {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return '';
	}
	return '<mark class="km-ph">' . esc_html( $label ) . '</mark>';
}

/**
 * Apakah halaman dibangun dengan Elementor (dan Elementor aktif).
 */
function km_is_elementor_page( $post_id = 0 ) {
	if ( ! did_action( 'elementor/loaded' ) ) {
		return false;
	}
	$post_id = $post_id ? $post_id : get_the_ID();
	return $post_id && 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}

/**
 * Kategori utama post: primary category Rank Math, lalu sub-kategori, lalu kategori pertama.
 *
 * @return WP_Term|null
 */
function km_primary_category( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$primary = (int) get_post_meta( $post_id, 'rank_math_primary_category', true );
	if ( $primary ) {
		$term = get_term( $primary, 'category' );
		if ( $term instanceof WP_Term ) {
			return $term;
		}
	}
	$cats = get_the_category( $post_id );
	if ( ! $cats ) {
		return null;
	}
	foreach ( $cats as $cat ) {
		if ( $cat->parent ) {
			return $cat;
		}
	}
	return $cats[0];
}

/**
 * Daftar awal "Paling banyak dibaca" (klik GSC 28 Apr – 27 Okt 2026).
 * Sumber utama adalah option mea_router_popular_* yang diisi dari Settings → MEA Router;
 * daftar ini hanya cadangan kalau option belum ada.
 */
function km_popular_defaults( $group ) {
	$defaults = array(
		'kreator' => array( 103943, 103795, 103899, 103202, 99015, 104395 ),
		'jualan'  => array( 104135, 104144, 91471, 64269, 59853, 92860 ),
	);
	return isset( $defaults[ $group ] ) ? $defaults[ $group ] : array();
}

/**
 * Post terpublikasi dari daftar kurasi, urutan sesuai daftar.
 * Post yang tidak lagi publish dilewati.
 *
 * @param string $group kreator|jualan.
 * @return WP_Post[]
 */
function km_popular_posts( $group ) {
	$ids = get_option( 'mea_router_popular_' . $group, null );
	$ids = ( null === $ids || '' === $ids ) ? km_popular_defaults( $group ) : wp_parse_id_list( $ids );
	$ids = array_slice( array_filter( $ids ), 0, 6 );
	if ( ! $ids ) {
		return array();
	}
	return get_posts(
		array(
			'post__in'            => $ids,
			'orderby'             => 'post__in',
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => count( $ids ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'suppress_filters'    => false,
		)
	);
}

/**
 * Jalur (seller/kreator) sebuah kategori untuk pewarnaan label. Pakai pemetaan
 * plugin mea-router kalau aktif.
 */
function km_category_path( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return '';
	}
	if ( function_exists( 'mea_router_category_path' ) ) {
		return (string) mea_router_category_path( $term->term_id );
	}
	return '';
}

/**
 * Tanggal tampil: "Diperbarui" kalau diubah > 1 hari setelah terbit.
 */
function km_entry_date() {
	$published = get_the_time( 'U' );
	$modified  = get_the_modified_time( 'U' );
	if ( $modified - $published > DAY_IN_SECONDS ) {
		return sprintf(
			'Diperbarui <time datetime="%s">%s</time>',
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);
	}
	return sprintf(
		'<time datetime="%s">%s</time>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() )
	);
}

/**
 * Isi FAQ homepage. Jawaban kosong = belum ada (hanya admin yang melihat placeholder,
 * dan item itu tidak ikut ke schema FAQPage).
 *
 * @return array<int, array{q:string,a:string}>
 */
function km_faq_items() {
	$items = array(
		array(
			'q' => 'Apa itu Komunitas MEA?',
			'a' => 'Komunitas belajar untuk seller dan kreator affiliate di Shopee dan TikTok Shop, dibangun oleh MEA sejak 2020 di Bandung. Saat ini ada 12.000+ kreator dan 3.000+ seller di dalamnya.',
		),
		array(
			'q' => 'Saya seller pemula, mulai dari mana?',
			'a' => 'Mulai dari SkilSkul untuk belajar dasar jualan di Shopee dan TikTok Shop secara bertahap.',
		),
		array(
			'q' => 'Apa bedanya didampingi Ordal dan dikelola MEA Agency?',
			'a' => 'Kalau Anda mau didampingi, pilih Ordal: Anda tetap menjalankan toko sendiri dengan strategi yang disusun bersama pendamping. Kalau Anda mau tokonya dikerjakan tim MEA, pilih MEA Agency: tim MEA yang mengerjakan iklan, kelola toko, konten, live, dan affiliate.',
		),
		array(
			'q' => 'Apa syarat gabung MCN MEA?',
			'a' => 'Minimal 1.000 follower, berlaku untuk MCN Shopee maupun TikTok.',
		),
		array(
			'q' => 'Belum punya 1.000 follower, bisa ikut?',
			'a' => 'Bisa. Mulai dari Affiliate Akademi untuk membangun akun dan skill sampai siap gabung MCN MEA.',
		),
		array(
			'q' => 'MEAGO! itu untuk siapa?',
			'a' => 'Untuk kreator kuliner dan kreator yang ikut program TikTok GO.',
		),
		array(
			'q' => 'Apakah gabung komunitas berbayar?',
			'a' => trim( (string) get_theme_mod( 'km_faq7_answer', '' ) ),
		),
	);
	return apply_filters( 'komunitasmea_faq_items', $items );
}

/**
 * Isi file aset tema, untuk di-inline. Komentar dan spasi berlebih dibuang.
 */
function km_inline_asset( $relative ) {
	static $cache = array();
	if ( isset( $cache[ $relative ] ) ) {
		return $cache[ $relative ];
	}
	$file = get_theme_file_path( $relative );
	$css  = is_readable( $file ) ? (string) file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$css  = preg_replace( '#/\*.*?\*/#s', '', $css );
	$css  = preg_replace( '/\s+/', ' ', $css );
	$css  = preg_replace( '/\s*([{};,>])\s*/', '$1', $css );
	return $cache[ $relative ] = trim( $css );
}

/**
 * Versi aset dari waktu modifikasi file (cache-busting).
 */
function km_asset_ver( $relative ) {
	$file = get_theme_file_path( $relative );
	return file_exists( $file ) ? (string) filemtime( $file ) : KM_VERSION;
}
