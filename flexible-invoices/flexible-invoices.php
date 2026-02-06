<?php
/**
 * Plugin Name: Flexible Invoices for WooCommerce and WordPress
 * Plugin URI: https://wordpress.org/plugins/flexible-invoices/
 * Description: Flexible Invoices for WooCommerce and WordPress made simple. Available <a href="https://www.flexibleinvoices.com/sk/flexible-invoices-plugin-desc" target="_blank">PRO extension</a> with automations and different types of documents.
 * Version: 6.2.5
 * Author: WP Desk
 * Author URI: https://www.flexibleinvoices.com/sk/flexible-invoices-author
 * Text Domain: flexible-invoices
 * Domain Path: /lang/
 * Requires at least: 6.4
 * Tested up to: 6.9
 * WC requires at least: 10.0
 * WC tested up to: 10.4
 * Requires PHP: 7.4
 *
 * Copyright 2020 WP Desk Ltd.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package Flexible Invoices
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/* THESE TWO VARIABLES CAN BE CHANGED AUTOMATICALLY */
$plugin_version = '6.2.5';
$plugin_release_timestamp = '2023-11-27 10:37';

$plugin_name        = 'Flexible Invoices for WooCommerce and WordPress';
$plugin_class_name  = '\WPDesk\FlexibleInvoices\PluginFactory';
$plugin_text_domain = 'flexible-invoices';
$product_id         = 'Flexible Invoices for WooCommerce and WordPress';
$plugin_file        = __FILE__;
$plugin_dir         = dirname( __FILE__ );

$requirements = [
	'php'     => '7.3',
	'wp'      => '6.0',
	'modules' => [
		[
			'name'      => 'zip',
			'nice_name' => 'Zip'
		]
	]
];

require __DIR__ . '/vendor_prefixed/wpdesk/wp-plugin-flow-common/src/plugin-init-php52-free.php';

if ( ! function_exists( 'flexible_invoices_deactivation_translate' ) ) {
	register_deactivation_hook( $plugin_file, 'flexible_invoices_deactivation_translate' );
	function flexible_invoices_deactivation_translate() {
		\WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\Translator::reset_translations();
	}
}

/**
 * Add option flag to ensure rewrite rules flush
 */
register_activation_hook( __FILE__, function (){
	update_option( \WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\PostType\TemplatesPostType::FLUSH_REWRITE_RULES_OPTION, true );
} );

/**
 * Flush rewrite rules after deactivating Flexible Invoices
 */
register_deactivation_hook( __FILE__, function (){
	flush_rewrite_rules();
} );

