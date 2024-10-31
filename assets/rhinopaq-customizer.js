( function( $, api ) {

    console.log("rhinopaq-customizer-js:3");

    api.section( 'rhinopaq_custom_settings', function( section ) {
        section.expanded.bind( function( isExpanded ) {
            if ( isExpanded ) {
                // navigate to the checkout page
                api.previewer.previewUrl.set( meinPluginCustomizer.checkoutUrl ); 
            }
        });
    });
} )( jQuery, wp.customize );
