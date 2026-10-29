<?php
/**
 * Pengaturan bawaan dan akses option.
 *
 * Option yang dipakai plugin ini (semua dihapus saat uninstall):
 * - mea_router_settings         array, lihat mea_router_default_settings()
 * - mea_router_popular_kreator  int[], ID post kolom "Untuk kreator" di homepage
 * - mea_router_popular_jualan   int[], ID post kolom "Untuk jualan" di homepage
 * Post meta: _mea_router_path (auto|seller|kreator|ganda|off).
 *
 * @package mea-router
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string, mixed>
 */
function mea_router_default_settings() {
	return array(
		// ID kategori produksi komunitasmea.web.id (brief §4).
		'cat_seller'   => array( 5710, 5711, 904, 1323 ),
		'cat_kreator'  => array( 5708, 5709, 5713 ),
		'cat_ganda'    => array( 1, 3924, 905, 1123 ),
		// Jalur untuk post yang kategorinya tidak terpetakan sama sekali.
		'default_path' => 'ganda',
		// Kartu ringkas disisipkan setelah paragraf ke-N...
		'mid_after'    => 3,
		// ...asal masih ada minimal sekian paragraf sesudahnya (supaya tidak menempel ke kartu akhir).
		'mid_min_rest' => 2,
		'dest'         => array(
			'skilskul'         => 'https://skilskul.co.id/',
			'ordal'            => 'https://ordal.co.id/',
			'meagency'         => 'https://meagency.co.id/',
			'affiliateakademi' => 'https://affiliateakademi.id/?ref=AFAKADEMI-2992',
			'meamcn'           => 'https://meamcn.com/',
			'meago'            => 'https://meago.id/',
		),
		'copy'         => array(
			'seller_eyebrow'  => 'Untuk seller',
			'seller_title'    => 'Belajar jualan dari dasar',
			'seller_text'     => 'Baru mulai jualan di Shopee atau TikTok Shop? Pelajari dasarnya bertahap bersama SkilSkul.',
			'seller_button'   => 'Belajar di SkilSkul',
			'seller_ordal'    => 'Mau didampingi?',
			'seller_agency'   => 'Mau dikerjakan tim MEA?',
			'kreator_eyebrow' => 'Untuk kreator',
			'kreator_title'   => 'Mulai jadi affiliator dari nol',
			'kreator_text'    => 'Belajar bikin konten affiliate bersama Affiliate Akademi sampai akunmu siap gabung MCN.',
			'kreator_button'  => 'Gabung Affiliate Akademi',
			'kreator_mcn'     => 'Sudah 1.000+ follower?',
			'kreator_meago'   => 'Kreator kuliner atau ikut TikTok GO?',
		),
	);
}

/**
 * Label merek tujuan (tidak bisa diubah dari pengaturan supaya konsisten).
 */
function mea_router_dest_labels() {
	return array(
		'skilskul'         => 'SkilSkul',
		'ordal'            => 'Ordal',
		'meagency'         => 'MEA Agency',
		'affiliateakademi' => 'Affiliate Akademi',
		'meamcn'           => 'Gabung MCN MEA',
		'meago'            => 'MEAGO!',
	);
}

/**
 * Daftar awal "Paling banyak dibaca" (klik GSC 28 Apr – 27 Okt 2026).
 *
 * @return int[]
 */
function mea_router_default_popular( $group ) {
	$lists = array(
		'kreator' => array( 103943, 103795, 103899, 103202, 99015, 104395 ),
		'jualan'  => array( 104135, 104144, 91471, 64269, 59853, 92860 ),
	);
	return isset( $lists[ $group ] ) ? $lists[ $group ] : array();
}

/**
 * Pengaturan tersimpan digabung dengan bawaan (kunci yang hilang diisi bawaan).
 *
 * @return array<string, mixed>
 */
function mea_router_settings() {
	static $settings = null;
	if ( null !== $settings && ! is_admin() ) {
		return $settings;
	}
	$defaults = mea_router_default_settings();
	$saved    = get_option( 'mea_router_settings', array() );
	$saved    = is_array( $saved ) ? $saved : array();
	$settings = array_merge( $defaults, $saved );
	foreach ( array( 'dest', 'copy' ) as $group ) {
		$settings[ $group ] = array_merge( $defaults[ $group ], isset( $saved[ $group ] ) && is_array( $saved[ $group ] ) ? array_filter( $saved[ $group ], 'strlen' ) : array() );
	}
	return $settings;
}

/**
 * Satu teks copy.
 */
function mea_router_copy( $key ) {
	$settings = mea_router_settings();
	return isset( $settings['copy'][ $key ] ) ? (string) $settings['copy'][ $key ] : '';
}

/**
 * URL tujuan mentah (tanpa UTM).
 */
function mea_router_dest_url( $key ) {
	$settings = mea_router_settings();
	return isset( $settings['dest'][ $key ] ) ? (string) $settings['dest'][ $key ] : '';
}
