<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject;

use InvalidArgumentException;
/**
 * Value Object representing a country code.
 */
class Country
{
    private string $code;
    public function __construct(string $code)
    {
        $code = strtoupper(trim($code));
        if (empty($code)) {
            throw new InvalidArgumentException(__('Country code cannot be empty.', 'flexible-invoices'));
        }
        $this->code = $code;
    }
    public function get_code(): string
    {
        return $this->code;
    }
    public function __toString(): string
    {
        return $this->code;
    }
}
