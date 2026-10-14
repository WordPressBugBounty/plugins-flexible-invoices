<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Service;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\InvoiceData;
use WC_Order;
/**
 * Service responsible solely for persisting invoice data to WC_Order meta data.
 */
class OrderInvoiceDataSaver
{
    private ConfigInterface $config;
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }
    /**
     * Saves InvoiceData DTO to WC_Order meta data.
     */
    public function save_to_order(WC_Order $order, InvoiceData $invoice_data): void
    {
        $want_invoice_key = $this->config->get_want_invoice_key();
        $billing_tax_id_key = $this->config->get_billing_tax_id_key();
        $shipping_tax_id_key = $this->config->get_shipping_tax_id_key();
        if ($invoice_data->has_want_invoice()) {
            $order->update_meta_data($want_invoice_key, $invoice_data->get_want_invoice() ? 'yes' : 'no');
        }
        if ($invoice_data->has_billing_tax_id()) {
            $order->update_meta_data($billing_tax_id_key, (string) $invoice_data->get_billing_tax_id());
        }
        if ($invoice_data->has_shipping_tax_id()) {
            $order->update_meta_data($shipping_tax_id_key, (string) $invoice_data->get_shipping_tax_id());
        }
        if ($this->config->should_show_company_field()) {
            if ($invoice_data->has_billing_company()) {
                $this->save_company_to_order($order, 'billing', (string) $invoice_data->get_billing_company());
            }
            if ($invoice_data->has_shipping_company()) {
                $this->save_company_to_order($order, 'shipping', (string) $invoice_data->get_shipping_company());
            }
        }
        if ($invoice_data->has_location_confirmation() && $invoice_data->get_location_confirmation()) {
            $order->update_meta_data($this->config->get_location_confirmation_key(), 'yes');
        }
    }
    /**
     * Core address company must be stored via setters so HPOS/order datastore updates the edit screen.
     */
    private function save_company_to_order(WC_Order $order, string $which, string $company): void
    {
        if ('billing' === $which) {
            if ('_billing_company' === $this->config->get_billing_company_key()) {
                $order->set_billing_company($company);
                return;
            }
            $order->update_meta_data($this->config->get_billing_company_key(), $company);
            return;
        }
        if ('_shipping_company' === $this->config->get_shipping_company_key()) {
            $order->set_shipping_company($company);
            return;
        }
        $order->update_meta_data($this->config->get_shipping_company_key(), $company);
    }
    /**
     * Saves InvoiceData DTO to WC_Order meta data and saves order.
     */
    public function save_to_order_and_apply(WC_Order $order, InvoiceData $invoice_data): void
    {
        $this->save_to_order($order, $invoice_data);
        $order->save();
    }
}
