<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Settings_Tab_rhinopaq {

    /**
     * Bootstraps the class and hooks required actions & filters.
     */
    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_settings_tab_rhinopaq', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_settings_tab_rhinopaq', __CLASS__ . '::update_settings' );
        add_action( 'admin_notices', __CLASS__ . '::display_admin_notices' );
    }
    
    /**
     * Add a new settings tab to the WooCommerce settings tabs array.
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['settings_tab_rhinopaq'] = 'rhinopaq';
        return $settings_tabs;
    } 

    /**
     * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab() {

        $settings = self::get_settings();
    
        echo '<div class="rhinopaq-settings-wrap">';
        woocommerce_admin_fields( $settings );
        echo '</div>';
    }

    /**
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }

    /**
     * Display admin notices if required fields are not set
     */
    public static function display_admin_notices() {
        if ('yes' === get_option('rhinopaq-smart-enabled', 'no') && empty(get_option('rhinopaq-plugin-id', ''))) {
            ?>
            <div class="notice notice-warning">
                <p><?php __('Please enter your rhinopaq Plugin ID to use the smart rhinopaq function. <a href=\"https://www.rhinopaq.com/kontakt\" target=\"_blank\">Contact us</a> if you do not have an ID or need assistance with setting up the plugin.', 'rhinopaq'); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
    public static function get_settings() {
        $product_id = get_option('rhinopaq-shipping-product-id',false);
        $product_edit_link = get_edit_post_link($product_id);
        $img_url = RHINOPAQ_PLUGIN_URL.'/assets/images/screen-settings.png';
        $customizer_section_url = add_query_arg('autofocus[section]', 'rhinopaq_custom_settings', admin_url('customize.php'));

        if($product_id) {
            $s2_field_4_text = 
                sprintf('<br><img class="rhinopaq-settings-image" src="%s"><br>',$img_url).
                '<ol class="rhinopaq-settings-image-list">
                <li>'.sprintf(__('<strong>Pop-up</strong>: For customization take a look in the Wordpress customizer in the <a href="%s" target="_blank">rhinopaq section</a>.','rhinopaq'),$customizer_section_url).'</li>
                <li>'.__('<strong>Product image</strong>: The small image in the checkout for the position of the reusable packaging corresponds to the product photo.','rhinopaq').'</li>
                <li>'.__('<strong>Product title</strong>: The title for the reusable packaging position also corresponds to the title of the product (same field as for the title of the pop-up).','rhinopaq').'</li>
                <li>'.sprintf(__('<strong>Surcharge</strong>: The price per use can be customized via the created <a href="%s" target="_blank">product</a>. It is the regular product price. Here you can choose 0,00 € or a surcharge.','rhinopaq'),$product_edit_link).'</li>
                </ol>';
        } else {
            $s2_field_4_text = __('The product could not be found. Please <a href="https://www.rhinopaq.com/kontakt/" target="_blank">contact us</a> for support.','rhinopaq');
        }
        
        $settings = array(
        // Section 1 ////// General settings
        's1-field-1' => array(
            'type' => 'title',
            'id' => 's1-field-1',
            'title' => __( 'rhinopaq Settings', 'rhinopaq' ),
            'desc' => __('On this page the essential settings for the rhinopaq Shipping Plugin can be set. rhinopaq is only available in Germany and Austria by default and is only offered to users from these countries.', 'rhinopaq' ),
        ),
        's1-field-2' => array(
            'type' => 'checkbox',
            'id' => 'rhinopaq-enabled',
            'desc' => __( 'Enable this packaging method for reusable packaging by rhinopaq.', 'rhinopaq' ),
            'default' => 'yes',
        ), 
        's1-sectionend' => array(
            'type' => 'sectionend',
            'id' => 's1-sectionend',
        ),
        // Section 2 ////// Check-Out settings
        's2-field-1' => array(
            'type' => 'title',
            'id' => 's2-field-1',
            'title' => __('Check-Out Settings', 'rhinopaq'),
            'desc' => __('Check-Out Settings of this plugin.', 'rhinopaq'),
        ),
        's2-field-2' => array(
            'type' => 'number',
            'id' => 'rhinopaq-shipping-product-id',
            'class' => 's2-fields all-fields rhinopaq-settings-note',
            'desc' => __( 'Generated automatically. The product ID of the reusable shipping packaging. Adjust only if necessary.', 'rhinopaq' ),
            'default' => get_rhinopaq_id(),
        ),
        's2-field-3' => array(
            'type' => 'checkbox',
            'id' => 'rhinopaq-standard-shipping',
            'class' => 's2-fields all-fields rhino-checkboxes',
            'desc' => __( 'Enable rhinopaq as a standard. There will be no pop up during the checkout. rhinopaq will always be added for reusable packaging.', 'rhinopaq' ),
            'default' => 'no',
        ),
        's2-field-4' => array(
            'type' => 'info',
            'id' => 's2-field-4',
            'text' => $s2_field_4_text, 
        ),
        's2-sectionend' => array(
            'type' => 'sectionend',
            'id' => 's2-sectionend',
        ),
        // Section 3 ////// Packaging sizes
        's3-field-1' => array(
            'type' => 'title',
            'id' => 's3-field-1',
            'title' => __('Packaging Settings', 'rhinopaq'),
            'desc' => __('Select the rhinopaq packaging sizes that you currently hold and offer.', 'rhinopaq'),
        ),
        's3-field-2' => array(
            'type' => 'checkbox',
            'id' => 'rhinopaq-4-enabled',
            'class' => 's3-fields all-fields rhino-checkboxes',
            'desc' => __( '35 x 20 x 5 cm (rhino-b-35x20x5)', 'rhinopaq' ),
            'default' => 'no',
        ),
        's3-field-3' => array(
            'type' => 'number',
            'id' => 'rhinopaq-shipping-rhinob35x20x5-id',
            'class' => 's3-fields all-fields rhinopaq-settings-note',
            'desc' => __( 'Generated automatically. The variant ID of the reusable shipping packaging (rhino-b-35x20x5). Adjust only if necessary.', 'rhinopaq' ),
            'default' => get_option('rhinopaq-shipping-rhino-b-35x20x5-id',false),
        ),
        's3-field-4' => array(
            'type' => 'checkbox',
            'id' => 'rhinopaq-1-enabled',
            'class' => 's3-fields all-fields rhino-checkboxes',
            'desc' => __( '25 x 25 x 10 cm (rhino-b-25x25x10)', 'rhinopaq' ),
            'default' => 'no',
        ),
        's3-field-5' => array(
            'type' => 'number',
            'id' => 'rhinopaq-shipping-rhinob25x25x10-id',
            'class' => 's3-fields all-fields rhinopaq-settings-note',
            'desc' => __( 'Generated automatically. The variant ID of the reusable shipping packaging (rhino-b-25x25x10). Adjust only if necessary.', 'rhinopaq' ),
            'default' => get_option('rhinopaq-shipping-rhino-b-25x25x10-id',false),
        ),
        's3-sectionend' => array(
            'type' => 'sectionend',
            'id' => 's3-sectionend',
        ),
        // Section 4 ////// Smart rhino settings
        's4-field-1' => array(
            'type' => 'title',
            'id' => 's4-field-1',
            'title' => __('Smart rhinopaq Settings', 'rhinopaq'),
            'desc' => __('Smart rhinopaq Settings of this plugin.', 'rhinopaq'),
        ),
        's4-field-2' => array(
            'type' => 'checkbox',
            'id' => 'rhinopaq-smart-enabled',
            'class' => 's4-fields all-fields rhino-checkboxes',
            'desc' => __( 'Enable the smart rhinopaq calculation. If not enabled, there will always be a pop up in the checkout asking to choose between single use and reusable packaging.', 'rhinopaq' ),
            'default' => 'no',
        ),
        's4-field-3' => array(
            'type' => 'text',
            'id' => 'rhinopaq-plugin-id',
            'class' => 's4-fields smart-fields rhinopaq-settings-note',
            'desc' => __( 'The unique plugin id provided by rhinopaq.', 'rhinopaq' ),
            'default' => '',
        ),
        's4-field-4' => array(
            'type' => 'number',
            'id' => 'rhinopaq-clearance',
            'class' => 's4-fields smart-fields',
            'custom_attributes' => array( 'step' => 'any', 'min' => '0' ),
            'desc' => __( 'The clearance in percent to adjust shipping tolerance to decide using a rhinopaq. Should be set as default to 5.', 'rhinopaq' ),
            'default' => '5',
        ),
        's4-field-5' => array(
            'type' => 'info',
            'id' => 's4-field-4',
            'name' => __('Smart rhinopaq Privacy', 'rhinopaq'),
            'text' => __('Smart rhinopaq Privacy of this plugin.', 'rhinopaq'),
        ),
        's4-sectionend' => array(
            'type' => 'sectionend',
            'id' => 's4-sectionend',
        ),
      );
  
      return apply_filters( 'wc_settings_tab_rhinopaq_settings', $settings );
    }

    # TODO: No PluginId but Smart rhinopaq activated

}

WC_Settings_Tab_rhinopaq::init();

// Add a product for reusable packaging
function create_virtual_rhinopaq() {

    // Upload images for the new product
    // Set the product featured image
    $image_feat_url = RHINOPAQ_PLUGIN_URL.'assets/images/rhinopaq-logo.png';
    $image_feat_id = rhino_upload_file_by_url($image_feat_url);

    // Set product gallery images
    $image_gal_url = RHINOPAQ_PLUGIN_URL.'assets/images/mehrwegverpackung-rhinopaq-s.jpg';
    $image_gal_id = rhino_upload_file_by_url($image_gal_url);

    // Define the new product
    $product_title = 'Mehrwegverpackung von rhinopaq';
    $product_slug = 'mehrwegverpackung-rhinopaq';
    $product_desc = 'Für einen Aufpreis von 1€ kannst du einen erheblichen Beitrag zum Einsparen von Ressourcen leisten. Du bekommst die Mehrwegverpackung von rhinopaq statt eine Einwegverpackung. Alles was du nach Erhalt tun musst, ist die Verpackung zusammen zu falten und in den nächsten Briefkasten zu werfen.';

    $product = new WC_Product_Variable();
    $product->set_name($product_title);
    $product->set_slug($product_slug);
    $product->set_description($product_desc);

    // Meta data
    $product->set_sold_individually(true);
    $product->set_virtual(true);
    $product->set_catalog_visibility('hidden');

    // Attach images
    $product->set_image_id($image_feat_id);
    $product->set_gallery_image_ids(array($image_gal_id));

    // Define the product attributes
    $attribute = new WC_Product_Attribute();
    $attribute->set_name('rhinopaq');
    $attribute->set_options( array(
        'rhino-b-35x20x5',
        'rhino-b-25x25x10'
    ) );
    $attribute->set_position(0);
    $attribute->set_visible(1);
    $attribute->set_variation(1);
    $product->set_attributes(array($attribute));

    // Save main product to get its id
    $product_id = $product->save();

    // Create variations after saving the new product
    $variation_ids = array();
    $variation_data = array(
        'rhino-b-35x20x5' => array('preis' => 1.00, 'attribute' => array('Variante' => 'rhino-b-35x20x5')),
        'rhino-b-25x25x10' => array('preis' => 2.00, 'attribute' => array('Variante' => 'rhino-b-25x25x10'))
    );
    foreach ($variation_data as $key => $data) {
        $variation = new WC_Product_Variation();
        $variation->set_parent_id($product_id);
        $variation->set_attributes(array('rhinopaq' => $key));
        $variation->set_regular_price($data['preis']);
        $variation->set_status('publish');
        $variation_ids[$key] = $variation->save();
    }

    $product->save();

    return array('product_id' => $product_id, 'variation_ids' => $variation_ids);  
}

// Upload image from url 
function rhino_upload_file_by_url($image_url) {
	// it allows us to use download_url() and wp_handle_sideload() functions
	require_once( ABSPATH . 'wp-admin/includes/file.php' );

	// download to temp dir
	$temp_file = download_url( $image_url );

	if( is_wp_error( $temp_file ) ) {
		return false;
	}

	// move the temp file into the uploads directory
	$file = array(
		'name'     => basename( $image_url ),
		'type'     => mime_content_type( $temp_file ),
		'tmp_name' => $temp_file,
		'size'     => filesize( $temp_file ),
	);
	$sideload = wp_handle_sideload(
		$file,
		array(
			'test_form'   => false // no needs to check 'action' parameter
		)
	);

	if( ! empty( $sideload[ 'error' ] ) ) {
		return false;
	}

	// it is time to add our uploaded image into WordPress media library
	$attachment_id = wp_insert_attachment(
		array(
			'guid'           => $sideload[ 'url' ],
			'post_mime_type' => $sideload[ 'type' ],
			'post_title'     => basename( $sideload[ 'file' ] ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$sideload[ 'file' ]
	);

	if( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		return false;
	}

	// update medatata, regenerate image sizes
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	wp_update_attachment_metadata(
		$attachment_id,
		wp_generate_attachment_metadata( $attachment_id, $sideload[ 'file' ] )
	);

	return $attachment_id;
}

// When creating the order, add the meta data from the cart item as order meta data
function add_rhinopaq_order_item_meta_data($item_id, $item, $order_id) {
    try{
        $search_key = 'rhinopaq-packaging';
        $value = array_key_exists_recursive($search_key, $item);
        if($value) {
            wc_add_order_item_meta($item_id, 'rhinopaq', $value);
        } 
    } catch(Exception $e) {
        // No further action
    }

}
add_action('woocommerce_new_order_item', 'add_rhinopaq_order_item_meta_data', 10, 4);

// Recursive function to check if a key exists in an array.
function array_key_exists_recursive($search_key, $array) {
    foreach ($array as $key => $value) {
        if ($key === $search_key) {
            return $value;
        } else {
            if (is_array($value)) {
                $subvalue = array_key_exists_recursive($search_key, $value);
                if ($subvalue) {
                    return $subvalue;
                }
            }
        }
    }
    return false;
}

// Debugging in this plugin 
function rhino_log($log) {
    $rhinoLogFile = plugin_dir_path(__FILE__) . '/rhino.log';
	error_log($log."\n",3,$rhinoLogFile);
    return true;
}

// Function to add an admin note
function rhinopaq_admin_notice($note = False) {
    if($note) {
        $message = sprintf('<div class="notice notice-success is-dismissible"><p>%s</p></div>',$note);
        echo $message;
    }
}
add_action('admin_notices', 'rhinopaq_admin_notice');

// Load scripts for the admin area
function load_rhinoadmin_script() {
    // Check if we are in the admin area for rhinopaq
    if (is_admin() && str_contains($_SERVER['REQUEST_URI'],'settings_tab_rhinopaq')) {
        $js_path = plugin_dir_url(__FILE__) . '/assets/rhinopaq-shipping-admin.js';
        wp_enqueue_script('rhinoadmin_script', $js_path, array(), '1.2.5', true);
        $css_path = plugin_dir_url(__FILE__) . 'assets/rhinopaq-shipping-admin.css';
        wp_enqueue_style('rhinoadmin_styles', $css_path, array(), '1.2.5', 'all');
    }
}
add_action('admin_enqueue_scripts', 'load_rhinoadmin_script');