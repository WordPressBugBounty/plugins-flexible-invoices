<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\User;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\InvoicesCheckout;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Service\UserInvoiceDataSaver;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\InvoiceData;
use WC_Order;
/**
 * Handles synchronizing order VAT and invoice data to customer user meta.
 */
class UserMetaSync
{
    private ConfigInterface $config;
    private UserInvoiceDataSaver $user_saver;
    public function __construct(ConfigInterface $config, UserInvoiceDataSaver $user_saver)
    {
        $this->config = $config;
        $this->user_saver = $user_saver;
    }
    public function hooks(): void
    {
        add_action('woocommerce_checkout_update_user_meta', [$this, 'save_checkout_vat_to_user'], 10, 2);
        add_action('woocommerce_store_api_checkout_update_order_from_request', [$this, 'sync_order_to_user_meta'], 20, 1);
        add_action('woocommerce_store_api_checkout_order_processed', [$this, 'sync_order_to_user_meta'], 10, 1);
        add_action('woocommerce_checkout_order_processed', [$this, 'sync_order_to_user_meta'], 10, 1);
        add_action('woocommerce_created_customer', [$this, 'save_created_customer_invoice_data'], 10, 1);
    }
    /**
     * Saves VAT fields to user meta during classic checkout.
     *
     * Note: In classic WooCommerce shortcode checkout, $_POST['ship_to_different_address']
     * is submitted only when the customer explicitly checks "Ship to a different address?".
     * If omitted/empty, shipping address falls back to billing address, so shipping VAT
     * is cleared to prevent storing obsolete separate shipping VAT.
     *
     * @param int   $user_id
     * @param array $data
     */
    public function save_checkout_vat_to_user($user_id, $data): void
    {
        if (!$user_id) {
            return;
        }
        $wants_invoice = \true;
        if (ConfigInterface::BEHAVIOR_ASK_USER === $this->config->get_field_behavior()) {
            $want_key = $this->config->get_want_invoice_user_key();
            $wants_invoice = filter_var($data[$want_key] ?? \false, \FILTER_VALIDATE_BOOLEAN);
        }
        $billing_nip = $this->config->get_billing_tax_id_key();
        $shipping_nip = $this->config->get_shipping_tax_id_key();
        foreach ($this->config->get_checkout_fields() as $field) {
            if (!$field->is_enabled()) {
                continue;
            }
            if (empty($_POST['ship_to_different_address']) && 'shipping' === $field->get_section()) {
                // phpcs:ignore WordPress.Security.NonceVerification.Missing
                update_user_meta($user_id, $field->get_user_meta_key(), '');
                continue;
            }
            $id = $field->get_id();
            $raw = $data[$field->get_checkout_key()] ?? '';
            if (!$wants_invoice && ($id === $billing_nip || $id === $shipping_nip)) {
                $raw = '';
            }
            $value = $field->sanitize_for_storage($raw);
            update_user_meta($user_id, $field->get_user_meta_key(), $value);
        }
    }
    /**
     * Synchronizes order invoice data to the customer's user meta after checkout.
     *
     * @param WC_Order|int|string $order_or_id Order instance or order ID.
     */
    public function sync_order_to_user_meta($order_or_id): void
    {
        $order = $order_or_id instanceof WC_Order ? $order_or_id : wc_get_order($order_or_id);
        if ($order instanceof WC_Order) {
            $user_id = $order->get_customer_id();
            if (!$user_id) {
                return;
            }
            $wants_invoice = \true;
            if (ConfigInterface::BEHAVIOR_ASK_USER === $this->config->get_field_behavior()) {
                $want_key = $this->config->get_want_invoice_key();
                $wants_invoice = filter_var($order->get_meta($want_key), \FILTER_VALIDATE_BOOLEAN);
            }
            $billing_nip = $this->config->get_billing_tax_id_key();
            $shipping_nip = $this->config->get_shipping_tax_id_key();
            foreach ($this->config->get_checkout_fields() as $field) {
                if (!$field->is_enabled()) {
                    continue;
                }
                $id = $field->get_id();
                if ($order->meta_exists($id)) {
                    $value = $order->get_meta($id);
                    if (!$wants_invoice && ($id === $billing_nip || $id === $shipping_nip)) {
                        $value = '';
                    }
                    update_user_meta($user_id, $field->get_user_meta_key(), $field->sanitize_for_storage($value));
                } elseif (!$wants_invoice && ($id === $billing_nip || $id === $shipping_nip)) {
                    update_user_meta($user_id, $field->get_user_meta_key(), '');
                }
            }
        }
    }
    /**
     * Persists invoice preference when a customer account is created during checkout.
     *
     * Block Checkout creates the account after the Store API order-update hook, so user meta
     * must also be written here from posted data or the checkout session.
     *
     * @param int $customer_id Newly created customer user ID.
     */
    public function save_created_customer_invoice_data($customer_id): void
    {
        $customer_id = (int) $customer_id;
        if ($customer_id <= 0) {
            return;
        }
        $data = [];
        if (!empty($_POST)) {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $data = wp_unslash($_POST);
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
        }
        $invoice_data = InvoiceData::from_array($data, $this->config);
        $session = WC()->session;
        if (!$invoice_data->has_want_invoice() && $session) {
            $session_data = $session->get(InvoicesCheckout::SESSION_KEY_BLOCKS_DATA);
            if (is_array($session_data)) {
                $invoice_data = InvoiceData::from_array($session_data, $this->config);
            }
        }
        if (!$invoice_data->has_want_invoice() && !$invoice_data->has_billing_tax_id()) {
            return;
        }
        $this->user_saver->save_to_user($customer_id, $invoice_data);
    }
}
