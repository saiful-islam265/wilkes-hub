<?php
/**
 * Hoodsly Hub
 *
 * @link https://wppool.dev
 *
 * @package WordPress
 * @subpackage hoodsly_hub
 * @since hoodsly hub 1.0.0
 */

/**
 * Hoodsly Hub only works in WordPress 4.7 or later.
 */
require_once get_template_directory() . '/inc/user-access-control.php';
require get_template_directory() . '/inc/theme-setup.php';
require get_template_directory() . '/inc/theme-assets.php';
require get_template_directory() . '/inc/theme-functions.php';
require get_template_directory() . '/inc/order-list.php';
require get_template_directory() . '/inc/pagination.php';
require get_template_directory() . '/inc/class-hoodslyhub-helper.php';
require get_template_directory() . '/inc/ups-shipping-label-generator/ups-shipping-label-generator.php';
if ( is_admin() ) {
	//require_once get_template_directory() . '/inc/class-hood-species-stock-list.php';
}

if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
	add_filter( 'https_ssl_verify', '__return_false' );
}
