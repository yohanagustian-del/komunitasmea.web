<?php
/**
 * Admin: Settings → MEA Router, meta box override per post, kolom "Jalur CTA".
 *
 * @package mea-router
 */

defined( 'ABSPATH' ) || exit;

/* ---------- Settings → MEA Router ---------- */

add_action(
	'admin_init',
	function () {
		register_setting(
			'mea_router',
			'mea_router_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'mea_router_sanitize_settings',
				'default'           => mea_router_default_settings(),
			)
		);
		foreach ( array( 'kreator', 'jualan' ) as $group ) {
			register_setting(
				'mea_router',
				'mea_router_popular_' . $group,
				array(
					'type'              => 'array',
					'sanitize_callback' => 'mea_router_sanitize_id_list',
					'default'           => mea_router_default_popular( $group ),
				)
			);
		}
	}
);

add_action(
	'admin_menu',
	function () {
		add_options_page( 'MEA Router', 'MEA Router', 'manage_options', 'mea-router', 'mea_router_settings_page' );
	}
);

add_filter(
	'plugin_action_links_' . plugin_basename( MEA_ROUTER_FILE ),
	function ( $links ) {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-general.php?page=mea-router' ) ) . '">Pengaturan</a>' );
		return $links;
	}
);

/**
 * @param mixed $value Daftar ID (string dipisah koma/spasi atau array).
 * @return int[]
 */
function mea_router_sanitize_id_list( $value ) {
	return array_values( array_unique( array_filter( wp_parse_id_list( is_array( $value ) ? $value : preg_split( '/[\s,]+/', (string) $value ) ) ) ) );
}

/**
 * @param mixed $input Data form.
 * @return array<string, mixed>
 */
function mea_router_sanitize_settings( $input ) {
	$defaults = mea_router_default_settings();
	$input    = is_array( $input ) ? $input : array();
	$out      = array();

	foreach ( array( 'cat_seller', 'cat_kreator', 'cat_ganda' ) as $key ) {
		$out[ $key ] = isset( $input[ $key ] ) ? mea_router_sanitize_id_list( $input[ $key ] ) : $defaults[ $key ];
	}
	$out['default_path'] = isset( $input['default_path'] ) && in_array( $input['default_path'], array( 'seller', 'kreator', 'ganda', 'off' ), true ) ? $input['default_path'] : $defaults['default_path'];
	$out['mid_after']    = isset( $input['mid_after'] ) ? max( 1, min( 20, absint( $input['mid_after'] ) ) ) : $defaults['mid_after'];
	$out['mid_min_rest'] = isset( $input['mid_min_rest'] ) ? min( 20, absint( $input['mid_min_rest'] ) ) : $defaults['mid_min_rest'];

	$out['dest'] = array();
	foreach ( $defaults['dest'] as $key => $url ) {
		$value               = isset( $input['dest'][ $key ] ) ? esc_url_raw( trim( (string) $input['dest'][ $key ] ), array( 'https', 'http' ) ) : '';
		$out['dest'][ $key ] = '' !== $value ? $value : $url;
	}
	$out['copy'] = array();
	foreach ( $defaults['copy'] as $key => $text ) {
		$value               = isset( $input['copy'][ $key ] ) ? sanitize_text_field( (string) $input['copy'][ $key ] ) : '';
		$out['copy'][ $key ] = '' !== $value ? $value : $text;
	}
	return $out;
}

/**
 * Nama kategori untuk daftar ID (supaya admin bisa memeriksa pemetaan).
 */
function mea_router_describe_terms( $ids ) {
	$names = array();
	foreach ( wp_parse_id_list( $ids ) as $id ) {
		$term    = get_term( $id, 'category' );
		$names[] = $term instanceof WP_Term ? sprintf( '%s (%d, %d post)', $term->name, $id, $term->count ) : sprintf( '<span style="color:#b32d2e">ID %d tidak ditemukan</span>', $id );
	}
	return implode( ' · ', $names );
}

/**
 * Pratinjau daftar "Paling banyak dibaca".
 */
function mea_router_describe_posts( $ids ) {
	$rows = array();
	foreach ( wp_parse_id_list( $ids ) as $i => $id ) {
		$status = get_post_status( $id );
		$title  = $status ? get_the_title( $id ) : '';
		if ( 'publish' === $status ) {
			$rows[] = sprintf( '%d. <a href="%s" target="_blank" rel="noopener">%s</a> <code>%d</code>', $i + 1, esc_url( get_permalink( $id ) ), esc_html( $title ), $id );
		} else {
			$rows[] = sprintf( '%d. <span style="color:#b32d2e">ID %d %s — dilewati di homepage</span>', $i + 1, $id, $status ? 'berstatus ' . esc_html( $status ) : 'tidak ditemukan' );
		}
	}
	return implode( '<br>', $rows );
}

