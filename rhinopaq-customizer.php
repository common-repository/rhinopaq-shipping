<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function rhinopaq_customizer_script() {
    wp_enqueue_script('rhinopaq-customizer-js', plugin_dir_url(__FILE__) . 'assets/rhinopaq-customizer.js', array( 'jquery', 'customize-controls' ), false, true);
    wp_localize_script('rhinopaq-customizer-js', 'meinPluginCustomizer', array(
        'cartUrl' => wc_get_cart_url(),
        'checkoutUrl' => wc_get_checkout_url(),
    ));
}
add_action('customize_controls_enqueue_scripts', 'rhinopaq_customizer_script');


function rhinopaq_customize_register($wp_customize) {
    
    # SECTION: rhinopaq custom settings
    $wp_customize->add_section('rhinopaq_custom_settings', array(
        'title' => 'rhinopaq',
        'priority' => 30,
    ));

    $wp_customize->add_setting('rhino_preview_active', array(
        'default' => 'true',
        'sanitize_callback' => '',
    ));

    # Defining some tests to show the preview
    $rhino_preview_desc = '';
    # rhinopaq not activated
    if(get_option('rhinopaq-enabled', 'yes') === 'no'):
        $rhino_preview_desc .= '<br><b><span style="color:#ff0000";>';
        $rhino_preview_desc .= __('Note: The rhinopaq option is currently disabled. Therefore, the popup cannot be displayed. Go to WooCommerce > Settings > rhinopaq in the admin area and activate the option.', 'rhinopaq');
        $rhino_preview_desc .= '</b></span><br>';
    # rhinopaq is standard
    elseif(get_option('rhinopaq-standard-shipping', 'no') === 'yes'):
        $rhino_preview_desc .= '<br><b><span style="color:#ff0000";>';
        $rhino_preview_desc .= __('Note: rhinopaq is currently selected as the default and is added to every order. Therefore, no popup will be displayed during checkout.', 'rhinopaq');
        $rhino_preview_desc .= '</b></span><br>';
    # No size picked
    elseif(get_option('rhinopaq-1-enabled', 'no') === 'no' && get_option('rhinopaq-4-enabled', 'no') === 'no'):
        $rhino_preview_desc .= '<br><b><span style="color:#ff0000";>';
        $rhino_preview_desc .= __('Note: You currently have no rhinopaq sizes activated. Therefore, the popup will not be displayed in your shop. Go to WooCommerce > Settings > rhinopaq in the admin area and activate at least one rhinopaq size.', 'rhinopaq');
        $rhino_preview_desc .= '</b></span><br>';
    # No smart rhinopaq activated
    elseif(get_option('rhinopaq-smart-enabled', 'no') === 'no'):
        $rhino_preview_desc .= '<br><b><span style="color:#dd9933";>';
        $rhino_preview_desc .= __('Note: You currently have the smart rhinopaq function disabled. Therefore, the popup will always be displayed during the shop checkout, regardless of whether the shopping cart fits or not. Go to WooCommerce > Settings > rhinopaq in the admin area and activate the smart rhinopaq function.', 'rhinopaq');
        $rhino_preview_desc .= '</b></span><br>';
    # Smart rhinopaq activated but no plugin ID
    elseif(get_option('rhinopaq-smart-enabled', 'no') === 'yes' && empty(get_option('rhinopaq-plugin-id', ''))):
        $rhino_preview_desc .= '<br><b><span style="color:#dd9933";>';
        $rhino_preview_desc .= __('Note: Please enter your rhinopaq Plugin ID to use the smart rhinopaq function. <a href=\"https://www.rhinopaq.com/kontakt\" target=\"_blank\">Contact us</a> if you do not have an ID or need assistance with setting up the plugin.', 'rhinopaq');
        $rhino_preview_desc .= '</b></span><br>';
    endif;
    $rhino_preview_desc .= __('Show the popup in the customizer preview. The popup preview is only visible during checkout.', 'rhinopaq');

    $wp_customize->add_control('rhino_preview_active', array(
        'label' => __('Enable popup preview', 'rhinopaq'),
        'section' => 'rhinopaq_custom_settings',
        'type' => 'checkbox',
        'description' => $rhino_preview_desc,
    ));

    # Pop-up container
        $wp_customize->add_setting('rhino_popup_bgcolor', array(
            'default' => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'rhino_popup_bgcolor', array(
            'label' => __('Popup background color', 'rhinopaq'),
            'section' => 'rhinopaq_custom_settings',
        )));

        $wp_customize->add_setting('rhino_popup_corner', array(
            'default' => 10,
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('rhino_popup_corner', array(
            'type' => 'range',
            'section' => 'rhinopaq_custom_settings',
            'label' => __('Popup corner radius', 'rhinopaq'),
            'description' => __('Choose a corner radius for the popup between 0px and 50px', 'rhinopaq'),
            'input_attrs' => array(
              'min' => 0,
              'max' => 50,
              'step' => 2
            ),
          ) );

    # Pop-up title & text
        $wp_customize->add_setting('rhino_popup_title', array(
            'default' => __('Reusable packaging by rhinopaq', 'rhinopaq'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('rhino_popup_title', array(
            'label' => __('Popup title', 'rhinopaq'),
            'section' => 'rhinopaq_custom_settings',
            'type' => 'text',
        ));

        $wp_customize->add_setting('rhino_popup_text', array(
            'default' => __('You can make a small contribution to saving resources. By choosing \'Reusable\', you will receive the reusable packaging from rhinopaq instead of a disposable cardboard box. All you have to do after receiving it is to fold the packaging and throw it into the nearest mailbox. Let\'s reduce packaging waste together.', 'rhinopaq'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('rhino_popup_text', array(
            'label' => __('Popup description', 'rhinopaq'),
            'section' => 'rhinopaq_custom_settings',
            'type' => 'textarea',
        ));

        $wp_customize->add_setting('rhino_popup_text_color', array(
            'default' => '#333333',
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'rhino_popup_text_color', array(
            'label' => __('Popup text color', 'rhinopaq'),
            'section' => 'rhinopaq_custom_settings',
        )));
    
    # Pop-up image
        $wp_customize->add_setting('rhino_popup_image', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'rhino_popup_image', array(
            'label' => __( 'Popup image', 'rhinopaq' ),
            'section' => 'rhinopaq_custom_settings',
            'mime_type' => 'image',
        )));

        $wp_customize->add_setting('rhino_image_active', array(
            'default' => 'true',
            'sanitize_callback' => '',
        ));

        $wp_customize->add_control('rhino_image_active', array(
            'label' => __('Activate popup image', 'rhinopaq'),
            'section' => 'rhinopaq_custom_settings',
            'type' => 'checkbox',
            'description' => __('Activate the popup image on large screens from 650px. The image will not be displayed on tablets and smartphones.', 'rhinopaq'),
        ));

    # Buttons 
        # Size
        $wp_customize->add_setting('rhino_button_size', array(
            'default' => 'middle',
            'sanitize_callback' => '',
        ));

        $wp_customize->add_control('rhino_button_size', array(
            'label' => __('Button size', 'rhinopaq'),
            'section' => 'rhinopaq_custom_settings',
            'type' => 'select',
            'choices' => array(
                'small' => __('Small', 'rhinopaq'),
                'middle' => __('Medium', 'rhinopaq'),
                'large' => __('Large', 'rhinopaq'),
              ),
        ));

    # Button 1
        # Text
        $wp_customize->add_setting('rhino_button_1_text', array(
            'default' => __('Reusable packaging', 'rhinopaq'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('rhino_button_1_text', array(
            'label' => __('Button text (Reusable)', 'rhinopaq'),
            'section' => 'rhinopaq_custom_settings',
            'type' => 'text',
        ));

        # Type
        $wp_customize->add_setting('rhino_button_1_type', array(
            'default' => 'pill',
            'sanitize_callback' => '',
        ));

        $wp_customize->add_control('rhino_button_1_type', array(
            'label' => __('Button type (Reusable)', 'rhinopaq'),
            'section' => 'rhinopaq_custom_settings',
            'type' => 'select',
            'choices' => array(
                'pill' => __('Pill', 'rhinopaq'),
                'rect' => __('Rect', 'rhinopaq'),
              ),
        ));

        # Colors
        $wp_customize->add_setting('rhino_button_1_bgcolor', array(
            'default' => '#333333',
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'rhino_button_1_bgcolor', array(
            'label' => __('Button color (Reusable)', 'rhinopaq'),
            'section' => 'rhinopaq_custom_settings',
        )));

        $wp_customize->add_setting('rhino_button_1_color', array(
            'default' => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'rhino_button_1_color', array(
            'label' => __('Button text color (Reusable)', 'rhinopaq'),
            'section' => 'rhinopaq_custom_settings',
        )));

    # Button 2
        # Text
        $wp_customize->add_setting('rhino_button_2_text', array(
            'default' => __('Disposable packaging', 'rhinopaq'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('rhino_button_2_text', array(
            'label' => __('Button text (Disposable)', 'rhinopaq'),
            'section' => 'rhinopaq_custom_settings',
            'type' => 'text',
        ));

        # Type
        $wp_customize->add_setting('rhino_button_2_type', array(
            'default' => 'pill',
            'sanitize_callback' => '',
        ));

        $wp_customize->add_control('rhino_button_2_type', array(
            'label' => __('Button type (Disposable)', 'rhinopaq'),
            'section' => 'rhinopaq_custom_settings',
            'type' => 'select',
            'choices' => array(
                'pill' => __('Pill', 'rhinopaq'),
                'rect' => __('Rect', 'rhinopaq'),
                'text' => __('Text', 'rhinopaq'),
              ),
        ));

        # Colors
        $wp_customize->add_setting('rhino_button_2_bgcolor', array(
            'default' => '#333333',
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'rhino_button_2_bgcolor', array(
            'label' => __('Button color (Disposable)', 'rhinopaq'),
            'section' => 'rhinopaq_custom_settings',
        )));

        $wp_customize->add_setting('rhino_button_2_color', array(
            'default' => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'rhino_button_2_color', array(
            'label' => __('Button text color (Disposable)', 'rhinopaq'),
            'section' => 'rhinopaq_custom_settings',
        )));
    
}
add_action('customize_register', 'rhinopaq_customize_register');

?>