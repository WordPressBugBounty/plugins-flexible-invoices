<?php

namespace WPDeskFIVendor\WPDesk\Forms\Sanitizer;

use WPDeskFIVendor\WPDesk\Forms\Sanitizer;
class NoSanitize implements Sanitizer
{
    public function sanitize($value)
    {
        return $value;
    }
}
