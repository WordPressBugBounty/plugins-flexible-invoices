<?php

namespace WPDesk\FlexibleInvoices\Marketing;

use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;

class SupportLinks implements Hookable {

	const INVOICE_LISTING_PAGE = 'edit-inspire_invoice';
	const INVOICE_EDIT_PAGE    = 'inspire_invoice';
	const INVOICE_ADD_ACTION   = 'add';

	public function hooks() {
		add_action( 'admin_footer', [ $this, 'add_support_link_on_invoice_listing' ] );
		add_action( 'admin_footer', [ $this, 'add_support_link_on_invoice_add' ] );
		add_action( 'admin_footer', [ $this, 'add_support_link_on_invoice_edit' ] );
	}

	public function get_docs_link( $url ) {
		return sprintf( esc_html__( 'Read more in the %1$splugin documentation &rarr;%2$s', 'flexible-invoices' ), '<a href="' . $url . '" target="_blank" style="color: #4BB04E; font-weight: 700;"><strong>', '</strong></a>' );
	}

	public function add_support_link_on_invoice_listing() {
		$screen = get_current_screen();
		$url    = 'https://docs.flexibleinvoices.com/article/801-managing-editing-proforma-and-invoices?utm_source=flexible-invoices-settings&utm_medium=link&utm_campaign=flexible-invoices-docs-link&utm_content=all-invoices';
		if ( get_locale() === 'pl_PL' ) {
			$url = 'https://www.wpdesk.pl/docs/faktury-woocommerce-docs/?utm_source=flexible-invoices-settings&utm_medium=link&utm_campaign=flexible-invoices-docs-link&utm_content=all-invoices#zarzadzanie-fakturami';
		}

		if ( isset( $screen->id ) && $screen->id === self::INVOICE_LISTING_PAGE ) {
			?>
			<script>
				( function ( $ ) {
					$( '.wp-header-end' ).before( '<div class="support-url-wrapper"><?php echo $this->get_docs_link( $url ); ?></div>' );
				} )( jQuery );
			</script>
			<?php
		}
	}

	public function add_support_link_on_invoice_add() {
		$screen = get_current_screen();
		if ( ( isset( $screen->id ) && $screen->id === self::INVOICE_EDIT_PAGE ) && ( isset( $screen->action ) && $screen->action === self::INVOICE_ADD_ACTION ) ) {
			$url = 'https://docs.flexibleinvoices.com/article/803-manual-issuing-proforma-and-invoices?utm_source=flexible-invoices-settings&utm_medium=link&utm_campaign=flexible-invoices-docs-link&utm_content=add-invoice';
			if ( get_locale() === 'pl_PL' ) {
				$url = 'https://www.wpdesk.pl/docs/faktury-woocommerce-docs/?utm_source=flexible-invoices-settings&utm_medium=link&utm_campaign=flexible-invoices-docs-link&utm_content=add-invoice#reczne-wystawianie-faktur';
			}
			?>
			<script>
				( function ( $ ) {
					$( '.wp-header-end' ).before( '<div class="support-url-wrapper"><?php echo $this->get_docs_link( $url ); ?></div>' );
				} )( jQuery );
			</script>
			<?php
		}
	}

	public function add_support_link_on_invoice_edit() {
		$screen = get_current_screen();
		if ( isset( $screen->id ) && $screen->id === self::INVOICE_EDIT_PAGE && empty( $screen->action ) ) {
			$url = 'https://docs.flexibleinvoices.com/article/801-managing-editing-proforma-and-invoices?utm_source=flexible-invoices-settings&utm_medium=link&utm_campaign=flexible-invoices-docs-link&utm_content=edit-invoice';
			if ( get_locale() === 'pl_PL' ) {
				$url = 'https://www.wpdesk.pl/docs/faktury-woocommerce-docs/?utm_source=flexible-invoices-settings&utm_medium=link&utm_campaign=flexible-invoices-docs-link&utm_content=edit-invoice#edycja-faktur';
			}
			?>
			<script>
				( function ( $ ) {
					$( '.wp-header-end' ).before( '<div class="support-url-wrapper"><?php echo $this->get_docs_link( $url ); ?></div>' );
				} )( jQuery );
			</script>
			<?php
		}
	}

}
