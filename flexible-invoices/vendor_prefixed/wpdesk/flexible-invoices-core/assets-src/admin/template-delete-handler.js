jQuery( function ( $ ) {
	$( document ).on( 'click','.delete a', function ( e ) {

		const $row = $(this).closest('tr');
		const isChecked = $row.find('.fi-block-template-toggle').is(':checked');

		if( isChecked ) {
			e.preventDefault();

			let userIsSure = confirm( 'Are you sure you want to delete this template?');

			if( userIsSure === true ) {
				window.location.href = $(this).attr('href');
			}
		}
	} );
} );
