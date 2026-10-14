<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\Validator;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\Country;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\VatNumber;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\ValidationResult;
/**
 * A validator that performs no validation and always returns true.
 * Used when validation is disabled.
 */
class NoValidation implements VatValidator
{
    /**
     * @inheritDoc
     */
    public function validate(VatNumber $vat_number, ?Country $country): ValidationResult
    {
        return ValidationResult::success();
    }
}
