<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Admin;

use WC_Order;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout\Field\AbstractCheckoutField;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
/**
 * Handles fields in the wp-admin Order Edit screen.
 */
class OrderEdit
{
    private ConfigInterface $config;
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }
    /**
     * Hooks into WordPress.
     */
    public function hooks(): void
    {
        add_filter('woocommerce_admin_billing_fields', [$this, 'add_admin_billing_fields'], 10, 2);
        add_filter('woocommerce_admin_shipping_fields', [$this, 'add_admin_shipping_fields'], 10, 2);
        add_action('woocommerce_process_shop_order_meta', [$this, 'save_admin_order_meta'], 10, 1);
    }
    /**
     * Adds billing checkout fields to the admin order billing edit form.
     *
     * @param array          $fields
     * @param WC_Order|false $order
     */
    public function add_admin_billing_fields($fields, $order = null): array
    {
        $order = $this->resolve_order($order);
        return $this->inject_admin_fields($fields, 'billing', $order);
    }
    /**
     * Adds shipping checkout fields to the admin order shipping edit form.
     *
     * @param array          $fields
     * @param WC_Order|false $order
     */
    public function add_admin_shipping_fields($fields, $order = null): array
    {
        $order = $this->resolve_order($order);
        return $this->inject_admin_fields($fields, 'shipping', $order);
    }
    /**
     * @param mixed $order
     */
    private function resolve_order($order): WC_Order
    {
        if ($order instanceof WC_Order) {
            return $order;
        }
        global $theorder;
        return $theorder instanceof WC_Order ? $theorder : new WC_Order();
    }
    /**
     * Injects custom checkout fields for a given section into admin order address fields.
     */
    private function inject_admin_fields(array $fields, string $section, WC_Order $order): array
    {
        $section_fields = $this->get_fields_for_section($section);
        $new_fields = [];
        $inserted = \false;
        foreach ($fields as $key => $field) {
            $new_fields[$key] = $field;
            if ('company' === $key) {
                foreach ($section_fields as $checkout_field) {
                    $new_fields[$this->admin_field_key($checkout_field)] = $checkout_field->get_admin_order_args($order);
                }
                $inserted = \true;
            }
        }
        if (!$inserted) {
            foreach ($section_fields as $checkout_field) {
                $new_fields[$this->admin_field_key($checkout_field)] = $checkout_field->get_admin_order_args($order);
            }
        }
        return $new_fields;
    }
    /**
     * Saves order meta from the admin edit screen.
     *
     * @param int|string $order_id
     */
    public function save_admin_order_meta($order_id): void
    {
        $order = wc_get_order($order_id);
        if (!$order instanceof WC_Order) {
            return;
        }
        $all_fields = $this->config->get_checkout_fields();
        $wants_invoice = $this->determine_wants_invoice($all_fields);
        $billing_nip = $this->config->get_billing_tax_id_key();
        $shipping_nip = $this->config->get_shipping_tax_id_key();
        foreach ($all_fields as $field) {
            if (!$field->is_enabled()) {
                continue;
            }
            $id = $field->get_id();
            $section = $field->get_section();
            // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $raw_value = wp_unslash($_POST['_' . $section . '_' . $id] ?? $_POST[$id] ?? '');
            if (!$wants_invoice && ($id === $billing_nip || $id === $shipping_nip)) {
                $raw_value = '';
            }
            $value = $field->sanitize_for_storage($raw_value);
            $order->update_meta_data($id, $value);
        }
        $order->save();
    }
    /**
     * Determines if the invoice consent is active based on config or submitted data.
     *
     * @param AbstractCheckoutField[] $all_fields
     */
    private function determine_wants_invoice(array $all_fields): bool
    {
        if (ConfigInterface::BEHAVIOR_ALWAYS_SHOW === $this->config->get_field_behavior()) {
            return \true;
        }
        $want_key = $this->config->get_want_invoice_key();
        foreach ($all_fields as $field) {
            if ($field->get_id() === $want_key && $field->is_enabled()) {
                $id = $field->get_id();
                $section = $field->get_section();
                // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                $raw_value = wp_unslash($_POST['_' . $section . '_' . $id] ?? $_POST[$id] ?? '');
                return filter_var($field->sanitize_for_storage($raw_value), \FILTER_VALIDATE_BOOLEAN);
            }
        }
        return \false;
    }
    /**
     * WooCommerce prefixes admin address field keys with _billing_ / _shipping_.
     * Use the suffix so the input id matches the order meta key.
     */
    private function admin_field_key(AbstractCheckoutField $field): string
    {
        $id = $field->get_id();
        $prefix = '_' . $field->get_section() . '_';
        if (strpos($id, $prefix) === 0) {
            return substr($id, strlen($prefix));
        }
        return $id;
    }
    /**
     * Gets enabled checkout fields for a given section.
     *
     * @return AbstractCheckoutField[]
     */
    private function get_fields_for_section(string $section): array
    {
        $fields = [];
        foreach ($this->config->get_checkout_fields() as $field) {
            if ($field->is_enabled() && $field->get_section() === $section) {
                $fields[] = $field;
            }
        }
        return $fields;
    }
}
