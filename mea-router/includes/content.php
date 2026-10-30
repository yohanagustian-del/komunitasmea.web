<?php
/**
 * Penempatan otomatis CTA di artikel (post saja, bukan page):
 * - mid: kartu ringkas setelah paragraf ke-3 (tingkat atas, bukan di dalam
 *   kutipan/tabel/daftar).
 * - end: kartu lengkap di akhir artikel. Kalau template yang sedang merender
 *   memasang filter 'mea_router_use_slot' (single.php tema komunitasmea), kartu
 *   dicetak di slot tema; kalau tidak (tema lain, template Elementor Pro), kartu
 *   ditempel di akhir the_content.
 *
 * @package mea-router
 */

defined( 'ABSPATH' ) || exit;

/**
 * Apakah konten yang sedang difilter adalah isi artikel utama di halaman artikel.
 * Menolak excerpt, "baca juga", feed, REST, dan pemanggilan dari plugin SEO di <head>.
 */
function mea_router_is_main_article() {
	if ( is_admin() || is_feed() || ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return false;
	}
	if ( doing_action( 'wp_head' ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return false;
	}
	return (int) get_the_ID() === (int) get_queried_object_id() && ! post_password_required();
}

/**
 * Posisi tepat setelah </p> ke-N di tingkat atas konten.
 *
 * @param string $content HTML.
 * @param int    $n       Nomor paragraf.
 * @return array{0:int|false,1:int} [offset sisip, jumlah paragraf tingkat atas]
 */
function mea_router_paragraph_offset( $content, $n ) {
	$containers = 'blockquote|table|ul|ol|dl|div|figure|details|section|aside|pre|form|nav';
	if ( ! preg_match_all( '#<(/?)(p|' . $containers . ')\b[^>]*>#i', $content, $tags, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) ) {
		return array( false, 0 );
	}
	$depth  = 0;
	$count  = 0;
	$offset = false;
	foreach ( $tags as $tag ) {
		$closing = '/' === $tag[1][0];
		$name    = strtolower( $tag[2][0] );
		if ( 'p' === $name ) {
			if ( $closing && 0 === $depth ) {
				++$count;
				if ( $count === $n ) {
					$offset = $tag[0][1] + strlen( $tag[0][0] );
				}
			}
			continue;
		}
		if ( '/' === substr( $tag[0][0], -2, 1 ) ) {
			continue; // <div />.
		}
		$depth = max( 0, $depth + ( $closing ? -1 : 1 ) );
	}
	return array( $offset, $count );
}

/**
 * Sisipkan kartu mid (dan kartu end bila tema tidak punya slot).
 * Prioritas 20: sesudah do_blocks (9), wpautop (10), dan shortcode (11).
 */
add_filter(
	'the_content',
	function ( $content ) {
		static $done = false;
		if ( $done || ! mea_router_is_main_article() ) {
			return $content;
		}
		$post_id = (int) get_the_ID();
		$path    = mea_router_post_path( $post_id );
		if ( 'off' === $path ) {
			return $content;
		}
		$done = true;

		// Penempatan manual lewat shortcode menggantikan kartu mid otomatis.
		if ( ! has_shortcode( (string) get_post_field( 'post_content', $post_id ), 'mea_cta' ) ) {
			$settings          = mea_router_settings();
			$n                 = max( 1, (int) $settings['mid_after'] );
			list( $at, $total ) = mea_router_paragraph_offset( $content, $n );
			if ( false !== $at && $total >= $n + max( 0, (int) $settings['mid_min_rest'] ) ) {
				$content = substr( $content, 0, $at ) . "\n" . mea_router_render( $path, 'mid', $post_id ) . "\n" . substr( $content, $at );
			}
		}

		if ( ! apply_filters( 'mea_router_use_slot', false ) ) {
			$content .= "\n" . mea_router_render( $path, 'end', $post_id );
		}
		return $content;
	},
	20
);

/**
 * Slot tema: do_action( 'mea_router_slot', 'end' ) di single.php.
 */
add_action(
	'mea_router_slot',
	function ( $pos = 'end' ) {
		static $printed = array();
		if ( ! is_singular( 'post' ) || isset( $printed[ $pos ] ) || post_password_required() ) {
			return;
		}
		$post_id = (int) get_the_ID();
		if ( $post_id !== (int) get_queried_object_id() ) {
			return;
		}
		$path = mea_router_post_path( $post_id );
		if ( 'off' === $path ) {
			return;
		}
		$printed[ $pos ] = true;
		echo mea_router_render( $path, $pos, $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- di-escape di mea_router_render().
	}
);
