jQuery( function( $ ) {
	'use strict';

	if ( typeof wpdInvoicesCheckout === 'undefined' ) {
		return;
	}

	const wantInvoiceKey   = wpdInvoicesCheckout.wantInvoiceKey;
	const billingTaxIdKey  = wpdInvoicesCheckout.billingTaxIdKey;
	const shippingTaxIdKey = wpdInvoicesCheckout.shippingTaxIdKey;
	const behavior         = wpdInvoicesCheckout.fieldBehavior || 'ask_user';

	function toggleFields() {
		const $wantInvoiceCheckbox = $( '#' + wantInvoiceKey + ', input[name="' + wantInvoiceKey + '"]' );
		const $billingField        = $( '#' + billingTaxIdKey + '_field' );
		const $shippingField       = $( '#' + shippingTaxIdKey + '_field' );
		const $shipToDifferent     = $( '#ship-to-different-address-checkbox' );

		let isWantInvoice = false;
		if ( behavior === 'ask_user' ) {
			isWantInvoice = $wantInvoiceCheckbox.is( ':checked' );
		} else {
			isWantInvoice = true;
		}
		const isShipDifferent = $shipToDifferent.length ? $shipToDifferent.is( ':checked' ) : false;

		if ( isWantInvoice ) {
			$billingField.show();
			if ( isShipDifferent ) {
				$shippingField.show();
			} else {
				$shippingField.hide();
			}
		} else {
			$billingField.hide();
			$shippingField.hide();
		}

		setRequiredMarker( $billingField, isWantInvoice );
		setRequiredMarker( $shippingField, isWantInvoice && isShipDifferent );
	}

	function setRequiredMarker( $field, visible ) {
		if ( ! $field.hasClass( 'wpd-invoices-required-with-invoice' ) ) {
			return;
		}

		const $label = $field.find( 'label' ).first();

		if ( visible ) {
			$label.find( 'span.optional' ).hide();
			// span, not abbr: themes underline abbr[title] and use the help cursor.
			if ( ! $label.find( 'span.required' ).length ) {
				$label.append( '<span class="required" aria-hidden="true">*</span>' );
			}
			$field.addClass( 'validate-required' );
			return;
		}

		$label.find( 'span.optional' ).show();
		$label.find( '.required' ).remove();
		$field.removeClass( 'validate-required woocommerce-invalid woocommerce-invalid-required-field' );
	}

	$( document ).on( 'change', '#' + wantInvoiceKey + ', input[name="' + wantInvoiceKey + '"], #ship-to-different-address-checkbox', toggleFields );
	$( document.body ).on( 'updated_checkout', toggleFields );

	// Initial toggle on page load
	toggleFields();
} );
