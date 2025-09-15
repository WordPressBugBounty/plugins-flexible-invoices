<?php

namespace WPDesk\FlexibleInvoices\Addons\Validation;

use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;

class ValidateInvoice implements Hookable {

	public function hooks() {
		add_action( 'edit_form_before_permalink', [ $this, 'validate_invoice' ] );
	}

	public function validate_invoice( $post ) {
		global $wpdb;
		$post_id             = (int) $post->ID;
		$post_title          = $post->post_title;
		$invoice_number      = $wpdb->get_var( "SELECT `post_title` FROM $wpdb->posts WHERE `post_title` = '" . $post_title . "' AND `post_type` = 'inspire_invoice' AND `ID` != '$post_id'  " ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$next_invoice_number = (int) get_option( 'inspire_invoices_invoice_start_number' );
		if ( $invoice_number ) {
			// translators: %1 invoice number, %2 next invoice number
			echo '<span style="color: red;">' . sprintf( esc_html__( 'The invoice with the number %1$s already exists! Next number is: %2$s.', 'flexible-invoices' ), esc_html( $invoice_number ), $next_invoice_number ) . '</span>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}
