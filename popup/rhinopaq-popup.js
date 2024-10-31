"use strict";

jQuery(document).ready(function($) {

	const btn_ewvp = document.getElementById("rhinopaq-ewvp");
	const btn_mwvp = document.getElementById("rhinopaq-mwvp");
	const rhino_popup = document.getElementById("rhinopaq-cont");
	const rhino_product_id = $('#rhinopaq-mwvp').data('productid');  // Product id of reusable packaging

	if(rhino_popup){
		// Function to check items of cart
		function isRhinopaqInCart(product_id) {
			let in_cart = false;
			jQuery.each(wc_checkout_params['cart'], function(index, item) {
				if (item['product_id'] == product_id) {
					in_cart = true;
					return false; // Exit the loop early if the product is found
				}
			});
			return in_cart;
		}

		// Function to set Session-Variable on the server site, in case disposable packaging
		function negDecisionRhinoAdd() {
			// AJAX call to set the session variable on the server side.
			jQuery.ajax({
				type: 'POST',
				dataType: 'json',
				url: rhinopaq_ajax_object.ajax_url,
				data: {
					action: 'neg_decision_rhino_add',
				},
				error: function(error) {
					// Error note
					console.error('Fehler beim Setzen der Session-Variable:', error);
				}
			});
		}

		// Function to add reusable packaging to the cart
		function posDecisionRhinoAdd() {
			// AJAX request to add the product to the shopping cart
			jQuery.ajax({
				type: 'POST',
				url: rhinopaq_ajax_object.ajax_url,
				data: {
					action: 'pos_decision_rhino_add',
				},
				success: function(response) {
					// Update shopping cart and total price in checkout
					jQuery(document.body).trigger('update_checkout');
				},
				error: function(error) {
					// Error handling if an error occurs when adding the product to the shopping cart
					console.error('Fehler beim Hinzufügen des Produkts zum Warenkorb:', error);
				}
			});		
		}

    	if (!isRhinopaqInCart(rhino_product_id)) {
			// The product is not in the shopping cart, so show the pop-up window
			rhino_popup.classList.add("show");

			// Action for disposable button
			btn_ewvp.addEventListener("click", (evt) => {
				evt.preventDefault();
				negDecisionRhinoAdd();
				rhino_popup.classList.remove("show");
			});

			// Action for reusable button
			btn_mwvp.addEventListener("click", (evt) => {
				evt.preventDefault();
				posDecisionRhinoAdd();
				rhino_popup.classList.remove("show");
			});
		} 
    }
});