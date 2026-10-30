<?php
/**
 * Header situs.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main">Langsung ke konten</a>
<header class="site-header">
	<div class="wrap site-header__in">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"<?php echo is_front_page() ? ' aria-current="page"' : ''; ?>><i aria-hidden="true"></i>Komunitas MEA</a>
		<nav class="site-nav" aria-label="Menu utama">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => 'km_primary_menu_fallback',
				)
			);
			?>
		</nav>
	</div>
</header>
