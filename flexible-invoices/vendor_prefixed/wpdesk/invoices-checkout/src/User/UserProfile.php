<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\User;

use InvalidArgumentException;
use WP_User;
use WP_Error;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Service\VatNumberValidator;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\Country;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\VatNumber;
/**
 * Handles adding, validating, and saving VAT fields in the WP-Admin User Profile.
 */
class UserProfile
{
    private ConfigInterface $config;
    private VatNumberValidator $validator;
    public function __construct(ConfigInterface $config, VatNumberValidator $validator)
    {
        $this->config = $config;
        $this->validator = $validator;
    }
    public function hooks(): void
    {
        add_filter('woocommerce_customer_meta_fields', [$this, 'add_customer_meta_fields']);
        add_action('user_profile_update_errors', [$this, 'validate_vat_fields'], 10, 3);
        add_action('personal_options_update', [$this, 'save_vat_fields']);
        add_action('edit_user_profile_update', [$this, 'save_vat_fields']);
    }
    /**
     * Integrates fields directly into WooCommerce's Customer Billing and Shipping Address sections on profile.php.
     */
    public function add_customer_meta_fields(array $show_fields): array
    {
        $sections = ['billing' => ['billing_company', $this->config->get_billing_company_user_key()], 'shipping' => ['shipping_company', $this->config->get_shipping_company_user_key()]];
        foreach ($sections as $section => $target_keys) {
            if (!$this->section_has_enabled_fields($section)) {
                continue;
            }
            if (!isset($show_fields[$section]['fields']) || !is_array($show_fields[$section]['fields'])) {
                $show_fields[$section]['fields'] = [];
            }
            $show_fields[$section]['fields'] = $this->inject_fields_into_section($show_fields[$section]['fields'], $section, $target_keys);
        }
        return $show_fields;
    }
    /**
     * Injects custom fields into a user profile address section.
     *
     * @param array $fields Existing fields.
     * @param string $section Section name (billing or shipping).
     * @param string[] $target_keys Keys to inject after.
     */
    private function inject_fields_into_section(array $fields, string $section, array $target_keys): array
    {
        $new_fields = [];
        $inserted = \false;
        foreach ($fields as $key => $field_args) {
            $new_fields[$key] = $field_args;
            if (in_array($key, $target_keys, \true)) {
                foreach ($this->config->get_checkout_fields() as $field) {
                    if ($field->is_enabled() && $field->get_section() === $section) {
                        $new_fields[$field->get_user_meta_key()] = $field->get_user_profile_args();
                    }
                }
                $inserted = \true;
            }
        }
        if (!$inserted) {
            foreach ($this->config->get_checkout_fields() as $field) {
                if ($field->is_enabled() && $field->get_section() === $section) {
                    $new_fields[$field->get_user_meta_key()] = $field->get_user_profile_args();
                }
            }
        }
        return $new_fields;
    }
    /**
     * @param string $section billing or shipping.
     */
    private function section_has_enabled_fields($section): bool
    {
        foreach ($this->config->get_checkout_fields() as $field) {
            if ($field->is_enabled() && $field->get_section() === $section) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * Validates VAT fields before saving profile in WP-Admin.
     *
     * @param WP_Error $errors
     * @param bool     $update
     * @param WP_User  $user
     */
    public function validate_vat_fields($errors, $update, $user): void
    {
        $user_id = (int) $user->ID;
        $billing_key = $this->config->get_billing_tax_id_user_key();
        $shipping_key = $this->config->get_shipping_tax_id_user_key();
        $wants_invoice = \true;
        if (ConfigInterface::BEHAVIOR_ASK_USER === $this->config->get_field_behavior()) {
            $want_key = $this->config->get_want_invoice_user_key();
            $wants_invoice = filter_var($_POST[$want_key] ?? \false, \FILTER_VALIDATE_BOOLEAN);
            // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        }
        if (!$wants_invoice) {
            return;
        }
        if (isset($_POST[$billing_key])) {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $billing_vat = trim(sanitize_text_field(wp_unslash($_POST[$billing_key])));
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            if (!empty($billing_vat)) {
                try {
                    $country = new Country($this->get_billing_country_from_request($user_id));
                } catch (InvalidArgumentException $ie) {
                    $errors->add('invalid_billing_vat', __('Billing country is required for VAT validation.', 'flexible-invoices'));
                    return;
                }
                $vat_number = new VatNumber($billing_vat);
                $result = $this->validator->validate($vat_number, $country);
                if (!$result->is_valid()) {
                    $errors->add($result->get_error_code(), $result->get_error_message());
                }
            }
        }
        if (isset($_POST[$shipping_key])) {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $shipping_vat = trim(sanitize_text_field(wp_unslash($_POST[$shipping_key])));
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            if (!empty($shipping_vat)) {
                try {
                    $country = new Country($this->get_shipping_country_from_request($user_id));
                } catch (InvalidArgumentException $ie) {
                    $errors->add('invalid_shipping_vat', __('Shipping country is required for VAT validation.', 'flexible-invoices'));
                    return;
                }
                $vat_number = new VatNumber($shipping_vat);
                $result = $this->validator->validate($vat_number, $country);
                if (!$result->is_valid()) {
                    $errors->add($result->get_error_code(), $result->get_error_message());
                }
            }
        }
    }
    private function get_billing_country_from_request(int $user_id): string
    {
        if (isset($_POST['billing_country'])) {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            return sanitize_text_field(wp_unslash($_POST['billing_country']));
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
        }
        return (string) get_user_meta($user_id, 'billing_country', \true);
    }
    private function get_shipping_country_from_request(int $user_id): string
    {
        if (isset($_POST['shipping_country'])) {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            return sanitize_text_field(wp_unslash($_POST['shipping_country']));
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
        }
        if (isset($_POST['billing_country'])) {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            return sanitize_text_field(wp_unslash($_POST['billing_country']));
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
        }
        $shipping_country = (string) get_user_meta($user_id, 'shipping_country', \true);
        if (!empty($shipping_country)) {
            return $shipping_country;
        }
        return (string) get_user_meta($user_id, 'billing_country', \true);
    }
    /**
     * Saves VAT fields from the user profile edit screen.
     *
     * @param int $user_id
     */
    public function save_vat_fields($user_id): void
    {
        if (!current_user_can('edit_user', $user_id)) {
            return;
        }
        $data = wp_unslash($_POST);
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        $wants_invoice = \true;
        if (ConfigInterface::BEHAVIOR_ASK_USER === $this->config->get_field_behavior()) {
            $want_key = $this->config->get_want_invoice_user_key();
            $wants_invoice = filter_var($data[$want_key] ?? \false, \FILTER_VALIDATE_BOOLEAN);
        }
        $billing_nip = $this->config->get_billing_tax_id_user_key();
        $shipping_nip = $this->config->get_shipping_tax_id_user_key();
        foreach ($this->config->get_checkout_fields() as $field) {
            if ($field->is_enabled()) {
                $key = $field->get_user_meta_key();
                $raw = $data[$key] ?? '';
                if (!$wants_invoice && ($key === $billing_nip || $key === $shipping_nip)) {
                    $raw = '';
                }
                update_user_meta((int) $user_id, $key, $field->sanitize_for_storage($raw));
            }
        }
    }
}
