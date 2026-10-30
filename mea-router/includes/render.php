<?php
/**
 * HTML kartu CTA, UTM builder, aset, dan shortcode [mea_cta].
 *
 * @package mea-router
 */

defined( 'ABSPATH' ) || exit;

/**
 * UTM artikel: utm_campaign=artikel_<path>&utm_content=<pos>_<post_id>.
 * Query yang sudah ada di URL tujuan (mis. ref=AFAKADEMI-2992) dipertahankan.
 *
 * @param string $url     URL tujuan.
 * @param string $path    seller|kreator|ganda.
 * @param string $pos     mid|end.
 * @param int    $post_id ID post.
 * @return string URL mentah (belum di-escape).
 */
function mea_router_utm( $url, $path, $pos, $post_id ) {
	if ( '' === $url ) {
		return '';
	}
	return add_query_arg(
		array(
			'utm_source'   => 'komunitasmea',
			'utm_medium'   => 'referral',
			'utm_campaign' => 'artikel_' . $path,
			'utm_content'  => $pos . '_' . (int) $post_id,
		),
		$url
	);
}

/**
 * Satu tautan CTA dengan atribut pelacakan.
 *
 * @param string $dest  Kunci tujuan.
 * @param string $inner HTML isi tautan (sudah aman).
 * @param array  $ctx   path, pos, post_id.
 * @param string $class Kelas CSS.
 */
function mea_router_link( $dest, $inner, $ctx, $class ) {
	$url = mea_router_utm( mea_router_dest_url( $dest ), $ctx['path'], $ctx['pos'], $ctx['post_id'] );
	if ( '' === $url ) {
		return '';
	}
	return sprintf(
		'<a class="%1$s" href="%2$s" target="_blank" rel="noopener" data-mea-dest="%3$s" data-mea-path="%4$s" data-mea-pos="%5$s">%6$s<span class="screen-reader-text"> (buka tab baru)</span></a>',
		esc_attr( $class ),
		esc_url( $url ),
		esc_attr( $dest ),
		esc_attr( $ctx['path'] ),
		esc_attr( $ctx['pos'] ),
		$inner
	);
}

/**
 * Tautan sekunder "Pertanyaan? → Merek".
 */
function mea_router_secondary( $dest, $question, $ctx ) {
	$labels = mea_router_dest_labels();
	$inner  = esc_html( $question ) . ' <span aria-hidden="true">→</span> <strong>' . esc_html( $labels[ $dest ] ) . '</strong>';
	return mea_router_link( $dest, $inner, $ctx, 'mea-card__link' );
}

/**
 * Satu kartu jalur.
 *
 * @param string $side    seller|kreator (isi kartu).
 * @param array  $ctx     path (jalur artikel, untuk UTM), pos, post_id.
 * @param bool   $compact Kartu ringkas (mid) tanpa paragraf penjelasan.
 */
function mea_router_card( $side, $ctx, $compact ) {
	if ( 'seller' === $side ) {
		$primary   = 'skilskul';
		$secondary = array(
			array( 'ordal', mea_router_copy( 'seller_ordal' ) ),
			array( 'meagency', mea_router_copy( 'seller_agency' ) ),
		);
	} else {
		$primary   = 'affiliateakademi';
		$secondary = array(
			array( 'meamcn', mea_router_copy( 'kreator_mcn' ) ),
			array( 'meago', mea_router_copy( 'kreator_meago' ) ),
		);
	}

	$html  = '<div class="mea-card mea-card--' . esc_attr( $side ) . '">';
	$html .= '<p class="mea-card__eyebrow">' . esc_html( mea_router_copy( $side . '_eyebrow' ) ) . '</p>';
	$html .= '<p class="mea-card__title">' . esc_html( mea_router_copy( $side . '_title' ) ) . '</p>';
	if ( ! $compact ) {
		$html .= '<p class="mea-card__text">' . esc_html( mea_router_copy( $side . '_text' ) ) . '</p>';
	}
	$html .= mea_router_link( $primary, esc_html( mea_router_copy( $side . '_button' ) ), $ctx, 'mea-card__btn' );
	$html .= '<ul class="mea-card__more">';
	foreach ( $secondary as $item ) {
		if ( '' !== $item[1] ) {
			$html .= '<li>' . mea_router_secondary( $item[0], $item[1], $ctx ) . '</li>';
		}
	}
	$html .= '</ul></div>';
	return $html;
}

