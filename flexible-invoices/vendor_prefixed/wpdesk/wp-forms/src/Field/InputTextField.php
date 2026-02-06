<?php

namespace WPDeskFIVendor\WPDesk\Forms\Field;

use WPDeskFIVendor\WPDesk\Forms\Sanitizer;
use WPDeskFIVendor\WPDesk\Forms\Sanitizer\TextFieldSanitizer;
class InputTextField extends BasicField
{
    public function get_sanitizer(): Sanitizer
    {
        return new TextFieldSanitizer();
    }
    public function get_template_name(): string
    {
        return 'input-text';
    }
}
