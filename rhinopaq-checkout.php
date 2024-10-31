<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Function to creat an initial pop up to ask for reusable packaging choice
function rhinopaq_checkout(){
	global $woocommerce;
	$packages = $woocommerce->shipping->packages;
	$shipping_country = $woocommerce->customer->get_shipping_country();

	// Internal function to assemble button styles
	function rhino_get_btn_style($btn_type, $btn_size, $btn_numb){
		
		$rhino_style_btn = "background:" . get_theme_mod('rhino_button_' . $btn_numb . '_bgcolor', '#333333').";";
		$rhino_style_btn .= " color:" . get_theme_mod('rhino_button_' . $btn_numb . '_color', '#ffffff').";";

		switch($btn_size):
			case "small":
				$rhino_style_btn .= " padding:5px 14px;";
				break;
			case "middle":
				$rhino_style_btn .= " padding:10px 28px;";
				break;
			default:
				$rhino_style_btn .= " padding:15px 42px;";
				break;
			endswitch;	

		switch($btn_type):
			case "rect":
				$rhino_style_btn .= " border-radius:0px;";
				break;
			case "text":
				$rhino_style_btn .= " text-decoration: underline; background: none; padding: 8px;";
				break;
			default:
				$rhino_style_btn .= " border-radius:100px;";
				break;
			endswitch;
			
			$rhino_style_btn .= " cursor: pointer;";

		return $rhino_style_btn;
	}

	if(get_option('rhinopaq-standard-shipping',false) == 'yes'){
		WC()->session->set('rhinopaq-prev-decision','yes');
	}
	$prev_decision = WC()->session->get('rhinopaq-prev-decision');

	// Check if we have packages to ship, shipping with rhinopaq is enabled && the amount of packages is > 0
	// If smart rhinopaq is not enabled, we don't need to check if dimensions fit into one rhinopaq
	// This pop up will only be display once, the decision will be stored in the WC session variable rhinopaq-prev-decision
	// In case there are further changes of the cart, we'll go via the hook woocommerce_cart_updated
	// 
	// IMPORTANT:
	// Recalculation after changes should only be done from the checkout - otherwise we have to many server requests to smart rhinopaq
	//
	// Add Country selection as well

	// Check, if we are in the customizer view to tunnel the request in this case
	$rhino_customizer_check = is_customize_preview() && get_theme_mod('rhino_preview_active', 'true');

	if($rhino_customizer_check || get_option('rhinopaq-enabled','no') == 'yes' && isset($packages) && count($packages) > 0 && is_country_rhinopaq_available($shipping_country) && !isset($prev_decision)):
		
		// Check if all cart items fit into a rhinopaq, if so, construct the pop up
		if($rhino_customizer_check || !is_customize_preview() && rhinopaq_cart_fits($packages)):

			// Construct the pop up element

			// Define buttons styles by selected button types in customizer settings
			$rhino_style_btn_1 = rhino_get_btn_style(get_theme_mod('rhino_button_1_type', 'pill'),get_theme_mod('rhino_button_size', 'middle'),1);
			$rhino_style_btn_2 = rhino_get_btn_style(get_theme_mod('rhino_button_2_type', 'pill'),get_theme_mod('rhino_button_size', 'middle'),2);
			?>
			<div id="rhinopaq-cont" class="rhinopop-container show">
			  <div class="rhinopop" style="background-color:<?php echo get_theme_mod('rhino_popup_bgcolor', '#ffffff'); ?>; border-radius:<?php echo get_theme_mod('rhino_popup_corner', '10'); ?>px;">
			  	<?php if(get_theme_mod('rhino_image_active', 'true')): ?>
					<div class="img">
						<img src="<?php echo wp_get_attachment_url(get_theme_mod('rhino_popup_image', get_option('woocommerce_placeholder_image',0))); ?>">
					</div>
				<?php endif; ?>
			    <div class="content" style="background-color:<?php echo get_theme_mod('rhino_popup_bgcolor', '#ffffff'); ?>;">
			      <h2 style="color:<?php echo get_theme_mod('rhino_popup_text_color', '#333333'); ?>;"><?php echo get_theme_mod('rhino_popup_title', 'Popup-Titel'); ?></h2>
			      <p style="color:<?php echo get_theme_mod('rhino_popup_text_color', '#333333'); ?>;"><?php echo get_theme_mod('rhino_popup_text', 'Popup-Text'); ?></p>
			      <form>
			        <button 
						id="rhinopaq-mwvp" 
						data-productid="<?php echo get_option('rhinopaq-shipping-product-id',false); ?>"
						style="<?php echo $rhino_style_btn_1; ?>">
							<?php echo get_theme_mod('rhino_button_1_text', 'Mehrweg'); ?>
					</button>
			        <button 
						id="rhinopaq-ewvp"
						style="<?php echo $rhino_style_btn_2; ?>">
							<?php echo get_theme_mod('rhino_button_2_text', 'Einweg'); ?>
					</button>
			      </form>
			      <span></span>
			    </div>
			  </div>
			</div>
			<?php

		endif;

	// Decision has been made before already, let's update the rhinopaq and cart
	elseif(get_option('rhinopaq-enabled','no') == 'yes' && isset($packages) && count($packages) > 0 && $prev_decision === 'yes'):

		// Check if all cart items fit into a rhinopaq; tunnel in case we are in customizer view
		if($rhino_customizer_check || !is_customize_preview() && rhinopaq_cart_fits($packages)):
			remove_rhinopaq_from_cart();
			add_rhinopaq_to_cart();
		else: 
			// remove reusable packaging, if there still is one since the cart does not fit
			remove_rhinopaq_from_cart();
		endif;
	endif;
}
add_action('woocommerce_after_checkout_form', 'rhinopaq_checkout');

