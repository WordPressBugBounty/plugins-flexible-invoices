<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Service;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
/**
 * Helper class for WooCommerce related checks and utilities.
 */
class WooCommerceHelper
{
    private ConfigInterface $config;
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }
    /**
     * Checks if the given country code is a European Union country.
     */
    public function is_eu_country(string $country_code): bool
    {
        $eu_countries = WC()->countries->get_european_union_countries('eu_vat');
        return in_array($country_code, $eu_countries, \true);
    }
    /**
     * Checks if cart has OSS products.
     */
    public function cart_has_oss_products(): bool
    {
        $oss_classes = $this->config->get_oss_tax_classes();
        if (!WC()->cart || empty($oss_classes)) {
            return \false;
        }
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product = $cart_item['data'] ?? null;
            if ($product && is_a($product, 'WC_Product')) {
                $tax_class = $product->get_tax_class();
                if ('' === $tax_class) {
                    $tax_class = 'standard';
                }
                if (in_array($tax_class, $oss_classes, \true)) {
                    return \true;
                }
            }
        }
        return \false;
    }
}
