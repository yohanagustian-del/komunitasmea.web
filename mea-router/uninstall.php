<?php
/**
 * Uninstall bersih: hapus semua option dan post meta milik MEA Router.
 *
 * @package mea-router
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

foreach ( array( 'mea_router_settings', 'mea_router_popular_kreator', 'mea_router_popular_jualan' ) as $mea_router_option ) {
	delete_option( $mea_router_option );
}
delete_post_meta_by_key( '_mea_router_path' );
