<?php
/**
	* Plugin Name: rhinopaq Shipping
	* Description: Integration of rhinopaq reusable packaging as a shipping method
	* Version: 1.2.0
	* Author: rhinopaq
	* Author URI: https://www.rhinopaq.com
	* WC requires at least: 3.0
	* WC tested up to: 8.4
	* Requires at least: 4.6
	* Domain Path: /languages
	*
	* rhinopaq Shipping is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	* GNU General Public License for more details.
	*
	* Copyright 2023 rhinopaq
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Check if WooCommerce is active
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	// Define url
	$rhinoPluginURL = plugin_dir_url(__FILE__);
	define('RHINOPAQ_PLUGIN_URL',$rhinoPluginURL);
	define('RHINOPAQ_SERVICE_URL','https://nmrnonwxu4vttk3yczj46sbvnq0zksag.lambda-url.eu-central-1.on.aws/');

	// Load translation
	function rhinopaq_load_textdomain() {
	  load_plugin_textdomain( 'rhinopaq', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}
	add_action( 'init', 'rhinopaq_load_textdomain' );

	include 'rhinopaq-config.php';
	include 'rhinopaq-functions.php';
	include 'rhinopaq-checkout.php';
	include 'rhinopaq-cart-update.php';
	include 'rhinopaq-order.php';
	include 'rhinopaq-customizer.php';

	// Get rhinopaq scripts
	function rhinopaq_popup_scripts() {
		wp_register_script('rhinopaq_popup_script', plugin_dir_url(__FILE__).'popup/rhinopaq-popup.js', array(), '1.2.0', true);
		wp_enqueue_script('rhinopaq_popup_script');
		wp_localize_script('rhinopaq_popup_script','rhinopaq_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		return;
	}
	add_action( 'wp_enqueue_scripts' , 'rhinopaq_popup_scripts' );

	// Get rhinopaq style sheet
	function rhinopaq_popup_style() {
		wp_register_style('rhinopaq_popup_style', plugin_dir_url(__FILE__).'popup/rhinopaq-popup.css', null, '1.2.0', false);
		wp_enqueue_style('rhinopaq_popup_style');
		return;
	}
	add_action( 'wp_enqueue_scripts' , 'rhinopaq_popup_style' );
	add_action( 'admin_enqueue_scripts' , 'rhinopaq_popup_style' );

	// Create an initial product for reusable packaging
	function rhinopaq_initial_product_id() {
		// Check, if the product has been created already
		if (!get_option('rhinopaq-shipping-product-id',false)) {

			// Create the product
			$rhinopaq_product = create_virtual_rhinopaq();
			update_option('rhinopaq-shipping-product-id',$rhinopaq_product['product_id']);
			$rhinopaq_variation_ids = $rhinopaq_product['variation_ids'];
			foreach ($rhinopaq_variation_ids as $key => $id) {
				update_option('rhinopaq-shipping-'.$key.'-id',$id);
			}

		}
	}
	register_activation_hook(__FILE__, 'rhinopaq_initial_product_id');

	// Uninstall routine
	function rhinopaq_uninstall() {
		// Delete the rhinopaq product
		$product_id = get_option('rhinopaq-shipping-product-id', false);
		if($product_id){
			wp_delete_post($product_id, true);
		}
		// Delete database entries
		global $wpdb;
		$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '%rhinopaq%'");
		$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'rhinopaq-%'");
	}
	register_uninstall_hook(__FILE__, 'rhinopaq_uninstall');
	
}

?>
