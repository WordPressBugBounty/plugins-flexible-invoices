<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\Validator;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\ValidationResult;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\Country;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\VatNumber;
/**
 * Rejects an empty VAT number. Format validators treat an empty number as valid.
 */
class NonEmptyVatNumber implements VatValidator
{
    public function validate(VatNumber $vat_number, ?Country $country): ValidationResult
    {
        if ('' === $vat_number->get_normalized()) {
            return ValidationResult::failure(ValidationResult::ERROR_VAT_REQUIRED, __('VAT Number is a required field.', 'flexible-invoices'));
        }
        return ValidationResult::success();
    }
}
