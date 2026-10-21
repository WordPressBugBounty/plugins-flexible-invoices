<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers;

/**
 * Get country label from iso slug.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Helpers
 */
class Countries
{
    public const EU_MEMBER_CODES = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HU', 'HR', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'];
    /**
     * @param string $slug
     *
     * @return string
     */
    public static function get_country_label(string $slug): string
    {
        if (WooCommerce::is_active() && strlen($slug) <= 3) {
            $countries = WC()->countries->get_countries();
            if (isset($countries)) {
                return $countries[$slug] ?? $slug;
            }
        }
        return $slug;
    }
    /**
     * @param string $slug
     * @param string $country
     *
     * @return string
     */
    public static function get_country_state_label(string $slug, string $country): string
    {
        if (WooCommerce::is_active() && strlen($slug) <= 3) {
            $states = WC()->countries->get_states($country);
            if (isset($states)) {
                return $states[$slug] ?? $slug;
            }
        }
        return $slug;
    }
    /**
     * @param string $name
     * @param string $value
     * @param string $country
     *
     * @return string
     */
    public static function generate_states_select(string $name, string $value = '', string $country = ''): string
    {
        if (WooCommerce::is_active()) {
            $states = WC()->countries->get_states($country);
            $output = '<select class="state-select2 medium hs-beacon-search" name="' . $name . '" id="customer_state" >';
            foreach ($states as $state_code => $state_name) {
                $output .= '<option ' . selected($state_code, $value) . ' value="' . $state_code . '">' . $state_name . '</option>' . \PHP_EOL;
                //@phpstan-ignore-line
            }
            $output .= '</select>' . \PHP_EOL;
        } else {
            $output = '<input type="text" class="medium hs-beacon-search" name="' . $name . '" id="customer_state" />';
        }
        return $output;
    }
    /**
     * Get filtered states.
     *
     * @return array
     */
    public static function get_states(): array
    {
        $states = [];
        if (WooCommerce::is_active()) {
            $countries_states = WC()->countries->get_states();
            foreach ($countries_states as $country_id => $country_states) {
                if (!empty($country_states)) {
                    $states[$country_id] = $country_states;
                }
            }
        }
        return $states;
    }
    /**
     * EU member codes, replaced by the WooCommerce list when that API is loaded.
     */
    public static function get_eu_country_codes(): array
    {
        $countries = self::EU_MEMBER_CODES;
        $wc_countries = null;
        if (WooCommerce::is_active()) {
            $wc_countries = WC()->countries;
        }
        if (null !== $wc_countries) {
            $countries = $wc_countries->get_european_union_countries();
        }
        $codes = [];
        foreach ($countries as $code) {
            $codes[] = strtoupper(trim((string) $code));
        }
        return array_values(array_unique($codes));
    }
    /**
     * @param string $country_code ISO 3166-1 alpha-2.
     */
    public static function is_eu_country(string $country_code): bool
    {
        $country_code = strtoupper(trim($country_code));
        return '' !== $country_code && in_array($country_code, self::get_eu_country_codes(), \true);
    }
}
