<?php

namespace WPDeskFIVendor\WPDesk\Forms\Sanitizer;

use WPDeskFIVendor\WPDesk\Forms\Sanitizer;
class EmailSanitizer implements Sanitizer
{
    public function sanitize($value): string
    {
        return sanitize_email($value);
    }
}
