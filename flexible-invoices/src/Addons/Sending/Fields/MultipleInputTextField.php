<?php

namespace WPDesk\FlexibleInvoices\Addons\Sending\Fields;

use WPDeskFIVendor\WPDesk\Forms\Field\InputTextField;

/**
 * Define multiple input text field.
 *
 * @package WPDesk\FIS\Settings\Fields
 */
class MultipleInputTextField extends InputTextField {

	public function get_template_name(): string {
		return 'input-text-multiple';
	}
}
