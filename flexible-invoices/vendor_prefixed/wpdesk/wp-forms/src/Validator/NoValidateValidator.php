<?php

namespace WPDeskFIVendor\WPDesk\Forms\Validator;

use WPDeskFIVendor\WPDesk\Forms\Validator;
class NoValidateValidator implements Validator
{
    public function is_valid($value): bool
    {
        return \true;
    }
    public function get_messages(): array
    {
        return [];
    }
}
