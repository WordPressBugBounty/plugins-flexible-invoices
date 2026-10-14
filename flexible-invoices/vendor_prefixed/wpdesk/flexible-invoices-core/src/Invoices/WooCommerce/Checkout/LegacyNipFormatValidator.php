<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce\Checkout;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\ValidationResult;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\Validator\ControlSumValidation;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\Validator\LengthValidation;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\Validator\VatValidator;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\Country;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\VatNumber;
/**
 * Runs the library length and control-sum checks, then reports the pre-library message.
 *
 * The validator chain stops at the first failure, so this replaces LengthValidation
 * and ControlSumValidation. VIES stays a separate validator after this one.
 */
class LegacyNipFormatValidator implements VatValidator
{
    private string $label;
    private LengthValidation $length_validation;
    private ControlSumValidation $control_sum_validation;
    public function __construct(string $label)
    {
        $this->label = $label;
        $this->length_validation = new LengthValidation();
        $this->control_sum_validation = new ControlSumValidation();
    }
    public function validate(VatNumber $vat_number, ?Country $country): ValidationResult
    {
        if (!$this->has_customer_country_prefix($vat_number, $country)) {
            return ValidationResult::failure(ValidationResult::ERROR_INVALID_VAT, LegacyNipMessage::for_country($country, $this->label));
        }
        foreach ([$this->length_validation, $this->control_sum_validation] as $validator) {
            $result = $validator->validate($vat_number, $country);
            if ($result->is_valid()) {
                continue;
            }
            $error_code = $result->get_error_code();
            if (!is_string($error_code) || '' === $error_code) {
                $error_code = ValidationResult::ERROR_INVALID_VAT;
            }
            return ValidationResult::failure($error_code, LegacyNipMessage::for_country($country, $this->label));
        }
        return ValidationResult::success();
    }
    /**
     * Checkout before invoices-checkout rejected a number that did not start with the customer country.
     * A bare number was accepted only when the shop country option was exactly that two-letter code.
     * WooCommerce usually stores the option as PL:MAZ, so a Polish NIP without PL still fails.
     * The library treats the Polish prefix as optional, which this check closes.
     */
    private function has_customer_country_prefix(VatNumber $vat_number, ?Country $country): bool
    {
        if (!$country instanceof Country || !LegacyNipMessage::country_uses_format_check($country)) {
            return \true;
        }
        $normalized = $vat_number->get_normalized();
        if ('' === $normalized) {
            return \true;
        }
        $customer_country = $country->get_code();
        if (substr($normalized, 0, 2) === $customer_country) {
            return \true;
        }
        $shop_country = get_option('woocommerce_default_country', '');
        return is_string($shop_country) && $shop_country === $customer_country;
    }
}
