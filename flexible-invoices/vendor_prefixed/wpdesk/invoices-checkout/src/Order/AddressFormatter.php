<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Order;

use WC_Order;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
/**
 * Handles injecting VAT details into formatted order addresses (e.g., for emails, invoices).
 */
class AddressFormatter
{
    private ConfigInterface $config;
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }
    /**
     * Hooks into WordPress.
     */
    public function hooks(): void
    {
        add_filter('woocommerce_order_formatted_billing_address', [$this, 'add_billing_vat_to_address_array'], 10, 2);
        add_filter('woocommerce_order_formatted_shipping_address', [$this, 'add_shipping_vat_to_address_array'], 10, 2);
        // FCF support. Override settings to properly display labels and values from lib.
        add_filter('woocommerce_order_formatted_billing_address', [$this, 'format_invoice_ask_for_checkout_fields_plugins'], 1000000, 2);
        add_filter('woocommerce_order_formatted_shipping_address', [$this, 'format_invoice_ask_for_checkout_fields_plugins'], 1000000, 2);
        add_filter('woocommerce_order_formatted_billing_address', [$this, 'hide_empty_tax_id_labels'], 1000000, 2);
        add_filter('woocommerce_order_formatted_shipping_address', [$this, 'hide_empty_tax_id_labels'], 1000000, 2);
        add_filter('woocommerce_formatted_address_replacements', [$this, 'add_address_replacements'], 10, 2);
        add_filter('woocommerce_localisation_address_formats', [$this, 'add_address_formats'], 10, 1);
    }
    /**
     * Adds billing fields to the raw address array before it's formatted.
     *
     * @param array $address
     * @param WC_Order $order
     * @return array
     */
    public function add_billing_vat_to_address_array($address, $order): array
    {
        foreach ($this->config->get_checkout_fields() as $field) {
            if ($field->is_enabled() && 'billing' === $field->get_section()) {
                $address[$field->get_id()] = $order->get_meta($field->get_id());
            }
        }
        return $address;
    }
    /**
     * Adds shipping fields to the raw address array before it's formatted.
     *
     * @param array $address
     * @param WC_Order $order
     * @return array
     */
    public function add_shipping_vat_to_address_array($address, $order): array
    {
        foreach ($this->config->get_checkout_fields() as $field) {
            if ($field->is_enabled() && 'shipping' === $field->get_section()) {
                $address[$field->get_id()] = $order->get_meta($field->get_id());
            }
        }
        return $address;
    }
    /**
     * Flexible Checkout Fields copies the stored checkbox into the address array as yes/no.
     *
     * @param array    $address Address fields about to be formatted.
     * @param WC_Order $order   Order being displayed.
     * @return array
     */
    public function format_invoice_ask_for_checkout_fields_plugins($address, $order)
    {
        if (!is_array($address) || !$order instanceof WC_Order) {
            return $address;
        }
        $checkout_key = $this->config->get_want_invoice_user_key();
        $raw = (string) $order->get_meta($this->config->get_want_invoice_key());
        $formatted = null;
        foreach ($this->config->get_checkout_fields() as $field) {
            if ($field->is_enabled() && $field->get_checkout_key() === $checkout_key) {
                $formatted = $field->format_address_replacement($raw);
                break;
            }
        }
        if (null === $formatted) {
            return $address;
        }
        // FCF stores the value under the checkout key and under the key with billing_/shipping_ removed.
        // The address format prints the short key.
        $short_key = preg_replace('/^(billing_|shipping_)/', '', $checkout_key, 1);
        foreach ([$checkout_key, $short_key] as $key) {
            if (!isset($address[$key]) || !$this->is_raw_invoice_ask($address[$key])) {
                continue;
            }
            $address[$key] = $formatted;
        }
        return $address;
    }
    /**
     * Flexible Checkout Fields prints a custom field label even when the value is empty, so the line is "NIP:".
     *
     * @param array    $address Address fields about to be formatted.
     * @param WC_Order $order   Order being displayed.
     * @return array
     */
    public function hide_empty_tax_id_labels($address, $order)
    {
        if (!is_array($address) || !$order instanceof WC_Order) {
            return $address;
        }
        $keys = [$this->config->get_billing_tax_id_user_key(), $this->config->get_shipping_tax_id_user_key(), $this->config->get_billing_tax_id_key(), $this->config->get_shipping_tax_id_key()];
        foreach ($keys as $checkout_key) {
            $short_key = preg_replace('/^(billing_|shipping_)/', '', $checkout_key, 1);
            foreach ([$checkout_key, $short_key] as $key) {
                if (!isset($address[$key]) || !$this->is_label_without_value($address[$key])) {
                    continue;
                }
                $address[$key] = '';
            }
        }
        return $address;
    }
    /**
     * @param mixed $value Address value printed by Flexible Checkout Fields.
     */
    private function is_label_without_value($value): bool
    {
        $value = trim(wp_strip_all_tags((string) $value));
        return '' !== $value && preg_match('/:\s*$/u', $value);
    }
    /**
     * @param mixed $value Address value printed by Flexible Checkout Fields.
     */
    private function is_raw_invoice_ask($value): bool
    {
        return in_array(strtolower(trim((string) $value)), ['yes', 'no', '1', '0', 'true', 'false'], \true);
    }
    /**
     * Defines tag replacements for WooCommerce address formatting.
     *
     * @param array $replacements
     * @param array $args
     * @return array
     */
    public function add_address_replacements($replacements, $args): array
    {
        foreach ($this->config->get_checkout_fields() as $field) {
            if ($field->is_enabled()) {
                $value = $args[$field->get_id()] ?? '';
                $replacements[$field->get_address_format_tag()] = $field->format_address_replacement((string) $value);
            }
        }
        return $replacements;
    }
    /**
     * Injects the tags into the country-specific address formats.
     *
     * @param array $formats
     * @return array
     */
    public function add_address_formats($formats): array
    {
        foreach ($formats as $country => $format) {
            foreach ($this->config->get_checkout_fields() as $field) {
                if ($field->is_enabled()) {
                    $tag = $field->get_address_format_tag();
                    if (!str_contains($formats[$country], $tag)) {
                        $formats[$country] .= "\n" . $tag;
                    }
                }
            }
        }
        return $formats;
    }
}
