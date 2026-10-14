<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce\Checkout;

use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Keeps pre-library address placeholders working beside the invoices-checkout tags.
 *
 * Order meta stays `_billing_vat_number` and `_billing_invoice_ask`.
 * The library fills `{_billing_vat_number}` and `{_billing_invoice_ask}`.
 * Older Woo address formats and custom emails still use `{vat_number}` and `{invoice_ask}`.
 * Both tags get the same text. When a format already contains the old tag, the new tag
 * is removed so the value is not printed twice.
 */
class LegacyAddressPlaceholders implements Hookable
{
    /**
     * New library tag => placeholder used before invoices-checkout.
     */
    private const PAIRS = ['{' . InvoicesCheckoutConfig::BILLING_TAX_ID_KEY . '}' => '{vat_number}', '{' . InvoicesCheckoutConfig::WANT_INVOICE_KEY . '}' => '{invoice_ask}'];
    public function hooks()
    {
        add_filter('woocommerce_formatted_address_replacements', [$this, 'alias_placeholders'], 100, 2);
        add_filter('woocommerce_localisation_address_formats', [$this, 'drop_duplicate_placeholder_lines'], 999);
        add_filter('woocommerce_order_formatted_billing_address', [$this, 'alias_billing_address_keys'], 20);
    }
    /**
     * Copies a filled placeholder onto its legacy or library twin.
     *
     * @param mixed $replacements Replacement map.
     * @param mixed $args         Address fields. Unused.
     *
     * @return mixed
     */
    public function alias_placeholders($replacements, $args)
    {
        // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
        if (!is_array($replacements)) {
            return $replacements;
        }
        foreach (self::PAIRS as $library_tag => $legacy_tag) {
            if (isset($replacements[$library_tag]) && !isset($replacements[$legacy_tag])) {
                $replacements[$legacy_tag] = $replacements[$library_tag];
            } elseif (isset($replacements[$legacy_tag]) && !isset($replacements[$library_tag])) {
                $replacements[$library_tag] = $replacements[$legacy_tag];
            }
        }
        return $replacements;
    }
    /**
     * Leaves one VAT or invoice-ask line when a format contains both tags.
     *
     * @param mixed $formats Country address formats.
     *
     * @return mixed
     */
    public function drop_duplicate_placeholder_lines($formats)
    {
        if (!is_array($formats)) {
            return $formats;
        }
        foreach ($formats as $country => $format) {
            if (!is_string($format)) {
                continue;
            }
            foreach (self::PAIRS as $library_tag => $legacy_tag) {
                if (\false === strpos($format, $legacy_tag) || \false === strpos($format, $library_tag)) {
                    continue;
                }
                $format = str_replace(["\n" . $library_tag, $library_tag], '', $format);
            }
            $formats[$country] = $format;
        }
        return $formats;
    }
    /**
     * Exposes library address keys under the names older code reads from the address array.
     *
     * @param mixed $address Billing address about to be formatted.
     *
     * @return mixed
     */
    public function alias_billing_address_keys($address)
    {
        if (!is_array($address)) {
            return $address;
        }
        $keys = [InvoicesCheckoutConfig::BILLING_TAX_ID_KEY => 'vat_number', InvoicesCheckoutConfig::WANT_INVOICE_KEY => 'invoice_ask'];
        foreach ($keys as $library_key => $legacy_key) {
            if (!array_key_exists($legacy_key, $address) && array_key_exists($library_key, $address)) {
                $address[$legacy_key] = $address[$library_key];
            }
        }
        return $address;
    }
}
