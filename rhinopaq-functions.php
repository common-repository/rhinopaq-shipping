<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Get the correct internal rhinopaq product or variation id
function get_rhinopaq_id($variation_name = false) {
    if(!$variation_name){
        $product_id = get_option('rhinopaq-shipping-product-id',false);
        if($product_id) {
            return intval($product_id);
        } else {
            try{
                $product_id = create_virtual_rhinopaq()['product_id'];
                if (!$product_id) {
                    throw new Exception('Error on creating the new product.');
                }
            } catch(Exception $e) {
                rhinopaq_admin_notice(__('Creating a new product for rhinopaq did not work. Please <a href="https://www.rhinopaq.com/kontakt/" target="_blank">contact us</a> for support.','rhinopaq'));
            }
        }
        return intval($product_id);
    } else {
        switch($variation_name):
            case('rhino-b-35x20x5'):
                $variation_id = get_option('rhinopaq-shipping-rhino-b-35x20x5-id',false);
                break;
            case('rhino-b-25x25x10'):
                $variation_id = get_option('rhinopaq-shipping-rhino-b-25x25x10-id',false);
                break;
        endswitch;
        if($variation_id){
            return intval($variation_id);
        } else {
            try{
                throw new Exception('Error on creating the new variation.');
            } catch(Exception $e) {
                rhinopaq_admin_notice(__('Creating a new variant for rhinopaq did not work. Please <a href="https://www.rhinopaq.com/kontakt/" target="_blank">contact us</a> for support.','rhinopaq'));
            }
        }
    }
    return false;
}

// Function to check if the cart fits in one rhinopaq size
function rhinopaq_cart_fits($packages){
	if(get_option('rhinopaq-smart-enabled','no') == 'yes'){

		$fits = false;
		$packaging = null;
		$response = null;
		$length = 0;
		$width = 0;
		$height = 0;
		$weight = 0;
		$quantity = 0;
		$rhinoTypes = [];
		$rhinoTypes[] = get_option('rhinopaq-1-enabled','no') == "yes" ? 1 : 0;
		$rhinoTypes[] = get_option('rhinopaq-4-enabled','no') == "yes" ? 4 : 0;
		$pluginId = get_option('rhinopaq-plugin-id','false');
		$items = array();
		$clearance = get_option('rhinopaq-clearance',0);

		// Get relevant product data out of the package and push them into the items-array
		foreach ( $packages[0]['contents'] as $item_id => $values ) {
		  $data = $values['data'];
		  $quantity = $values['quantity'];
		  $dimensions = $data->get_dimensions(false);
		  $length = round(doubleval($dimensions['length']),2);
		  $width = round(doubleval($dimensions['width']),2);
		  $height = round(doubleval($dimensions['height']),2);
		  $weight = round(doubleval($data->get_weight()),2);
		  $items []= array($length, $width, $height, $weight, $quantity);
		}

		// Send cart data to service to decide, if shipping is possible with rhinopaq
		$endpoint_url = RHINOPAQ_SERVICE_URL;
		$dataArray = [
		  'dialog' => 'checkOutCheck',
		  'shopSystem' => 'woocommerce',
		  'pluginId' => $pluginId,
		  'requestId' => $pluginId.strval(time()),
		  'rhinoTypes' => json_encode($rhinoTypes),
		  'items' => json_encode($items),
		  'clearance' => $clearance];
		$response = wp_remote_get($endpoint_url, array('body'=>$dataArray));

		// Check if we got a valid response
		if (is_wp_error($response) || empty($response['response']) || $response['response']['code'] != '200') {
		  // Error
		  $fits = false;
		} else {
		  // Get the fitting result out of the answer
		  $result = json_decode($response['body']);
		  if($result && $result->fits == true){
			$fits = true;
			$packaging = $result->fittingRhinopaq;
		  }
		}

	} else {
		// In case smart rhinopaq is deactivated, this will be no questions to ask and it fits
		$fits = true;
	}

	WC()->session->set('rhinopaq-fitting',array(
		'fits' => $fits,
		'packaging' => $packaging,
		'variation-id' => get_rhinopaq_id($packaging)
	));
	
	return $fits;
}

// Adding a reusable package to the cart
function add_rhinopaq_to_cart() {

	$cart_item_key = null;
	$response = false;
	$variation_id = WC()->session->get('rhinopaq-fitting')['variation-id'];

	if($variation_id && $variation_id != false) {
		if(!is_rhinopaq_in_cart()){
			$cart_item_key = WC()->cart->add_to_cart(get_rhinopaq_id(),1,$variation_id,array(),array());

			if ($cart_item_key) {
				// Success on adding rhinopaq to the cart
				$response = array(
					'success' => true,
					'message' => 'Produkt wurde zum Warenkorb hinzugefügt.',
				);
			} else {
				// Error on adding rhinopaq to the cart
				$response = array(
					'success' => false,
					'message' => 'Fehler: Produkt konnte nicht zum Warenkorb hinzugefügt werden. Produkt existiert ggf. nicht oder ist nicht verfügbar.',
				);
			}
		} else {
			$response = array(
				'success' => false,
				'message' => 'rhinopaq schon im Warenkorb.',
			);
		}
	}
	return $response;
}

// Removing the reusable package from the cart
function remove_rhinopaq_from_cart() {

	$product_id = intval(get_option('rhinopaq-shipping-product-id',false));

	// Search the shopping cart for the product with the given product ID
	foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
		if ($cart_item['product_id'] === $product_id) {
			// Remove the product from the shopping cart
			WC()->cart->remove_cart_item($cart_item_key);
			break;
		}
	}
	return true;
}

// Checking if reusable is already in the cart
function is_rhinopaq_in_cart() {
    $product_id = get_option('rhinopaq-shipping-product-id',false);
	$cart_items = WC()->cart->get_cart();
	
	if($product_id != false) {
		// Look for the product
		foreach ($cart_items as $cart_item_key => $cart_item) {
			if ($cart_item['product_id'] == $product_id) {
				// rhinopaq already in the cart
				return true;
			}
		}
	}
    // rhinopaq not found
    return false;
}

// Check if the country is available for rhinopaq
function is_country_rhinopaq_available($shipping_country){
	if($shipping_country == 'DE' || $shipping_country == 'AT') {
		// rhinopaq is available in this country
		return true;
	}
	// rhinopaq is not available in this country
	return false;
}

