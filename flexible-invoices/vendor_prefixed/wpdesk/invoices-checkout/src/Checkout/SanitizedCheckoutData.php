<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\InvoicesCheckout;
/**
 * Extracts and sanitizes checkout data from various sources (classic checkout string or Store API array).
 */
class SanitizedCheckoutData
{
    private array $data;
    private ConfigInterface $config;
    private function __construct(array $data, ConfigInterface $config)
    {
        $this->data = $data;
        $this->config = $config;
    }
    /**
     * Creates an instance from a URL-encoded string (classic checkout AJAX `post_data`).
     * Handles wp_parse_args and wp_unslash internally — caller should pass the raw string.
     */
    public static function from_url_encoded_string(string $posted_data, ConfigInterface $config): self
    {
        return new self(wp_unslash(wp_parse_args($posted_data)), $config);
    }
    /**
     * Creates an instance from an array (Store API extensions, WC validated data, or $_POST).
     * Applies wp_unslash to the entire array on entry.
     */
    public static function from_array(array $data, ConfigInterface $config): self
    {
        return new self(wp_unslash($data), $config);
    }
    /**
     * Classic checkout place-order payload from WooCommerce omits confirmation checkboxes:
     * they are rendered in order review, not registered as checkout fields.
     */
    public static function from_woocommerce_checkout(array $data, ConfigInterface $config): self
    {
        return self::from_array(self::merge_posted_confirmation_fields($data, $config), $config);
    }
    /**
     * @param array<string, mixed> $data Posted checkout data.
     * @return array<string, mixed>
     */
    private static function merge_posted_confirmation_fields(array $data, ConfigInterface $config): array
    {
        foreach ([$config->get_location_confirmation_key(), $config->get_vat_confirmation_key()] as $key) {
            if (isset($data[$key])) {
                continue;
            }
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            if (isset($_POST[$key])) {
                // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                $data[$key] = wp_unslash($_POST[$key]);
            }
        }
        return $data;
    }
    public function wants_invoice(): bool
    {
        if (ConfigInterface::BEHAVIOR_ALWAYS_SHOW === $this->config->get_field_behavior()) {
            return \true;
        }
        $key = $this->config->get_want_invoice_key();
        $user_key = $this->config->get_want_invoice_user_key();
        $raw = $this->data[InvoicesCheckout::KEY_WANT_INVOICE] ?? $this->data[$key] ?? $this->data[$user_key] ?? \false;
        return filter_var($raw, \FILTER_VALIDATE_BOOLEAN);
    }
    public function get_billing_tax_id(): string
    {
        $key = $this->config->get_billing_tax_id_key();
        $user_key = $this->config->get_billing_tax_id_user_key();
        $raw = $this->data[InvoicesCheckout::KEY_BILLING_TAX_ID] ?? $this->data[$key] ?? $this->data[$user_key] ?? '';
        return trim((string) sanitize_text_field($raw));
    }
    public function get_shipping_tax_id(): string
    {
        $key = $this->config->get_shipping_tax_id_key();
        $user_key = $this->config->get_shipping_tax_id_user_key();
        $raw = $this->data[InvoicesCheckout::KEY_SHIPPING_TAX_ID] ?? $this->data[$key] ?? $this->data[$user_key] ?? '';
        return trim((string) sanitize_text_field($raw));
    }
    public function get_billing_company(): string
    {
        $key = $this->config->get_billing_company_key();
        $user_key = $this->config->get_billing_company_user_key();
        $raw = $this->data[InvoicesCheckout::KEY_BILLING_COMPANY] ?? $this->data[$key] ?? $this->data[$user_key] ?? '';
        return trim((string) sanitize_text_field($raw));
    }
    public function get_shipping_company(): string
    {
        $key = $this->config->get_shipping_company_key();
        $user_key = $this->config->get_shipping_company_user_key();
        $raw = $this->data[InvoicesCheckout::KEY_SHIPPING_COMPANY] ?? $this->data[$key] ?? $this->data[$user_key] ?? '';
        return trim((string) sanitize_text_field($raw));
    }
    public function get_billing_country(): string
    {
        $raw = $this->data['billing_country'] ?? '';
        return trim((string) sanitize_text_field($raw));
    }
    public function get_shipping_country(): string
    {
        $raw = !empty($this->data['shipping_country']) ? $this->data['shipping_country'] : $this->data['billing_country'] ?? '';
        return trim((string) sanitize_text_field($raw));
    }
    public function has_location_confirmation(): bool
    {
        $key = $this->config->get_location_confirmation_key();
        return filter_var($this->data[$key] ?? \false, \FILTER_VALIDATE_BOOLEAN);
    }
    public function has_vat_confirmation(): bool
    {
        $key = $this->config->get_vat_confirmation_key();
        return filter_var($this->data[$key] ?? \false, \FILTER_VALIDATE_BOOLEAN);
    }
    /**
     * Returns the underlying array data.
     *
     * Used only when integrating with legacy hooks that require raw array format.
     */
    public function get_data(): array
    {
        return $this->data;
    }
}
