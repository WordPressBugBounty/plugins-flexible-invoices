<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\Validator;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\Country;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\VatNumber;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\ValidationResult;
/**
 * Interface VatValidator
 *
 * Checks if passed VAT Number is valid
 */
interface VatValidator
{
    /**
     * @param VatNumber  $vat_number VAT Number.
     * @param ?Country   $country    Country. Null only when the VAT number is empty.
     *
     * @return ValidationResult
     */
    public function validate(VatNumber $vat_number, ?Country $country): ValidationResult;
}
