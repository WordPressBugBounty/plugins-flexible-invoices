jQuery( function ( $ ) {
	$( '.fi-block-template-toggle' ).on( 'change', function () {
		let postId = $( this ).data( 'post-id' );
		$( '.fi-block-template-toggle' ).prop( 'checked', false );
		$( this ).prop( 'checked', true );

		$.post( fiInvoicesCore.ajax_url, {
			action: 'fi_toggle_template_enabled',
			post_id: postId,
			nonce: fiInvoicesCore.nonce
		} );
	} );
} );
