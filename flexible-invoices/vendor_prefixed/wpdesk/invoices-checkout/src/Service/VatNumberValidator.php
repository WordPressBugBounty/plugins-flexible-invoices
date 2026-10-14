<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Service;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\ValidationResult;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\Validator\VatValidator;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\Country;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\VatNumber;
/**
 * Runs the configured VAT validators and stops at the first failure.
 */
class VatNumberValidator
{
    /**
     * @var VatValidator[]
     */
    private array $validators;
    /**
     * @param VatValidator[] $validators
     */
    public function __construct(array $validators)
    {
        $this->validators = $validators;
    }
    public function validate(VatNumber $vat_number, ?Country $country): ValidationResult
    {
        foreach ($this->validators as $validator) {
            $result = $validator->validate($vat_number, $country);
            if (!$result->is_valid()) {
                return $result;
            }
        }
        return ValidationResult::success();
    }
}
