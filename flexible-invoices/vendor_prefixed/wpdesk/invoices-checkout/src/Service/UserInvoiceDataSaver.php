<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Service;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\InvoicesCheckout;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\InvoiceData;
use WC_Order;
/**
 * Service responsible solely for persisting invoice data to user meta and syncing order meta to user.
 */
class UserInvoiceDataSaver
{
    private ConfigInterface $config;
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }
    /**
     * Saves InvoiceData DTO directly to user meta.
     */
    public function save_to_user(int $user_id, InvoiceData $invoice_data): void
    {
        if (!$user_id) {
            return;
        }
        $want_invoice_user_key = $this->config->get_want_invoice_user_key();
        $billing_user_key = $this->config->get_billing_tax_id_user_key();
        $shipping_user_key = $this->config->get_shipping_tax_id_user_key();
        $billing_company_user_key = $this->config->get_billing_company_user_key();
        $shipping_company_user_key = $this->config->get_shipping_company_user_key();
        if ($invoice_data->has_want_invoice()) {
            update_user_meta($user_id, $want_invoice_user_key, $invoice_data->get_want_invoice() ? '1' : '0');
        }
        if ($invoice_data->has_billing_tax_id()) {
            update_user_meta($user_id, $billing_user_key, sanitize_text_field(wp_unslash((string) $invoice_data->get_billing_tax_id())));
        }
        if ($invoice_data->has_shipping_tax_id()) {
            update_user_meta($user_id, $shipping_user_key, sanitize_text_field(wp_unslash((string) $invoice_data->get_shipping_tax_id())));
        }
        if ($this->config->should_show_company_field()) {
            if ($invoice_data->has_billing_company()) {
                update_user_meta($user_id, $billing_company_user_key, sanitize_text_field(wp_unslash((string) $invoice_data->get_billing_company())));
            }
            if ($invoice_data->has_shipping_company()) {
                update_user_meta($user_id, $shipping_company_user_key, sanitize_text_field(wp_unslash((string) $invoice_data->get_shipping_company())));
            }
        }
    }
    /**
     * Syncs saved order data back to user profile.
     */
    public function sync_order_to_user(WC_Order $order): void
    {
        $user_id = $order->get_customer_id();
        if (!$user_id) {
            return;
        }
        $want_key = $this->config->get_want_invoice_key();
        $billing_tax_key = $this->config->get_billing_tax_id_key();
        $shipping_tax_key = $this->config->get_shipping_tax_id_key();
        $data = [];
        if ($order->meta_exists($want_key)) {
            $data[InvoicesCheckout::KEY_WANT_INVOICE] = $order->get_meta($want_key);
        }
        if ($order->meta_exists($billing_tax_key)) {
            $data[InvoicesCheckout::KEY_BILLING_TAX_ID] = $order->get_meta($billing_tax_key);
        }
        if (!isset($data[InvoicesCheckout::KEY_WANT_INVOICE]) && !empty($data[InvoicesCheckout::KEY_BILLING_TAX_ID])) {
            $data[InvoicesCheckout::KEY_WANT_INVOICE] = 'yes';
        }
        if ($order->meta_exists($shipping_tax_key)) {
            $data[InvoicesCheckout::KEY_SHIPPING_TAX_ID] = $order->get_meta($shipping_tax_key);
        }
        if ($this->config->should_show_company_field()) {
            $billing_company_key = $this->config->get_billing_company_key();
            $shipping_company_key = $this->config->get_shipping_company_key();
            if ($order->meta_exists($billing_company_key)) {
                $data[InvoicesCheckout::KEY_BILLING_COMPANY] = $order->get_meta($billing_company_key);
            }
            if ($order->meta_exists($shipping_company_key)) {
                $data[InvoicesCheckout::KEY_SHIPPING_COMPANY] = $order->get_meta($shipping_company_key);
            }
        }
        $invoice_data = InvoiceData::from_array($data, $this->config);
        $this->save_to_user($user_id, $invoice_data);
    }
    /**
     * Public API helper to check if user wants an invoice.
     */
    public function get_user_want_invoice(int $user_id): bool
    {
        $val = get_user_meta($user_id, $this->config->get_want_invoice_user_key(), \true);
        return filter_var($val, \FILTER_VALIDATE_BOOLEAN);
    }
}
