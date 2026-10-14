<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Service\OrderInvoiceDataSaver;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Service\ViesOrderMetaWriter;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\InvoiceData;
use WC_Order;
/**
 * Handles saving custom checkout fields into order meta.
 */
class CheckoutSave
{
    private ConfigInterface $config;
    private OrderInvoiceDataSaver $order_saver;
    private ViesOrderMetaWriter $vies_meta_writer;
    public function __construct(ConfigInterface $config, OrderInvoiceDataSaver $order_saver, ViesOrderMetaWriter $vies_meta_writer)
    {
        $this->config = $config;
        $this->order_saver = $order_saver;
        $this->vies_meta_writer = $vies_meta_writer;
    }
    /**
     * Hooks into WordPress.
     */
    public function hooks(): void
    {
        add_action('woocommerce_checkout_create_order', [$this, 'save_checkout_fields'], 10, 2);
    }
    /**
     * Saves checkout fields to order meta before the order is stored.
     *
     * @param WC_Order $order Order being created.
     * @param array    $data  Posted checkout data.
     */
    public function save_checkout_fields($order, $data): void
    {
        // Use POST if data is empty for some reason, standard WC behavior fallback.
        if (empty($data) && !empty($_POST)) {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $data = $_POST;
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
        }
        $checkout_data = SanitizedCheckoutData::from_woocommerce_checkout((array) $data, $this->config);
        $invoice_data = InvoiceData::from_sanitized_checkout_data($checkout_data);
        $this->order_saver->save_to_order($order, $invoice_data);
        $this->vies_meta_writer->write($order, $checkout_data, $checkout_data->get_billing_country());
        $built_in_keys = [$this->config->get_want_invoice_key(), $this->config->get_billing_tax_id_key(), $this->config->get_shipping_tax_id_key()];
        foreach ($this->config->get_checkout_fields() as $field) {
            if (!$field->is_enabled() || in_array($field->get_id(), $built_in_keys, \true)) {
                continue;
            }
            $id = $field->get_id();
            $checkout_key = $field->get_checkout_key();
            // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $raw_value = $data[$checkout_key] ?? (isset($_POST[$checkout_key]) ? wp_unslash($_POST[$checkout_key]) : '');
            $order->update_meta_data($id, $field->sanitize_for_storage($raw_value));
        }
    }
}
