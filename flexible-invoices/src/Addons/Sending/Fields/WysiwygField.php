<?php

namespace WPDesk\FlexibleInvoices\Addons\Sending\Fields;

use WPDeskFIVendor\WPDesk\Forms\Field;

/**
 * Define custom wysiwyg field.
 *
 * @package WPDesk\FIS\Settings\Fields
 */
class WysiwygField extends Field\WyswigField {

	public function get_template_name(): string {
		return 'wysiwyg';
	}

	/**
	 * @return false
	 */
	public function should_override_form_template(): bool {
		return false;
	}
}