/**
 * Blok CTA lengkap untuk satu posisi.
 *
 * @param string $path    seller|kreator|ganda.
 * @param string $pos     mid|end.
 * @param int    $post_id ID post (untuk utm_content).
 * @return string HTML.
 */
function mea_router_render( $path, $pos, $post_id ) {
	if ( ! in_array( $path, mea_router_paths(), true ) ) {
		return '';
	}
	$pos     = 'mid' === $pos ? 'mid' : 'end';
	$compact = 'mid' === $pos;
	$ctx     = array(
		'path'    => $path,
		'pos'     => $pos,
		'post_id' => (int) $post_id,
	);
	// Ganda: kreator dulu (kiri), seller kedua.
	$sides = 'ganda' === $path ? array( 'kreator', 'seller' ) : array( $path );
	$label = 'ganda' === $path ? 'Langkah berikutnya untuk kreator dan seller' : ( 'seller' === $path ? 'Langkah berikutnya untuk seller' : 'Langkah berikutnya untuk kreator' );

	$html = sprintf(
		'<aside class="mea-cta mea-cta--%1$s mea-cta--%2$s%3$s" aria-label="%4$s">',
		esc_attr( $pos ),
		esc_attr( $path ),
		count( $sides ) > 1 ? ' mea-cta--duo' : '',
		esc_attr( $label )
	);
	foreach ( $sides as $side ) {
		$html .= mea_router_card( $side, $ctx, $compact );
	}
	$html .= '</aside>';

	mea_router_enqueue_style();
	return apply_filters( 'mea_router_cta_html', $html, $path, $pos, $post_id );
}

/**
 * CSS kartu. Dipanggil saat kartu benar-benar dirender (artikel atau shortcode).
 */
function mea_router_enqueue_style() {
	if ( ! wp_style_is( 'mea-router', 'enqueued' ) ) {
		wp_enqueue_style( 'mea-router', MEA_ROUTER_URL . 'assets/mea-router.css', array(), MEA_ROUTER_VERSION );
	}
}

add_action(
	'wp_enqueue_scripts',
	function () {
		// Kartu artikel: muat CSS di <head> supaya tidak ada lompatan layout.
		if ( is_singular( 'post' ) && 'off' !== mea_router_post_path( get_queried_object_id() ) ) {
			mea_router_enqueue_style();
		}
		// Listener klik (< 1 KB) di semua halaman: tema juga memberi data-mea-* di hero, tangga, dan footer.
		wp_enqueue_script(
			'mea-router',
			MEA_ROUTER_URL . 'assets/mea-router.js',
			array(),
			MEA_ROUTER_VERSION,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
);

/**
 * [mea_cta path="seller|kreator|ganda" pos="end|mid"]
 * Tanpa path: jalur otomatis post saat ini (atau ganda di luar post).
 */
add_shortcode(
	'mea_cta',
	function ( $atts ) {
		$atts    = shortcode_atts(
			array(
				'path' => '',
				'pos'  => 'end',
			),
			$atts,
			'mea_cta'
		);
		$post_id = (int) get_the_ID();
		$path    = sanitize_key( $atts['path'] );
		if ( ! in_array( $path, mea_router_paths(), true ) ) {
			$path = $post_id && 'post' === get_post_type( $post_id ) ? mea_router_post_path( $post_id ) : 'ganda';
		}
		if ( 'off' === $path ) {
			return '';
		}
		return mea_router_render( $path, sanitize_key( $atts['pos'] ), $post_id );
	}
);
