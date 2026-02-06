<?php

namespace WPDeskFIVendor\WPDesk\Forms\Field;

use WPDeskFIVendor\WPDesk\Forms\Sanitizer;
use WPDeskFIVendor\WPDesk\Forms\Sanitizer\TextFieldSanitizer;
class DatePickerField extends BasicField
{
    public function __construct()
    {
        $this->add_class('date-picker');
        $this->set_placeholder('YYYY-MM-DD');
    }
    public function get_sanitizer(): Sanitizer
    {
        return new TextFieldSanitizer();
    }
    public function get_template_name(): string
    {
        return 'input-date-picker';
    }
}
