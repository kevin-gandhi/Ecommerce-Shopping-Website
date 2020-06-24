<?php
/**
 * Plugin Family Id: dangoodman/wc-weight-based-shipping
 * Plugin Name: WooCommerce Weight Based Shipping
 * Plugin URI: https://wordpress.org/plugins/weight-based-shipping-for-woocommerce/
 * Description: Simple yet flexible shipping method for WooCommerce.
 * Version: 5.3.4.2
 * Author: weightbasedshipping.com
 * Author URI: https://weightbasedshipping.com
 * Requires PHP: 5.3
 * Requires at least: 4.0
 * Tested up to: 5.4
 * WC requires at least: 2.3
 * WC tested up to: 4.2
 */

if (!class_exists('WbsVendors_DgmWpPluginBootstrapGuard', false)) {
    require_once(__DIR__ .'/server/vendor/dangoodman/wp-plugin-bootstrap-guard/DgmWpPluginBootstrapGuard.php');
}

WbsVendors_DgmWpPluginBootstrapGuard::checkPrerequisitesAndBootstrap(
    'WooCommerce Weight Based Shipping',
    '5.3', '4.0', '2.3',
    __DIR__ .'/bootstrap.php'
);