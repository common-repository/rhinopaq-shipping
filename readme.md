# rhinopaq shipping for WooCommerce
Contributors: rhinopaq
Plugin URL: https://www.rhinopaq.com/
Tags: rhinopaq, woocommerce, ecommerce, reusable packaging, packaging, sustainability, woo
Requires at least: 4.6
Requires PHP: 5.6
Tested up to: 6.4.2
Stable tag: 1.2.0
License: GPLv3 or later

## Description

Integration of rhinopaq reusable packaging as a shipping method. The plugin enables woocommerce shops to use rhinopaq as shipping mehtod, show a pop-up message on the checkout page, add prices for reusable packaging in Germany and Austria and use the smart rhinopaq function hosted by rhinopaq on AWS. Smart rhinopaq calculates if all items in a shopping cart fit into one rhinopaq.

Goods that are shipped usually can't always be put together perfectly. We do the calculation for you via our API. The shopping cart is sent to us and we calculate whether the contents can be shipped with a rhinopaq. If so, a pop-up will be displayed at checkout indicating this. This feature is called **smart rhinopaq**. To use it, you need to contact rhinopaq to get a valid plugin-id.

## Installation

### From your WordPress dashboard

1. Visit 'Plugins > Add New'
2. Search for 'rhinopaq Shipping'
3. Activate rhinopaq Shipping from your Plugins page.

### From WordPress.org

1. Download rhinopaq Shipping.
2. Upload to your '/wp-content/plugins/' directory, using your favorite method (ftp, sftp, scp, etc...)
3. Activate rhinopaq Shipping from your Plugins page.

## Once Activated

1. Go to WooCommerce > Settings > rhinopaq
2. Configure the plugin for your store

## Configuring rhinopaq Shipping

* The basic functionalities of rhinopaq shipping can be used without further action. In order to use the smart rhinopaq calculation for the decision, regarding
* the use of reusable packaging or not, a plugin ID is required. rhinopaq provides the ID to stores. Please contact rhinopaq via the [contact form](https://www.rhinopaq.com/kontakt/).
* Within the WordPress administration area, go to the WooCommerce > Settings page and you will see rhinopaq as sub menu.
* Clicking on rhinopaq will take you into the settings page, where you can configure the plugin for your store.

### rhinopaq Enable / Disable

Turn the rhinopaq shipping method on / off for visitors at checkout.

### Packaging Settings

Activate the packaging sizes of rhinopaq that you currently have in stock and would like to offer to your customers.

### Check-Out Settings

Details like the price per use, the title in the shopping cart or the image you can customize via the created and linked product. rhinopaq will be offered for customers in Germany and Austria.

### Smart Shipping Enable / Disable

Enable the calculation using our API to show the reusable packaging option only if the cart fits into the reusable shipping box.

### PlugIn-ID

If you decide to activate Smart rhinopaq, you need a PlugIn ID from rhinopaq. Just contact us via the contact form, if you haven't received an ID yet.

**Please keep in mind that all products have to have maintained shipping data (length, width, height & weight) for the calculation to work.**

### Clearance

The space allowance as a percentage of the box volume of a rhinopaq. The value is used in the calculation for packing. With the addition you can influence the results of our API. If you enter 100 (for 100%) there will be no space left for your products. If you enter 0 (for 0%), your products will be packed very densely. We recommend the value 5.

## Frequently Asked Questions

### Where do i get reusable packaging for my shop?

You can purchase reusable packaging by rhinopaq on https://www.rhinopaq.com/.

## Prerequisites

To use this plugin with your WooCommerce store you will need:
* WooCommerce plugin

## Dependencies

This plugin is using an service by rhinopaq hostet on the Amazon AWS. Therefore, data of the shopping cart will be sent to an AWS endpoint.

### Dynamic data sent to that endpoint
* pluginId: the PlugIn-ID defined in the admin area and give by rhinopaq
* requestId: unique id of the request to handle it properly
* items: length, width, height and weight of each item in the shopping cart
* clearance: chosen clearance value in the admin area

### Data received by that endpoint
* fits: boolean value that has been calculated by the service of rhinopaq; if true, the given items fit into one rhinopaq and rhinopaq will be displayed as shipping option; if false, the given items do not fit into one rhinopaq; rhinopaq shipping method will be hidden
* fittingRhinopaq: the suggested rhinopaq size for shipping; one of those, that are activated in the admin area
* pluginid: again the pluginid
* requestid: again the unique id of the request to handle it properly
* message: status message

## Screenshots

1. Pop-Up in Checkout on Desktop
2. Pop-Up in Checkout on Mobile
3. Admin panel
4. Customizer area

## Privacy Policy

The WooCommerce plug-in rhinopaq Shipping does not send any data from your site to us without smart rhinopaq enabled. If you use the smart rhinopaq function, the measurements and the weight of all items in the shopping cart will be sent to our service and servers respectively, hosted on Amazon AWS. The data is used to request the possible use of one of our rhinopaqs in the checkout process. If our service confirms the possibility, positive feedback will be sent back to your store accordingly. rhinopaq will then be suggested as a shipping method and a pop-up will be created asking the user if she would like to use reusable packaging or stick with disposable. No personal data is sent, nor any information about the products beyond weight and dimensions. The data is stored by us in order to be able to determine the quality of our calculations in the long term and to optimize the service. By activating the smart rhinopaq function you agree to its use.

## Changelog

### 1.2.0 
* moved the customization options to the Wordpress customizer area 
* extended customization options of the popup 
* added variations to work with in the reusable packaging product
* integrated proper cleaning habbits on deleting the plugin
* fixed bugs

### 1.1.0 
* moved the position of the admin area to the parent settings level of WooCommerce
* reusable packaging is now added to the cart as an additional virtual product
* images, price and texts of the reusable packaging pop-up are now maintained through the newly created product of reusable packaging
* different sizes are now offered and made selectable in the admin area

### 1.0.3 
* added sliders in admin area
* irrelevant areas in admin area are disabled if not selected

### 1.0.2
* added language en_US
* added privacy policy in the admin area

### 1.0.1
* pop-up image will be provided locally via plugin folder /assets/images/. Therefore, the plugin parameter RHINOPAQ_PLUGIN_URL has been added in /rhinopaq-shipping.php. The image has been added /assets/images/mehrwegverpackung-rhinopaq-s.jpg.
* the readme file was extended by the dependencies section
* curl method has been replaced by the WordPress HTTP API with wp_remote_get() in /rhinopaq-config.php:220
* all echo calls in /popup/rhinopaq-popup.php have been replaced by esc_html_e()

### 1.0.0
* rhinopaq Shipping
