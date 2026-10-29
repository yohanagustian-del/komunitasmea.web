<?php
/**
 * CSS, JS, font, dan pembersihan aset yang tidak perlu.
 *
 * Anggaran: satu file CSS (main.css) + critical CSS di-inline; di homepage
 * hanya hero.js (≤ 3 KB) dari tema; tanpa jQuery dari tema.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

/**
 * Daftar font self-host. Hanya dua yang di-preload (H1 dan teks isi).
 */
function km_fonts() {
	return array(
		array( 'Bricolage Grotesque', 800, 'bricolage-grotesque-latin-800-normal.woff2', true ),
		array( 'Plus Jakarta Sans', 400, 'plus-jakarta-sans-latin-400-normal.woff2', true ),
		array( 'Plus Jakarta Sans', 500, 'plus-jakarta-sans-latin-500-normal.woff2', false ),
		array( 'Plus Jakarta Sans', 700, 'plus-jakarta-sans-latin-700-normal.woff2', false ),
	);
}

/**
 * Preload font + critical CSS, sedini mungkin di <head>.
 */
add_action(
	'wp_head',
	function () {
		$base = get_template_directory_uri() . '/assets/fonts/';
		$face = '';
		foreach ( km_fonts() as $font ) {
			list( $family, $weight, $file, $preload ) = $font;
			$url = $base . $file;
			if ( $preload ) {
				printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( $url ) );
			}
			$face .= sprintf(
				'@font-face{font-family:"%s";font-style:normal;font-weight:%d;font-display:swap;src:url(%s) format("woff2");unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}',
				$family,
				$weight,
				esc_url( $url )
			);
		}
		echo '<style id="km-critical">' . $face . km_inline_asset( 'assets/css/critical.css' ) . "</style>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS milik tema.
	},
	2
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$uri = get_template_directory_uri();

		wp_enqueue_style( 'komunitasmea', $uri . '/assets/css/main.css', array(), km_asset_ver( 'assets/css/main.css' ) );

		if ( is_front_page() ) {
			wp_enqueue_script(
				'komunitasmea-hero',
				$uri . '/assets/js/hero.js',
				array(),
				km_asset_ver( 'assets/js/hero.js' ),
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);
		}

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
);

/*
 * Di homepage main.css tidak memblokir render: semua yang terlihat di layar
 * pertama (header + hero) sudah ada di critical CSS.
 */
add_filter(
	'style_loader_tag',
	function ( $tag, $handle, $href ) {
		if ( 'komunitasmea' !== $handle || ! is_front_page() ) {
			return $tag;
		}
		return sprintf(
			'<link rel="stylesheet" id="komunitasmea-css" href="%1$s" media="print" onload="this.media=\'all\'">' . "\n" .
			'<noscript><link rel="stylesheet" href="%1$s"></noscript>' . "\n",
			esc_url( $href )
		);
	},
	10,
	3
);

/* ---------- Buang bloat ---------- */

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
add_filter( 'emoji_svg_url', '__return_false' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );

/**
 * Awalan handle aset builder yang tidak dipakai homepage (homepage dirender
 * front-page.php, bukan Elementor). Bisa diubah lewat filter.
 */
function km_front_strip_prefixes() {
	return apply_filters(
		'komunitasmea_front_strip_prefixes',
		array( 'elementor', 'e-animation', 'e-sticky', 'e-swiper', 'swiper', 'ekit', 'elementskit', 'google-fonts-', 'font-awesome', 'fontawesome' )
	);
}

/**
 * Lepas CSS/JS yang tidak dipakai homepage: style blok Gutenberg dan aset Elementor.
 */
function km_strip_front_assets() {
	if ( ! is_front_page() || isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	foreach ( array( 'wp-block-library', 'wp-block-library-theme', 'classic-theme-styles', 'global-styles', 'core-block-supports' ) as $handle ) {
		wp_dequeue_style( $handle );
	}
	$prefixes = km_front_strip_prefixes();
	foreach ( array( wp_styles(), wp_scripts() ) as $deps ) {
		foreach ( (array) $deps->queue as $handle ) {
			foreach ( $prefixes as $prefix ) {
				if ( 0 === strpos( $handle, $prefix ) ) {
					$deps instanceof WP_Scripts ? wp_dequeue_script( $handle ) : wp_dequeue_style( $handle );
					break;
				}
			}
		}
	}
}
add_action( 'wp_enqueue_scripts', 'km_strip_front_assets', 999 );
add_action( 'wp_print_footer_scripts', 'km_strip_front_assets', 1 );
