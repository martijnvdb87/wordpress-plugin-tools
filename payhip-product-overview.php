<?php
/**
 * Plugin Name:       Payhip Product Overview
 * Plugin URI:        https://github.com/martijnvdb87/wp-plugin-payhip-product-overview
 * Description:       Generates an overview page of your Payhip products.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.0
 * Author:            Martijn van den Bosch
 * Author URI:        https://martijnvandenbosch.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       payhip-product-overview
 */

require_once __DIR__ . '/vendor/autoload.php';

$plugin = new Martijnvdb\PayhipProductOverview\Plugin([
    'name' => 'payhip-product-overview'
]);
$plugin->run();