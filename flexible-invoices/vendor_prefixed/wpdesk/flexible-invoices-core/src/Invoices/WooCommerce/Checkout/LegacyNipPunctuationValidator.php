<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce\Checkout;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\ValidationResult;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\Validator\VatValidator;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\Country;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\VatNumber;
/**
 * Rejects spaces and hyphens before the library format checks.
 *
 * Checkout before invoices-checkout compared the typed string with the string
 * after spaces, hyphens, commas and dots were removed. The library normalizes
 * those characters away and then accepts the number. This check stops the chain
 * on that older failure and keeps the old sentence.
 */
class LegacyNipPunctuationValidator implements VatValidator
{
    private string $label;
    public function __construct(string $label)
    {
        $this->label = $label;
    }
    public function validate(VatNumber $vat_number, ?Country $country): ValidationResult
    {
        $raw = $vat_number->get_raw();
        if ('' === $raw || !LegacyNipMessage::country_uses_format_check($country)) {
            return ValidationResult::success();
        }
        $stripped = strtoupper(trim($raw));
        // The hyphen is escaped. An unescaped hyphen between space and comma is a range and does not match "-".
        $stripped = (string) preg_replace('/[ \-,.]/', '', $stripped);
        if (strlen($stripped) === strlen($raw)) {
            return ValidationResult::success();
        }
        return ValidationResult::failure(ValidationResult::ERROR_INVALID_VAT, LegacyNipMessage::for_country($country, $this->label));
    }
}
