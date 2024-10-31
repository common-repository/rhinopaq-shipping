<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Add the rhinopaq id field to order page
function add_order_rhinopaq_id($order){

    // Check if order has rhinopaq before adding the id field
    if(check_cart_for_rhinopaq($order->get_id())){
        $args = array(
            'id' => '_rhinopaq_id',
            'name' => '_rhinopaq_id',
            'label' => 'rhinopaq ID:',
            'class' => '',
            'desc_tip' => false,
                'maxlength' => 29,
                'value' => $order->get_meta('_rhinopaq_id',true)
        );
        woocommerce_wp_text_input($args);
    }
    
}
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'add_order_rhinopaq_id', 10, 1 );

// Save the custom field value: _rhinopaq_id
function save_order_rhinopaq_id($order_id){
    $order = wc_get_order($order_id);
    if(isset($_POST['_rhinopaq_id'])){
        $order = wc_get_order($order_id);
        $order->update_meta_data( '_rhinopaq_id', $_POST['_rhinopaq_id'] );
        $order->save();
    }
}
add_action('woocommerce_order_status_changed', 'save_order_rhinopaq_id', 10, 1 );
add_action('woocommerce_order_status_completed', 'save_order_rhinopaq_id', 10, 1 );

// Send info to rhinopaq about used rhinopaq id
function send_order_rhinopaq_id($order_id){
    try{
        // Make sure we have a valid order id
        if (!$order_id) {
            return;
        }

        $contains_rhinopaq = check_cart_for_rhinopaq($order_id);

        // Only continue in case there is a rhinopaq in the cart
        if ($contains_rhinopaq) {
            // Check, if the rhinopaq id is set in the order admin area
            $order = wc_get_order($order_id);
            $package_id = $order->get_meta('_rhinopaq_id',true);

            if($package_id){
                // Send rhinopaq outgoing info about id
                wp_remote_get(RHINOPAQ_SERVICE_URL,array(
                    'body' => array(
                        'dialog' => 'rhinoOutgoing',
                        'pluginId' => get_option('rhinopaq-plugin-id','false'),
                        'orderId' => $order_id,
                        'packageId' => $package_id
                    )
                ));
                return true;
            }
        }
    } catch(Exception $e) {
        return false;
    }
	return false;
}
add_action('woocommerce_order_status_completed', 'send_order_rhinopaq_id', 20, 1 );

// Check cart for rhinopaq
function check_cart_for_rhinopaq($order_id){
    // Make sure we have a valid order id
    if (!$order_id) {
        return;
    }

    // Check if rhinopaq is in order 
    $order = wc_get_order($order_id);
    $product_id = get_option('rhinopaq-shipping-product-id', false);
    $contains_rhinopaq = false;

    foreach ($order->get_items() as $item_id => $item) {
        if ($item->get_product_id() == $product_id) {
            $contains_rhinopaq = true;
            break;
        }
    }

    return $contains_rhinopaq;
}


