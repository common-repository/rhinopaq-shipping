<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Check for updates in the cart
function rhinopaq_cart_update() {

	// We only need to make adjustments in case rhinopaq is active and there was a pro reusable decision before
	if(get_option('rhinopaq-enabled','no') == 'yes' && WC()->session->get('rhinopaq-prev-decision') == 'yes'){
		
        global $woocommerce;
		$shipping_country = $woocommerce->customer->get_shipping_country();

		// Check the country, we still want rhinopaq & it is fitting 
		if(is_country_rhinopaq_available($shipping_country) && WC()->session->get('rhinopaq-prev-decision') == 'yes') {
			// rhinopaq is available in this country
			// Check, if there is already a rhinopaq in the cart and it still fits
			if(!is_rhinopaq_in_cart()){
				add_rhinopaq_to_cart();
			}
		} else {
			// rhinopaq is not available in this country
			remove_rhinopaq_from_cart();
		}
	}
}
add_action('woocommerce_cart_updated','rhinopaq_cart_update');

// Check if deleted item is rhinopaq product
function rhinopaq_on_remove_cart_item($cart_item_key, $cart) {
    $product_id = get_option('rhinopaq-shipping-product-id', false);

    // Check whether the deleted product is the rhinopaq product
    if (isset($cart->cart_contents[$cart_item_key]) && $cart->cart_contents[$cart_item_key]['product_id'] == $product_id) {
        // Set the session variable 'rhinopaq-prev-decision' to NULL
        WC()->session->set('rhinopaq-prev-decision', NULL);
    }
}
add_action('woocommerce_remove_cart_item', 'rhinopaq_on_remove_cart_item', 10, 2);