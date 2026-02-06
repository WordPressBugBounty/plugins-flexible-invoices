<?php

namespace WPDeskFIVendor\WPDesk\Forms\Validator;

use WPDeskFIVendor\WPDesk\Forms\Validator;
class RequiredValidator implements Validator
{
    public function is_valid($value): bool
    {
        return $value !== null;
    }
    public function get_messages(): array
    {
        return [];
    }
}
