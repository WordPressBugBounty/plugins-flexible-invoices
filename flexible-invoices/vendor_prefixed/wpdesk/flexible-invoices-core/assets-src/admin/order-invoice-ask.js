jQuery( function ( $ ) {
	$( document ).ajaxComplete( function ( event, xhr, settings ) {
		const payload = settings && settings.data ? settings.data : '';
		const action = typeof payload === 'string' ? payload : ( payload.action || '' );
		if ( -1 === String( action ).indexOf( 'woocommerce_get_customer_details' ) ) {
			return;
		}

		const response = xhr.responseJSON;
		if ( ! response || ! response.billing || typeof response.billing.invoice_ask === 'undefined' ) {
			return;
		}

		const box = document.getElementById( '_billing_invoice_ask' );
		if ( ! box ) {
			return;
		}

		const ask = response.billing.invoice_ask;
		box.value = '1';
		box.checked = ask === '1' || ask === 'yes' || ask === 1 || ask === true;
	} );
} );
