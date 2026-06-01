<?php

namespace WPDeskFIVendor;

/**
 * Template used for rendering fields in WordPress profile edit page.
 * @var string $vat_number
 * @var string $invoice_ask
 */
?>

<script id="vat_number_row" type="template/text">
	<tr>
		<th><label for="vat_number"><?php 
\esc_html_e('VAT Number', 'flexible-invoices');
?></label>
		</th>

		<td>
			<input type="text" name="vat_number" id="vat_number" value="<?php 
echo \esc_attr($vat_number);
?>" class="regular-text"/><br/>
			<span class="description"></span>
		</td>
	</tr>
</script>
<script id="invoice_ask_row" type="template/text">
	<tr>
		<th><label for="invoice_ask"><?php 
\esc_html_e('I want an invoice', 'flexible-invoices');
?></label>
		</th>

		<td>
			<input type="checkbox" name="invoice_ask" id="invoice_ask" value="1" <?php 
\checked($invoice_ask);
?> /><br/>
			<span class="description"></span>
		</td>
	</tr>
</script>
<script>
	/**
	 * Adds VAT Number and Invoice Ask fields after the company or name field.
	 */
	jQuery( function ( $ ) {
		let has_billing_vat = $( '#billing_vat_number' ).length;
		let billing_company_field = $( '#billing_company' );
		let billing_last_name_field = $( '#billing_last_name' );

		let target = null;
		if ( billing_company_field.length ) {
			target = billing_company_field.closest('tr');
		} else if ( billing_last_name_field.length ) {
			target = billing_last_name_field.closest('tr');
		}

		if ( target ) {
			if ( ! has_billing_vat ) {
				let vat_number_field = $('#vat_number_row').html();
				target.after( vat_number_field );
				target = target.next();
			} else {
				target = $( '#billing_vat_number' ).closest('tr');
			}

			let invoice_ask_field = $('#invoice_ask_row').html();
			target.after( invoice_ask_field );
		}
	})
</script>
<?php 
