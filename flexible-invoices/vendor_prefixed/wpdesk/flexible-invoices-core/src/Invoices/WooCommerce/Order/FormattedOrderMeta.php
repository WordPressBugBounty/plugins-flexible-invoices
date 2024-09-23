<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce\Order;

use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Retrieves the user's VAT number from the meta field and assigns it to the order when manually creating an order.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\WooCommerce
 */
class FormattedOrderMeta implements \WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * Fires hooks
     */
    public function hooks()
    {
        \add_filter('woocommerce_ajax_get_customer_details', [$this, 'get_customer_details'], 10, 2);
    }
    /**
     * @param              $data
     * @param \WC_Customer $customer
     *
     * @return array
     */
    public function get_customer_details($data, \WC_Customer $customer)
    {
        $data['billing']['vat_number'] = $customer->get_meta('vat_number') ?? $customer->get_meta('billing_vat_number');
        return $data;
    }
}
