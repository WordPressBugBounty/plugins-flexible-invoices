<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\Validator;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\Country;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\VatNumber;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\ValidationResult;
/**
 * Validates the control sum of a VAT number.
 */
class ControlSumValidation implements VatValidator
{
    /**
     * @inheritDoc
     */
    public function validate(VatNumber $vat_number, ?Country $country): ValidationResult
    {
        if ('' === $vat_number->get_normalized()) {
            return ValidationResult::success();
        }
        if (!$country instanceof Country) {
            return ValidationResult::failure(ValidationResult::ERROR_COUNTRY_REQUIRED, __('Country is required for VAT validation.', 'flexible-invoices'));
        }
        // Currently only PL NIP control sum is supported.
        if ('PL' === $country->get_code()) {
            $is_valid = $this->is_valid_nip($vat_number->get_normalized());
            if (!$is_valid) {
                return ValidationResult::failure(ValidationResult::ERROR_INVALID_CONTROL_SUM, __('The VAT number is invalid. Please check the data and re-enter the number.', 'flexible-invoices'));
            }
        }
        return ValidationResult::success();
    }
    /**
     * Validates Polish NIP control sum.
     */
    private function is_valid_nip(string $vat_number): bool
    {
        // Remove 'PL' prefix if exists.
        $nip = preg_replace('/^PL/', '', $vat_number);
        if (10 !== strlen($nip)) {
            return \false;
        }
        $weights = [6, 5, 7, 2, 3, 4, 5, 6, 7];
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $nip[$i] * $weights[$i];
        }
        $control_digit = $sum % 11;
        return $control_digit === (int) $nip[9];
    }
}
