<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce\Order;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce\Checkout\InvoicesCheckoutConfig;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce\Checkout\LegacyUserMeta;
use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Retrieves the user's VAT number from the meta field and assigns it to the order when manually creating an order.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\WooCommerce
 */
class FormattedOrderMeta implements Hookable
{
    private LegacyUserMeta $legacy_user_meta;
    public function __construct(LegacyUserMeta $legacy_user_meta)
    {
        $this->legacy_user_meta = $legacy_user_meta;
    }
    /**
     * Fires hooks
     */
    public function hooks()
    {
        add_filter('woocommerce_ajax_get_customer_details', [$this, 'get_customer_details'], 10, 2);
    }
    /**
     * Fills the order screen VAT inputs when a customer is loaded.
     *
     * WooCommerce writes `#_billing_{key}` and `#_shipping_{key}` from this response.
     * The admin field keys are `vat_number` and `invoice_ask`, so the values have to sit
     * under those keys. invoices-checkout stores the same data under the order meta keys
     * `_billing_vat_number` and `_billing_invoice_ask`, which do not match those inputs.
     *
     * Checkout now saves `billing_vat_number` and `billing_invoice_ask` and no longer writes
     * `vat_number` or `invoice_ask`. Those old keys are read only when the new key has
     * never been saved. A saved empty VAT number stays empty.
     *
     * @param mixed $data     Customer details passed to the order screen.
     * @param mixed $customer Customer being loaded.
     *
     * @return mixed
     */
    public function get_customer_details($data, $customer)
    {
        if (!is_array($data)) {
            return $data;
        }
        if (!isset($data['billing']) || !is_array($data['billing'])) {
            $data['billing'] = [];
        }
        if (!isset($data['shipping']) || !is_array($data['shipping'])) {
            $data['shipping'] = [];
        }
        if (!$customer instanceof \WC_Customer) {
            $data['billing']['vat_number'] = '';
            $data['billing']['invoice_ask'] = '0';
            $data['shipping']['vat_number'] = '';
            return $data;
        }
        $data['billing']['vat_number'] = $this->customer_vat_number($customer);
        $data['billing']['invoice_ask'] = $this->customer_wants_invoice($customer) ? '1' : '0';
        $data['shipping']['vat_number'] = $this->customer_meta_string($customer, InvoicesCheckoutConfig::SHIPPING_TAX_ID_USER_KEY);
        return $data;
    }
    private function customer_vat_number(\WC_Customer $customer): string
    {
        return $this->legacy_user_meta->read($customer->get_id(), InvoicesCheckoutConfig::BILLING_TAX_ID_USER_KEY);
    }
    private function customer_wants_invoice(\WC_Customer $customer): bool
    {
        $invoice_ask = $this->legacy_user_meta->read($customer->get_id(), InvoicesCheckoutConfig::WANT_INVOICE_USER_KEY);
        return filter_var($invoice_ask, \FILTER_VALIDATE_BOOLEAN);
    }
    private function customer_meta_string(\WC_Customer $customer, string $meta_key): string
    {
        $value = $customer->get_meta($meta_key);
        return is_string($value) ? $value : '';
    }
}
