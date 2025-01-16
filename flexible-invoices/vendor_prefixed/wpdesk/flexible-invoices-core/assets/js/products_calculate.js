jQuery.noConflict();
( function ( $ ) {

	function parseFloatLocal( num ) {
		return parseFloat( num.replace( ",", "." ) );
	}

	function moneyMultiply( a, b ) {
		if( a === 0 || b === 0 ) {
			return 0;
		}
		let log_10 = function ( c ) {
				return Math.log( c ) / Math.log( 10 );
			},
			ten_e = function ( d ) {
				return Math.pow( 10, d );
			},
			pow_10 = -Math.floor( Math.min( log_10( a ), log_10( b ) ) ) + 1;
		let mul = ( ( a * ten_e( pow_10 ) ) * ( b * ten_e( pow_10 ) ) ) / ten_e( pow_10 * 2 );

		if( isNaN( mul ) || ! isFinite( mul ) ) {
			return 0;
		} else {
			return mul;
		}
	}

	function bruttoToNetto( brutto, vat, qty ) {
		if( vat === 0 ) {
			return brutto / qty;
		}
		if( brutto === 0 || vat === 0 || qty === 0 || isNaN( brutto ) || isNaN( vat ) || isNaN( qty ) ) {
			return 0;
		}

		let netto = brutto / ( 1 + ( vat / 100 ) );
		return netto / qty;
	}

	function getVatRateFromField( field ) {
		return parseFloat( field.val().split( '|' )[ 1 ], 10 );
	}

	function invoiceRefreshProductNetPriceSum( $productHandle ) {
		$( '.net_price_sum', $productHandle ).val(
			moneyMultiply(
				parseFloatLocal( $( '.net_price', $productHandle ).val() ),
				parseFloatLocal( $( '.quantity', $productHandle ).val() )
			).toFixed( 4 )
		);
		invoiceRefreshProductVatRate( $productHandle );
	}

	function invoiceRefreshProductBruttoPriceSum( $productHandle ) {
		$( '.net_price', $productHandle ).val(
			bruttoToNetto(
				parseFloatLocal( $( '.total_price', $productHandle ).val() ),
				getVatRateFromField( $( '.vat_type', $productHandle ) ),
				parseFloatLocal( $( '.quantity', $productHandle ).val() )
			).toFixed( 4 )
		);
		$( '.net_price', $productHandle ).trigger( 'change' );
	}

	function invoiceRefreshProductVatRate( $productHandle ) {
		let vatType = getVatRateFromField( $( '.vat_type', $productHandle ) );
		let discount = 0;

		if( $( '.discount', $productHandle ).length > 0 ) {
			discount = parseFloatLocal( $( '.discount', $productHandle ).val() );
		}

		let net_price_sum = parseFloatLocal( $( '.net_price_sum', $productHandle ).val() );

		if( discount > 0 ) {
			net_price_sum = net_price_sum - discount;
			$( '.net_price_sum', $productHandle ).val( net_price_sum.toFixed( 2 ) );
		}

		let vat_sum = moneyMultiply(
			net_price_sum,
			( isNaN( vatType ) ? 0 : vatType ) / 100
		);

		$( '.vat_sum', $productHandle ).val( vat_sum.toFixed( 2 ) );
		invoiceRefreshProductTotal( $productHandle );
	}

	function invoiceRefreshProductTotal( $productHandle ) {

		let total = parseFloatLocal( $( '.vat_sum', $productHandle ).val() ) + parseFloatLocal( $( '.net_price_sum', $productHandle ).val() );
		$( '.total_price', $productHandle ).val(
			(
				( isNaN( total ) ? 0 : total ).toFixed( 2 )
			)
		);
		invoiceRefreshTotal( $( '.vat_sum', $productHandle ).parents( 'table' ) );
	}

	function invoiceRefreshTotal( $table ) {
		let price = 0.0;
		if( $table.hasClass( 'after-correction' ) ) {
			price = calculate_correction_total( $table );

		} else {
			$( '.product_row .total_price' ).each( function ( index, item ) {
				let val = parseFloatLocal( $( item ).val() );
				price += isNaN( val ) ? 0 : val;
			} );
		}


		$( '[name=total_price]' ).val( price.toFixed( 2 ) );
	}

	function calculate_correction_total() {
		let  before_price = 0.0;
		let  after_price = 0.0;
		$( '#before-correction .product_row .total_price' ).each( function ( index, item ) {
			let before_price_val = parseFloatLocal( $( item ).val() );
			before_price += isNaN( before_price_val ) ? 0 : before_price_val;
		} );

		$( '#after-correction .product_row .total_price' ).each( function ( index, item ) {
			let after_price_val = parseFloatLocal( $( item ).val() );

			after_price += isNaN( after_price_val ) ? 0 : after_price_val;
		} );

		return -before_price + after_price;
	}

	$( 'body.post-type-inspire_invoice .products_metabox' )
		.on( 'click', '.remove_product', function ( e ) {
			e.preventDefault();

			let table = $( this ).parents( 'table' );
			if( table.hasClass( 'after-correction' ) ) {
				let after_index = $( this ).parents( '.product_row' ).index();
				$( 'table.before-correction tbody tr' ).eq( after_index ).remove();
			}
			$( this ).parents( '.product_row' ).remove();
			invoiceRefreshTotal( $( this ).parents( 'table' ) );
		} )
		.on( 'click', '.add_product', function ( e ) {
			e.preventDefault();

			let item_html = $( '#product_prototype' ).html();
			$( '.products_container' ).append( item_html );
		} )
		.on( 'click', '.add_product_correction', function ( e ) {
			e.preventDefault();
			let product_before = $( '#product_before_prototype' ).html();
			let product_after = $( '#product_after_prototype' ).html();
			$( '.products_before_container' ).append( product_before );
			$( '.products_after_container' ).append( product_after );
		} )
		.on( 'change', '.refresh_net_price_sum', function ( e ) {
			let productHandle = $( this ).parents( '.product_row' );
			invoiceRefreshProductNetPriceSum( productHandle );
		} )
		.on( 'change', '.refresh_product', function ( e ) {
			let productHandle = $( this ).parents( '.product_row' );
			let price = this.options[ this.selectedIndex ].dataset.price;

			productHandle[ 0 ].querySelector( "input[name='product[net_price][]" ).value = price;

			invoiceRefreshProductNetPriceSum( productHandle );
		} )
		.on( 'change', '.refresh_vat_sum', function ( e ) {
			let productHandle = $( this ).parents( '.product_row' );
			invoiceRefreshProductNetPriceSum( productHandle );

		} )
		.on( 'change', '.refresh_total_price', function ( e ) {
			let productHandle = $( this ).parents( '.product_row' );
			$( '.total_price', productHandle ).trigger( 'change' );
		} )
		.on( 'change', '.refresh_total', function ( e ) {
			invoiceRefreshTotal( $( this ).parents( 'table' ) );
			let productHandle = $( this ).parents( '.product_row' );
			invoiceRefreshProductBruttoPriceSum( productHandle );
		} );

} )( jQuery );
