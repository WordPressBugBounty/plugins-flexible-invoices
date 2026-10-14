<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\User;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
/**
 * Handles filling customer VAT details during WP-Admin order creation AJAX calls.
 */
class CustomerAdminAjax
{
    private ConfigInterface $config;
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }
    public function hooks(): void
    {
        add_filter('woocommerce_ajax_get_customer_details', [$this, 'fill_ajax_customer_details'], 10, 3);
    }
    /**
     * Fills VAT details in the AJAX response when selecting a customer in wp-admin order creation.
     *
     * @param array $data
     * @param mixed $customer
     * @param int   $user_id
     * @return array
     */
    public function fill_ajax_customer_details($data, $customer, $user_id): array
    {
        $user_id = (int) $user_id;
        if (!$user_id) {
            return $data;
        }
        foreach ($this->config->get_checkout_fields() as $field) {
            if (!$field->is_enabled()) {
                continue;
            }
            $section = $field->get_section();
            if (!isset($data[$section]) || !is_array($data[$section])) {
                $data[$section] = [];
            }
            $data[$section][$field->get_id()] = get_user_meta($user_id, $field->get_user_meta_key(), \true);
        }
        return $data;
    }
}
