<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\User;

use InvalidArgumentException;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Service\VatNumberValidator;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\Country;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\VatNumber;
/**
 * Handles adding, validating, and saving VAT fields in My Account edit address forms.
 */
class MyAccountAddress
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
        add_filter('woocommerce_address_to_edit', [$this, 'add_my_account_address_fields'], 10, 2);
        add_action('woocommerce_after_save_address_validation', [$this, 'validate_my_account_address_save'], 10, 2);
        add_action('woocommerce_customer_save_address', [$this, 'save_my_account_vat'], 10, 2);
    }
    /**
     * Adds VAT and Requested Invoice fields to My Account edit address forms.
     *
     * @param array  $fields
     * @param string $load_address
     * @return array
     */
    public function add_my_account_address_fields($fields, $load_address)
    {
        $user_id = get_current_user_id();
        foreach ($this->config->get_checkout_fields() as $field) {
            if ($field->is_enabled() && $field->get_section() === $load_address) {
                $fields[$field->get_checkout_key()] = $field->get_my_account_args($user_id);
            }
        }
        return $fields;
    }
    /**
     * Validates VAT number when saving address in My Account.
     *
     * @param int    $user_id
     * @param string $load_address
     */
    public function validate_my_account_address_save($user_id, $load_address): void
    {
        if ('billing' === $load_address) {
            $wants_invoice = \true;
            if (ConfigInterface::BEHAVIOR_ASK_USER === $this->config->get_field_behavior()) {
                $want_key = $this->config->get_want_invoice_user_key();
                // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
                $wants_invoice = filter_var($_POST[$want_key] ?? \false, \FILTER_VALIDATE_BOOLEAN);
            }
            if (!$wants_invoice) {
                return;
            }
            $vat_key = $this->config->get_billing_tax_id_user_key();
            $country_str = $this->get_billing_country_from_request((int) $user_id);
            $this->validate_vat_for_section($vat_key, $country_str);
        } elseif ('shipping' === $load_address) {
            $wants_invoice = \true;
            if (ConfigInterface::BEHAVIOR_ASK_USER === $this->config->get_field_behavior()) {
                $want_key_user = $this->config->get_want_invoice_user_key();
                $wants_invoice = filter_var(get_user_meta($user_id, $want_key_user, \true), \FILTER_VALIDATE_BOOLEAN);
            }
            if (!$wants_invoice) {
                return;
            }
            $vat_key = $this->config->get_shipping_tax_id_user_key();
            $country_str = $this->get_shipping_country_from_request((int) $user_id);
            $this->validate_vat_for_section($vat_key, $country_str);
        }
    }
    /**
     * Validates a single VAT field.
     */
    private function validate_vat_for_section(string $vat_key, string $country_code): void
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        $vat_value = trim(sanitize_text_field(wp_unslash($_POST[$vat_key] ?? '')));
        if (empty($vat_value)) {
            return;
        }
        try {
            $country = new Country($country_code);
        } catch (InvalidArgumentException $ie) {
            wc_add_notice(__('Country is required for VAT validation.', 'flexible-invoices'), 'error');
            return;
        }
        $vat_number = new VatNumber($vat_value);
        $result = $this->validator->validate($vat_number, $country);
        if (!$result->is_valid()) {
            wc_add_notice($result->get_error_message(), 'error');
        }
    }
    private function get_billing_country_from_request(int $user_id): string
    {
        if (isset($_POST['billing_country'])) {
            //phpcs:ignore WordPress.Security.NonceVerification.Missing
            return sanitize_text_field(wp_unslash($_POST['billing_country']));
            //phpcs:ignore WordPress.Security.NonceVerification.Missing
        }
        return (string) get_user_meta($user_id, 'billing_country', \true);
    }
    private function get_shipping_country_from_request(int $user_id): string
    {
        if (isset($_POST['shipping_country'])) {
            //phpcs:ignore WordPress.Security.NonceVerification.Missing
            return sanitize_text_field(wp_unslash($_POST['shipping_country']));
            //phpcs:ignore WordPress.Security.NonceVerification.Missing
        }
        if (isset($_POST['billing_country'])) {
            //phpcs:ignore WordPress.Security.NonceVerification.Missing
            return sanitize_text_field(wp_unslash($_POST['billing_country']));
            //phpcs:ignore WordPress.Security.NonceVerification.Missing
        }
        $shipping_country = (string) get_user_meta($user_id, 'shipping_country', \true);
        if (!empty($shipping_country)) {
            return $shipping_country;
        }
        return (string) get_user_meta($user_id, 'billing_country', \true);
    }
    /**
     * Saves VAT fields from My Account address edit.
     *
     * @param int    $user_id
     * @param string $load_address
     */
    public function save_my_account_vat($user_id, $load_address = ''): void
    {
        $data = wp_unslash($_POST);
        //phpcs:ignore WordPress.Security.NonceVerification.Missing
        $wants_invoice = \true;
        if (ConfigInterface::BEHAVIOR_ASK_USER === $this->config->get_field_behavior()) {
            if ('billing' === $load_address) {
                $want_key = $this->config->get_want_invoice_user_key();
                $wants_invoice = filter_var($data[$want_key] ?? \false, \FILTER_VALIDATE_BOOLEAN);
            } elseif ('shipping' === $load_address) {
                $want_key_user = $this->config->get_want_invoice_user_key();
                $wants_invoice = filter_var(get_user_meta($user_id, $want_key_user, \true), \FILTER_VALIDATE_BOOLEAN);
            }
        }
        $billing_nip = $this->config->get_billing_tax_id_user_key();
        $shipping_nip = $this->config->get_shipping_tax_id_user_key();
        foreach ($this->config->get_checkout_fields() as $field) {
            if ($field->is_enabled() && $field->get_section() === $load_address) {
                $key = $field->get_user_meta_key();
                // The form field ID is used as the input name in My Account Address,
                // but we save to user meta using the meta key.
                $raw = $data[$field->get_checkout_key()] ?? '';
                if (!$wants_invoice && ($key === $billing_nip || $key === $shipping_nip)) {
                    $raw = '';
                }
                update_user_meta($user_id, $key, $field->sanitize_for_storage($raw));
            }
        }
        if ('billing' === $load_address && !$wants_invoice) {
            update_user_meta($user_id, $shipping_nip, '');
        }
    }
}
