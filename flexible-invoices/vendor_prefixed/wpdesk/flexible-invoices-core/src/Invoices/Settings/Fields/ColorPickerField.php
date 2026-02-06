<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields;

use WPDeskFIVendor\WPDesk\Forms\Field\InputTextField;
/**
 * Color picker field.
 *
 * @package WPDesk\FIT\Settings\Fields
 */
class ColorPickerField extends InputTextField
{
    public function get_template_name(): string
    {
        return 'color-picker-input';
    }
}