// Ajax function to handle positiv decision on reusable packaging on front end
function pos_decision_rhino_add() {
	$response = null;
	
	// Set the session variable on yes
	WC()->session->set('rhinopaq-prev-decision', 'yes');
	
	// Send rhinopaq info popup analytics, 1 = true
	send_reuse_choice(1);

	// Add the reusable packaging product to the cart
    wp_send_json(add_rhinopaq_to_cart());

}
add_action('wp_ajax_pos_decision_rhino_add', 'pos_decision_rhino_add');
add_action('wp_ajax_nopriv_pos_decision_rhino_add', 'pos_decision_rhino_add');

// Ajax function to handle negative decision on reusable packaging on front end
function neg_decision_rhino_add() {
	$response = null;
	
	// Set the session variable on no
    WC()->session->set('rhinopaq-prev-decision', 'no');

	// Send rhinopaq info popup analytics, 0 = false
	send_reuse_choice(0);

	$response = array(
		'success' => true,
		'message' => 'Session-Variable erfolgreich auf "no" gesetzt.',
	);
    wp_send_json($response);

}
add_action('wp_ajax_neg_decision_rhino_add', 'neg_decision_rhino_add');
add_action('wp_ajax_nopriv_neg_decision_rhino_add', 'neg_decision_rhino_add');

// Function to send reuse choice on popup to rhinopaq
function send_reuse_choice($choice) {
	// Get further details about the choice
	$variation_id = WC()->session->get('rhinopaq-fitting')['variation-id'];
	$surcharge = get_post_meta($variation_id, '_regular_price', true);
	$cart_total = WC()->cart->get_total('edit');
	$surcharge_ratio = $surcharge/$cart_total;
	try{
		wp_remote_get(RHINOPAQ_SERVICE_URL,array(
			'body' => array(
				'dialog' => 'popupAnalytics',
				'pluginId' => get_option('rhinopaq-plugin-id','false'),
				'reuse' => $choice,
				'surcharge' => $surcharge,
				'surchargeRatio' => $surcharge_ratio
			)
		));
	} catch(Exception $e) {
		return false;
	}
	return true;
}


?>
