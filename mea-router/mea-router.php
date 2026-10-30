<?php
/**
 * Plugin Name:       MEA Router
 * Plugin URI:        https://www.komunitasmea.web.id/
 * Description:       CTA otomatis di setiap artikel ke jalur konversi yang tepat (seller, kreator, atau ganda) dengan UTM konsisten dan event GA4 mea_cta_click.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            PT MEA Digital Marketing
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mea-router
 *
 * @package mea-router
 */

defined( 'ABSPATH' ) || exit;

define( 'MEA_ROUTER_VERSION', '1.0.0' );
define( 'MEA_ROUTER_FILE', __FILE__ );
define( 'MEA_ROUTER_DIR', plugin_dir_path( __FILE__ ) );
define( 'MEA_ROUTER_URL', plugin_dir_url( __FILE__ ) );

require MEA_ROUTER_DIR . 'includes/settings.php';
require MEA_ROUTER_DIR . 'includes/router.php';
require MEA_ROUTER_DIR . 'includes/render.php';
require MEA_ROUTER_DIR . 'includes/content.php';

if ( is_admin() ) {
	require MEA_ROUTER_DIR . 'includes/admin.php';
}

register_activation_hook(
	__FILE__,
	function () {
		add_option( 'mea_router_settings', mea_router_default_settings() );
		add_option( 'mea_router_popular_kreator', mea_router_default_popular( 'kreator' ) );
		add_option( 'mea_router_popular_jualan', mea_router_default_popular( 'jualan' ) );
	}
);