/**
 * Halaman pengaturan.
 */
function mea_router_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$s        = mea_router_settings();
	$defaults = mea_router_default_settings();
	$name     = 'mea_router_settings';
	$copy     = array(
		'seller_eyebrow'  => 'Label kecil kartu seller',
		'seller_title'    => 'Judul kartu seller',
		'seller_text'     => 'Teks kartu seller (hanya kartu akhir)',
		'seller_button'   => 'Tombol utama → SkilSkul',
		'seller_ordal'    => 'Tautan sekunder → Ordal',
		'seller_agency'   => 'Tautan sekunder → MEA Agency',
		'kreator_eyebrow' => 'Label kecil kartu kreator',
		'kreator_title'   => 'Judul kartu kreator',
		'kreator_text'    => 'Teks kartu kreator (hanya kartu akhir)',
		'kreator_button'  => 'Tombol utama → Affiliate Akademi',
		'kreator_mcn'     => 'Tautan sekunder → Gabung MCN MEA',
		'kreator_meago'   => 'Tautan sekunder → MEAGO!',
	);
	$labels   = mea_router_dest_labels();
	?>
	<div class="wrap">
		<h1>MEA Router</h1>
		<p>CTA otomatis di setiap artikel (post): kartu ringkas setelah paragraf ke-<?php echo (int) $s['mid_after']; ?> dan kartu lengkap di akhir. UTM: <code>utm_source=komunitasmea&amp;utm_medium=referral&amp;utm_campaign=artikel_&lt;jalur&gt;&amp;utm_content=&lt;mid|end&gt;_&lt;post_id&gt;</code>. Event GA4: <code>mea_cta_click</code> (parameter <code>mea_dest</code>, <code>mea_path</code>, <code>mea_pos</code>).</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'mea_router' ); ?>

			<h2>Pemetaan kategori → jalur</h2>
			<p>ID kategori dipisah koma. Sub-kategori mewarisi jalur induknya kalau tidak dipetakan sendiri, dan sub-kategori lebih menentukan daripada induk. Post dengan kategori seller dan kreator sekaligus memakai <strong>ganda</strong>.</p>
			<table class="form-table" role="presentation">
				<?php foreach ( array( 'seller' => 'Seller', 'kreator' => 'Kreator', 'ganda' => 'Ganda (kreator dulu, seller kedua)' ) as $path => $title ) : ?>
					<tr>
						<th scope="row"><label for="mea-cat-<?php echo esc_attr( $path ); ?>"><?php echo esc_html( $title ); ?></label></th>
						<td>
							<input type="text" class="regular-text" id="mea-cat-<?php echo esc_attr( $path ); ?>" name="<?php echo esc_attr( $name ); ?>[cat_<?php echo esc_attr( $path ); ?>]" value="<?php echo esc_attr( implode( ', ', wp_parse_id_list( $s[ 'cat_' . $path ] ) ) ); ?>">
							<p class="description"><?php echo wp_kses_post( mea_router_describe_terms( $s[ 'cat_' . $path ] ) ); ?></p>
						</td>
					</tr>
				<?php endforeach; ?>
				<tr>
					<th scope="row"><label for="mea-default-path">Kategori tidak terpetakan</label></th>
					<td>
						<select id="mea-default-path" name="<?php echo esc_attr( $name ); ?>[default_path]">
							<?php foreach ( array( 'ganda', 'seller', 'kreator', 'off' ) as $opt ) : ?>
								<option value="<?php echo esc_attr( $opt ); ?>" <?php selected( $s['default_path'], $opt ); ?>><?php echo esc_html( 'off' === $opt ? 'off (tanpa CTA)' : $opt ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			</table>

			<h2>Posisi</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="mea-mid-after">Kartu ringkas setelah paragraf ke-</label></th>
					<td><input type="number" min="1" max="20" id="mea-mid-after" name="<?php echo esc_attr( $name ); ?>[mid_after]" value="<?php echo (int) $s['mid_after']; ?>" class="small-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="mea-mid-rest">Minimal paragraf sesudahnya</label></th>
					<td><input type="number" min="0" max="20" id="mea-mid-rest" name="<?php echo esc_attr( $name ); ?>[mid_min_rest]" value="<?php echo (int) $s['mid_min_rest']; ?>" class="small-text">
					<p class="description">Artikel yang terlalu pendek hanya mendapat kartu akhir.</p></td>
				</tr>
			</table>

			<h2>Copy CTA</h2>
			<p>Kosongkan untuk kembali ke teks bawaan. Sapaan: seller "Anda", kreator "kamu".</p>
			<table class="form-table" role="presentation">
				<?php foreach ( $copy as $key => $title ) : ?>
					<tr>
						<th scope="row"><label for="mea-copy-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $title ); ?></label></th>
						<td><input type="text" class="large-text" id="mea-copy-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $name ); ?>[copy][<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $s['copy'][ $key ] ); ?>" placeholder="<?php echo esc_attr( $defaults['copy'][ $key ] ); ?>"></td>
					</tr>
				<?php endforeach; ?>
			</table>

			<h2>URL tujuan</h2>
			<p>UTM ditambahkan otomatis. Pertahankan <code>ref=AFAKADEMI-2992</code> di URL Affiliate Akademi.</p>
			<table class="form-table" role="presentation">
				<?php foreach ( $defaults['dest'] as $key => $url ) : ?>
					<tr>
						<th scope="row"><label for="mea-dest-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $labels[ $key ] ); ?></label></th>
						<td><input type="url" class="large-text" id="mea-dest-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $name ); ?>[dest][<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $s['dest'][ $key ] ); ?>" placeholder="<?php echo esc_attr( $url ); ?>"></td>
					</tr>
				<?php endforeach; ?>
			</table>

			<h2>Homepage: Paling banyak dibaca</h2>
			<p>6 ID post per kolom, urut dari klik Google Search Console tertinggi. Diperbarui MEA SEO Room tiap audit bulanan. Post yang tidak publish dilewati otomatis.</p>
			<table class="form-table" role="presentation">
				<?php foreach ( array( 'kreator' => 'Untuk kreator', 'jualan' => 'Untuk jualan' ) as $group => $title ) : ?>
					<?php $ids = get_option( 'mea_router_popular_' . $group, mea_router_default_popular( $group ) ); ?>
					<tr>
						<th scope="row"><label for="mea-pop-<?php echo esc_attr( $group ); ?>"><?php echo esc_html( $title ); ?></label></th>
						<td>
							<input type="text" class="large-text" id="mea-pop-<?php echo esc_attr( $group ); ?>" name="mea_router_popular_<?php echo esc_attr( $group ); ?>" value="<?php echo esc_attr( implode( ', ', wp_parse_id_list( $ids ) ) ); ?>">
							<p class="description"><?php echo wp_kses_post( mea_router_describe_posts( $ids ) ); ?></p>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/* ---------- Meta box override per post ---------- */

add_action(
	'add_meta_boxes_post',
	function () {
		add_meta_box( 'mea-router-path', 'MEA Router: jalur CTA', 'mea_router_meta_box', 'post', 'side', 'default' );
	}
);

/**
 * @param WP_Post $post Post.
 */
function mea_router_meta_box( $post ) {
	$current = get_post_meta( $post->ID, '_mea_router_path', true );
	$current = $current ? $current : 'auto';
	$auto    = mea_router_auto_path( $post->ID );
	wp_nonce_field( 'mea_router_path', 'mea_router_path_nonce' );
	echo '<label class="screen-reader-text" for="mea-router-path-select">Jalur CTA</label>';
	echo '<select id="mea-router-path-select" name="mea_router_path" style="width:100%">';
	foreach ( array(
		'auto'    => 'Otomatis (dari kategori: ' . $auto . ')',
		'seller'  => 'Seller',
		'kreator' => 'Kreator',
		'ganda'   => 'Ganda',
		'off'     => 'Off (tanpa CTA)',
	) as $value => $label ) {
		printf( '<option value="%s" %s>%s</option>', esc_attr( $value ), selected( $current, $value, false ), esc_html( $label ) );
	}
	echo '</select>';
	echo '<p class="description">Jalur otomatis dihitung ulang dari kategori saat post disimpan.</p>';
}

add_action(
	'save_post_post',
	function ( $post_id ) {
		if ( ! isset( $_POST['mea_router_path_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mea_router_path_nonce'] ) ), 'mea_router_path' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$value = isset( $_POST['mea_router_path'] ) ? sanitize_key( wp_unslash( $_POST['mea_router_path'] ) ) : 'auto';
		if ( in_array( $value, array( 'seller', 'kreator', 'ganda', 'off' ), true ) ) {
			update_post_meta( $post_id, '_mea_router_path', $value );
		} else {
			delete_post_meta( $post_id, '_mea_router_path' );
		}
	}
);

/* ---------- Kolom "Jalur CTA" di daftar post ---------- */

add_filter(
	'manage_post_posts_columns',
	function ( $columns ) {
		$columns['mea_router'] = 'Jalur CTA';
		return $columns;
	}
);

add_action(
	'manage_post_posts_custom_column',
	function ( $column, $post_id ) {
		if ( 'mea_router' !== $column ) {
			return;
		}
		$override = get_post_meta( $post_id, '_mea_router_path', true );
		echo esc_html( mea_router_post_path( $post_id ) . ( $override ? ' (manual)' : '' ) );
	},
	10,
	2
);
