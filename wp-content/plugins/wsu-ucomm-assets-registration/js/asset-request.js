/**
 * Handle form submissions through the asset request form.
 */
(function( $, window, undefined ){

	/**
	 * Handle the click action on the form submission button.
	 */
	function handle_click( e ) {
		e.preventDefault();

		var email_address = $( '#email-address' ).val(),
			department    = $( '#department' ).val(),
			notes         = $( '#request-notes' ).val(),
			asset_type    = $( '#asset-type' ).val(),
			nonce         = $( '#asset-request-nonce' ).val();

		// Build the data for our ajax call
		var data = {
			action:        'submit_asset_request',
			email_address: email_address,
			department:    department,
			notes:         notes,
			asset_type:    asset_type,
			_ajax_nonce:   nonce
		};

		// Make the ajax call
		$.post( window.ucomm_asset_data.ajax_url, data, function( response ) {
			response = $.parseJSON( response );

			if ( response.success ) {
				$( '#asset-request-form' ).remove();
				$( '#asset-request' ).append( '<p>Your asset request has been received. Please allow 24-48 hours for a response.</p>' );
			} else {
				$( '#asset-request' ).prepend( '<p>Something in the request failed. Please try again.</p>' );
			}
		});
	}

	$( '#submit-asset-request' ).on( 'click', handle_click );
}( jQuery, window ) );