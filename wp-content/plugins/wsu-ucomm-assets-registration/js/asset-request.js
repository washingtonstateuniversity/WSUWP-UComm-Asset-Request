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
			nonce         = $( '#asset-request-nonce' ).val();

		// Build the data for our ajax call
		var data = {
			action:        'submit_asset_request',
			email_address: email_address,
			department:    department,
			notes:         notes,
			_ajax_nonce:   nonce
		};

		// Make the ajax call
		$.post( window.ucomm_asset_data.ajax_url, data, function( response ) {
			if ( 'success' === response ) {

			} else {
				console.log('hi');
			}
		});
	}

	$( '#submit-asset-request' ).on( 'click', handle_click );
}( jQuery, window ) );