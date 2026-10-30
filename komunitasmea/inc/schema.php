<?php
/**
 * Schema FAQPage — satu-satunya schema yang dicetak tema, hanya di homepage.
 * Title, meta, OG, canonical, dan schema lain diserahkan ke Rank Math.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_head',
	function () {
		if ( ! is_front_page() || ! apply_filters( 'komunitasmea_faq_schema', true ) ) {
			return;
		}
		$entities = array();
		foreach ( km_faq_items() as $item ) {
			$answer = trim( wp_strip_all_tags( $item['a'] ) );
			if ( '' === $answer ) {
				continue; // Jawaban belum ada: jangan masuk schema.
			}
			$entities[] = array(
				'@type'          => 'Question',
				'name'           => wp_strip_all_tags( $item['q'] ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $answer,
				),
			);
		}
		if ( ! $entities ) {
			return;
		}
		$data = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'@id'        => home_url( '/#faq' ),
			'mainEntity' => $entities,
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . "</script>\n";
	},
	30
);
