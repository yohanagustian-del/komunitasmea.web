<?php
/**
 * Form pencarian.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

$km_id = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $km_id ); ?>">Cari artikel</label>
	<input type="search" id="<?php echo esc_attr( $km_id ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="Cari artikel, mis. bio TikTok affiliate">
	<button type="submit" class="btn">Cari</button>
</form>
