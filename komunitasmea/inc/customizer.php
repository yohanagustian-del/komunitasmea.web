<?php
/**
 * Pengaturan konten yang masih menunggu Yohan (Tampilan → Sesuaikan).
 *
 * Semua kosong secara bawaan: section Kegiatan dan Testimoni tersembunyi
 * sampai diisi, kontak footer dan FAQ no. 7 hanya tampil sebagai placeholder
 * untuk admin.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

/** Jumlah slot foto kegiatan dan testimoni. */
const KM_SLOTS = 6;

add_action(
	'customize_register',
	function ( WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_panel(
			'km_panel',
			array(
				'title'    => 'Komunitas MEA',
				'priority' => 30,
			)
		);

		/* Tentang komunitas + FAQ */
		$wp_customize->add_section(
			'km_home',
			array(
				'title'       => 'Homepage: teks',
				'panel'       => 'km_panel',
				'description' => 'Isi hanya dengan copy yang sudah disetujui Yohan. Jangan menulis angka selain 12.000+ kreator, 3.000+ seller, dan syarat MCN minimal 1.000 follower.',
			)
		);
		$wp_customize->add_setting( 'km_about_html', array( 'default' => '', 'sanitize_callback' => 'wp_kses_post' ) );
		$wp_customize->add_control(
			'km_about_html',
			array(
				'section'     => 'km_home',
				'type'        => 'textarea',
				'label'       => 'Tentang komunitas (copy final §3.2)',
				'description' => 'Boleh HTML sederhana; pisahkan paragraf dengan <p>. Kosong = draf bawaan dengan tanda "draf" untuk admin.',
			)
		);
		$wp_customize->add_setting( 'km_faq7_answer', array( 'default' => '', 'sanitize_callback' => 'sanitize_textarea_field' ) );
		$wp_customize->add_control(
			'km_faq7_answer',
			array(
				'section' => 'km_home',
				'type'    => 'textarea',
				'label'   => 'FAQ 7: Apakah gabung komunitas berbayar?',
			)
		);

		/* Kegiatan komunitas */
		$wp_customize->add_section(
			'km_kegiatan',
			array(
				'title'       => 'Homepage: foto kegiatan',
				'panel'       => 'km_panel',
				'description' => 'Hanya foto kegiatan asli dari tim MEA (bukan AI atau stok). Section tampil kalau minimal satu foto diisi. Teks alternatif diambil dari Media Library.',
			)
		);
		for ( $i = 1; $i <= KM_SLOTS; $i++ ) {
			$wp_customize->add_setting( "km_kegiatan_$i", array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
			$wp_customize->add_control(
				new WP_Customize_Media_Control(
					$wp_customize,
					"km_kegiatan_$i",
					array(
						'section'   => 'km_kegiatan',
						'label'     => "Foto $i",
						'mime_type' => 'image',
					)
				)
			);
		}

		/* Testimoni */
		$wp_customize->add_section(
			'km_testimoni',
			array(
				'title'       => 'Homepage: testimoni',
				'panel'       => 'km_panel',
				'description' => 'Hanya testimoni asli yang sudah diverifikasi (sumber: halaman Testimoni Mitra Kami, ID 50517, atau dari Yohan). Section tampil kalau minimal satu kutipan diisi.',
			)
		);
		for ( $i = 1; $i <= KM_SLOTS; $i++ ) {
			$wp_customize->add_setting( "km_testi_{$i}_quote", array( 'default' => '', 'sanitize_callback' => 'sanitize_textarea_field' ) );
			$wp_customize->add_control( "km_testi_{$i}_quote", array( 'section' => 'km_testimoni', 'type' => 'textarea', 'label' => "Testimoni $i: kutipan" ) );
			$wp_customize->add_setting( "km_testi_{$i}_name", array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
			$wp_customize->add_control( "km_testi_{$i}_name", array( 'section' => 'km_testimoni', 'type' => 'text', 'label' => "Testimoni $i: nama" ) );
			$wp_customize->add_setting( "km_testi_{$i}_role", array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
			$wp_customize->add_control( "km_testi_{$i}_role", array( 'section' => 'km_testimoni', 'type' => 'text', 'label' => "Testimoni $i: toko / akun / peran" ) );
		}

		/* Footer */
		$wp_customize->add_section(
			'km_footer',
			array(
				'title'       => 'Footer',
				'panel'       => 'km_panel',
				'description' => 'Isi kolom tautan footer lewat Tampilan → Menu (4 lokasi "Footer: …").',
			)
		);
		$wp_customize->add_setting( 'km_footer_contact', array( 'default' => '', 'sanitize_callback' => 'wp_kses_post' ) );
		$wp_customize->add_control(
			'km_footer_contact',
			array(
				'section'     => 'km_footer',
				'type'        => 'textarea',
				'label'       => 'Kontak',
				'description' => 'Misalnya email atau WhatsApp resmi. Boleh berisi tautan.',
			)
		);
		$wp_customize->add_setting( 'km_privacy_page_id', array( 'default' => 104983, 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control(
			'km_privacy_page_id',
			array(
				'section'     => 'km_footer',
				'type'        => 'dropdown-pages',
				'label'       => 'Halaman Kebijakan Privasi',
				'description' => 'Tautan hanya tampil kalau halaman ini sudah publish.',
			)
		);
	}
);

/**
 * Foto kegiatan yang sudah diisi.
 *
 * @return int[] ID attachment.
 */
function km_kegiatan_ids() {
	$ids = array();
	for ( $i = 1; $i <= KM_SLOTS; $i++ ) {
		$id = (int) get_theme_mod( "km_kegiatan_$i", 0 );
		if ( $id && wp_attachment_is_image( $id ) ) {
			$ids[] = $id;
		}
	}
	return $ids;
}

/**
 * Testimoni yang sudah diisi.
 *
 * @return array<int, array{quote:string,name:string,role:string}>
 */
function km_testimonials() {
	$out = array();
	for ( $i = 1; $i <= KM_SLOTS; $i++ ) {
		$quote = trim( (string) get_theme_mod( "km_testi_{$i}_quote", '' ) );
		if ( '' === $quote ) {
			continue;
		}
		$out[] = array(
			'quote' => $quote,
			'name'  => trim( (string) get_theme_mod( "km_testi_{$i}_name", '' ) ),
			'role'  => trim( (string) get_theme_mod( "km_testi_{$i}_role", '' ) ),
		);
	}
	return $out;
}
