<?php

namespace WPDesk\FlexibleInvoices\Addons\Sending;

use WPDesk\FlexibleInvoices\Addons\Sending\Fields\SendingAddonURL;
use WPDesk\FlexibleInvoices\Addons\Sending\Fields\MultipleInputTextField;
use WPDesk\FlexibleInvoices\Addons\Sending\Fields\WysiwygField;
use WPDeskFIVendor\WPDesk\Forms\Field\BasicField;
use WPDeskFIVendor\WPDesk\Forms\Field\CheckboxField;
use WPDeskFIVendor\WPDesk\Forms\Field\Header;
use WPDeskFIVendor\WPDesk\Forms\Field\InputTextField;
use WPDeskFIVendor\WPDesk\Forms\Field\SelectField;
use WPDeskFIVendor\WPDesk\Forms\Field\SubmitField;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Tabs\FieldSettingsTab;

class SendingTab extends FieldSettingsTab {

	const TAX_NAME = 'name';
	const TAX_RATE = 'rate';

	/**
	 * Get disabled data value.
	 *
	 * @return string
	 */
	private function get_disabled(): string {
		return 'yes';
	}

	private function get_doc_link(): string {
		$pro_url = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/sklep/faktury-woocommerce-zaawansowana-wysylka/?utm_source=wp-admin-plugins&utm_medium=button&utm_campaign=flexible-invoices-adavanced-sending' : 'https://flexibleinvoices.com/products/advanced-sending-for-flexible-invoices/?utm_source=wp-admin-plugins&utm_medium=button&utm_campaign=flexible-invoices-advanced-sending';

		$doc_link = sprintf( esc_html__( 'To automate emails with invoices to your accountant buy the %2$s%1$s%3$s', 'flexible-invoices' ), esc_html__( 'Advanced Sending add-on for Flexible Invoices &rarr;', 'flexible-invoices' ), '<a target="_blank" href="' . $pro_url . '" style="color: #8f0350; font-weight: 700;">', '</a>' );
		$output   = sprintf( '%2$s%1$s%3$s', $doc_link, '<strong>', '</strong><br/>' );
		$output   .= sprintf( '%2$s%1$s%3$s', esc_html__( 'The add-on requires Flexible Invoices PRO.', 'flexible-invoices' ), '<strong>', '</strong>' );

		return $output;
	}

	public function get_docs_url(): string {
		$docs_url = 'https://wpde.sk/fi-sending-docs';
		if ( get_locale() === 'pl_PL' ) {
			$docs_url = 'https://wpde.sk/fi-sending-docs-pl';
		}

		return sprintf( esc_html__( 'Read more in the %1$splugin documentation &rarr;%2$s', 'flexible-invoices' ), '<a href="' . $docs_url . '" target="_blank" style="color: #4BB04E; font-weight: 700;"><strong>', '</strong></a>' );
	}

