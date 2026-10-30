<?php
/**
 * Komunitas MEA — setup tema.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

define( 'KM_VERSION', '1.0.0' );

require get_template_directory() . '/inc/helpers.php';
require get_template_directory() . '/inc/assets.php';
require get_template_directory() . '/inc/menus.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/schema.php';

if ( ! isset( $content_width ) ) {
	$content_width = 760;
}

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		// Memberi tahu plugin mea-router bahwa single.php punya slot CTA akhir.
		add_theme_support( 'mea-router-slot' );

		add_image_size( 'km-card', 640, 360, true );

		register_nav_menus(
			array(
				'primary'          => 'Menu utama (header)',
				'footer-komunitas' => 'Footer: Komunitas MEA',
				'footer-seller'    => 'Footer: Untuk seller',
				'footer-kreator'   => 'Footer: Untuk kreator',
				'footer-ekosistem' => 'Footer: Ekosistem MEA Grup',
			)
		);
	}
);

/*
 * Homepage selalu memakai front-page.php, termasuk kalau halaman "homepage"
 * (ID 102135) masih bertemplate Elementor Canvas/Full Width. Elementor memasang
 * filter template_include di prioritas 11; filter ini jalan sesudahnya.
 * Mode edit/preview Elementor dibiarkan.
 */
add_filter(
	'template_include',
	function ( $template ) {
		if ( is_front_page() && ! is_home() && ! isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$front = locate_template( 'front-page.php' );
			if ( $front ) {
				return $front;
			}
		}
		return $template;
	},
	99
);

/*
 * Halaman Elementor lama didesain untuk latar terang. Paksa mode terang di sana
 * supaya teks yang mewarisi warna tema tidak jadi terang di atas section putih.
 */
add_filter(
	'language_attributes',
	function ( $output ) {
		if ( ! is_admin() && is_singular() && km_is_elementor_page( get_queried_object_id() ) ) {
			$output .= ' data-theme="light"';
		}
		return $output;
	}
);

add_filter(
	'body_class',
	function ( $classes ) {
		if ( is_singular() && km_is_elementor_page( get_queried_object_id() ) ) {
			$classes[] = 'km-builder-page';
		}
		return $classes;
	}
);

/* Arsip kategori: tanpa awalan "Kategori:". */
add_filter(
	'get_the_archive_title_prefix',
	function ( $prefix ) {
		return ( is_category() || is_tag() ) ? '' : $prefix;
	}
);

add_filter(
	'excerpt_length',
	function () {
		return 22;
	}
);

add_filter(
	'excerpt_more',
	function () {
		return '…';
	}
);
