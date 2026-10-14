<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject;

/**
 * Value Object for VAT numbers.
 * Encapsulates validation of structure and normalization.
 */
class VatNumber
{
    private string $raw_vat_number;
    private ?string $normalized_vat_number = null;
    public function __construct(string $raw_vat_number)
    {
        $this->raw_vat_number = $raw_vat_number;
    }
    private function normalize(): string
    {
        $vat_number = strtoupper(trim($this->raw_vat_number));
        return (string) preg_replace('/[ \-,.]/', '', $vat_number);
    }
    public function get_raw(): string
    {
        return $this->raw_vat_number;
    }
    public function get_normalized(): string
    {
        if (isset($this->normalized_vat_number)) {
            return $this->normalized_vat_number;
        }
        $this->normalized_vat_number = $this->normalize();
        return $this->normalized_vat_number;
    }
}