	/**
	 * Field definition.
	 *
	 * @return array
	 */
	protected function get_fields() {
		return [
			( new Header() )
				->set_name( 'no_value' )
				->set_description( $this->get_doc_link() )
				->set_label( esc_html__( 'Sending invoices', 'flexible-invoices' ) )
				->set_disabled(),
			( new CheckboxField() )
				->set_name( 'no_value' )
				->set_label( esc_html__( 'Sending invoices to customers', 'flexible-invoices' ) )
				->set_default_value( 'on' )
				->set_sublabel( esc_html__( 'Enable automatic mailing of invoices to customers', 'flexible-invoices' ) )
				->set_disabled(),
			( new CheckboxField() )
				->set_name( 'no_value' )
				->set_label( esc_html__( 'Attachments in the e-mail', 'flexible-invoices' ) )
				->set_sublabel( esc_html__( 'Attach PDF file to invoice email', 'flexible-invoices' ) )
				->set_disabled(),
			( new Header() )
				->set_name( 'no_value' )
				->set_label( esc_html__( 'Cyclical sending of invoices', 'flexible-invoices' ) )
				->set_description( esc_html__( 'Below you will set up a cyclical sending of ZIP files with invoices.', 'flexible-invoices' ) . ' ' . $this->get_docs_url() )
				->set_disabled(),
			( new MultipleInputTextField() )
				->set_name( 'no_value' )
				->set_label( esc_html__( 'Additional recipients', 'flexible-invoices' ) )
				->set_placeholder( esc_html__( 'E-mail address', 'flexible-invoices' ) )
				->set_description( esc_html__( 'Add additional recipients', 'flexible-invoices' ) )
				->set_disabled(),
			( new SelectField() )
				->set_label( esc_html__( 'Schedule for sending documents', 'flexible-invoices' ) )
				->set_name( 'no_value' )
				->set_description( esc_html__( 'Choose the period for which you want sent documents to the address from the "Additional Recipients" setting.', 'flexible-invoices' ) )
				->set_options(
					[
						'none'    => esc_html__( 'none', 'flexible-invoices' ),
						'daily'   => esc_html__( 'daily', 'flexible-invoices' ),
						'weekly'  => esc_html__( 'weekly', 'flexible-invoices' ),
						'monthly' => esc_html__( 'monthly', 'flexible-invoices' ),
					]
				)
				->set_default_value( 'none' )
				->set_disabled(),
			( new InputTextField() )
				->set_name( 'no_value' )
				->set_label( esc_html__( 'Email subject', 'flexible-invoices' ) )
				->set_placeholder( esc_html__( 'Invoices from {from_date} to {to_date}', 'flexible-invoices' ) )
				->set_default_value( EmailStrings::get_email_invoice_subject() )
				->set_description( esc_html__( 'You can use the following shortcodes: {site_title}, {site_url}, {admin_email}, {current_date}, {site_description}, {from_date}, {to_date}.', 'flexible-invoices' ) )
				->set_disabled(),
			( new WysiwygField() )
				->set_name( 'no_value' )
				->set_label( esc_html__( 'E-mail body', 'flexible-invoices' ) )
				->set_default_value( EmailStrings::get_email_invoice_body() )
				->set_description( esc_html__( 'You can use the following shortcodes: {site_title}, {site_url}, {admin_email}, {current_date}, {site_description}, {from_date}, {to_date}.', 'flexible-invoices' ) )
				->set_disabled(),

			( new Header() )
				->set_name( 'no_value' )
				->set_label( esc_html__( 'Cyclical sending of reports', 'flexible-invoices' ) )
				->set_description( esc_html__( 'Below you will set up a cyclical sending of reports.', 'flexible-invoices' ) . ' ' . $this->get_docs_url() )
				->set_disabled(),
			( new MultipleInputTextField() )
				->set_name( 'no_value' )
				->set_label( esc_html__( 'Additional recipients', 'flexible-invoices' ) )
				->set_placeholder( esc_html__( 'E-mail address', 'flexible-invoices' ) )
				->set_description( esc_html__( 'Add additional recipients.', 'flexible-invoices' ) )
				->set_attribute( 'data-disabled', $this->get_disabled() )
				->set_disabled(),
			( new SelectField() )
				->set_label( esc_html__( 'Schedule for sending reports', 'flexible-invoices' ) )
				->set_name( 'no_value' )
				->set_description( esc_html__( 'Choose the period for which you want the report automatically sent to the address from the "Additional Recipients" setting.', 'flexible-invoices' ) )
				->set_options(
					[
						'none'    => esc_html__( 'none', 'flexible-invoices' ),
						'daily'   => esc_html__( 'daily', 'flexible-invoices' ),
						'weekly'  => esc_html__( 'weekly', 'flexible-invoices' ),
						'monthly' => esc_html__( 'monthly', 'flexible-invoices' ),
					]
				)
				->set_default_value( 'none' )
				->set_attribute( 'data-disabled', $this->get_disabled() )
				->set_disabled(),
			( new InputTextField() )
				->set_name( 'no_value' )
				->set_label( esc_html__( 'Email subject', 'flexible-invoices' ) )
				->set_placeholder( esc_html__( 'Report from {from_date} to {to_date} ', 'flexible-invoices' ) )
				->set_default_value( EmailStrings::get_email_report_subject() )
				->set_description( esc_html__( 'You can use the following shortcodes: {site_title}, {site_url}, {admin_email}, {current_date}, {site_description}, {from_date}, {to_date}.', 'flexible-invoices' ) )
				->set_attribute( 'data-disabled', $this->get_disabled() )
				->set_disabled(),
			( new WysiwygField() )
				->set_name( 'no_value' )
				->set_label( esc_html__( 'E-mail body', 'flexible-invoices' ) )
				->set_description( esc_html__( 'You can use the following shortcodes: {site_title}, {site_url}, {admin_email}, {current_date}, {site_description}, {from_date}, {to_date}.', 'flexible-invoices' ) )
				->set_default_value( EmailStrings::get_email_report_body() )
				->set_attribute( 'data-disabled', $this->get_disabled() )
				->set_disabled(),
			( new SubmitField() )
				->set_name( 'no_value' )
				->set_label( esc_html__( 'Save changes', 'flexible-invoices' ) )
				->add_class( 'button-primary' )
				->set_disabled(),
		];
	}

	/**
	 * Get tab slug.
	 *
	 * @return string
	 */
	public static function get_tab_slug() {
		return 'fias-sending';
	}

	/**
	 * Get tab name.
	 *
	 * @return string
	 */
	public function get_tab_name() {
		return esc_html__( 'Advanced Sending', 'flexible-invoices' );
	}

	/**
	 * Is active.
	 *
	 * @return bool
	 */
	public static function is_active() {
		return true;
	}

}
