<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce\Checkout;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\Country;
/**
 * The two VAT error sentences used by checkout before invoices-checkout.
 */
class LegacyNipMessage
{
    /**
     * Country codes used by the old message choice in ValidateVatNumber.
     */
    private const COUNTRY_ISO_SLUG = ['AT', 'BE', 'BG', 'CHE', 'CY', 'CZ', 'DE', 'DK', 'EE', 'EL', 'ES', 'EU', 'FI', 'FR', 'GB', 'GR', 'HR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'RS', 'SI', 'SK', 'SE'];
    /**
     * Countries outside this list skipped VAT format checks before invoices-checkout.
     */
    public static function country_uses_format_check(?Country $country): bool
    {
        if (!$country instanceof Country) {
            return \true;
        }
        return in_array($country->get_code(), self::COUNTRY_ISO_SLUG, \true);
    }
    public static function for_country(?Country $country, string $label): string
    {
        $customer_country = $country instanceof Country ? $country->get_code() : '';
        $shop_country = (string) get_option('woocommerce_default_country', '');
        if ($shop_country === $customer_country && !in_array($shop_country, self::COUNTRY_ISO_SLUG, \true)) {
            return sprintf(
                /* translators: %s VAT number label from settings. */
                __('Please enter a valid %s. Do not enter hyphens or spaces. Optionally add country prefix (EU VAT Number).', 'flexible-invoices'),
                $label
            );
        }
        return sprintf(
            /* translators: %s VAT number label from settings. */
            __('Please enter a valid %s without hyphens and spaces, with valid country prefix (EU VAT Number).', 'flexible-invoices'),
            $label
        );
    }
}
