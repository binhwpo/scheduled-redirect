/**
 * Handle the redirect form
 */

( function( $ ) {
	$( document ).ready( function( $ ) {
		$( document ).on( 'change', '#sca-type', function() {
			// get the type
			var type = $( this ).val();

			// check if the given type is there to work with terms or metas
			if ( type == 'redirect' ) {

				// load the additional form data via the action type
				var postVars = {
					action: 'sca_load_additional_form_data',
					type: type,
					post_id: $( '#post_ID' ).val()
				}
				$.post( ajaxurl, postVars, function( response ) {
					$( '.sca-additional-form-data' ).html( '<hr>' + response );
					ScheduledContentActionsScripts.bindAdditionalTaxonomyAction();
				} );
			}
			return false;
		} );
	});
} )( jQuery );
