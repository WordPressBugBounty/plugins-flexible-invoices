<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Admin;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\View\SimpleTemplateRenderer;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Service\WooCommerceHelper;
use WC_Order;
use WP_Post;
/**
 * Shows VIES / OSS validation details on the order edit screen.
 */
class ViesOrderMetaBox
{
    public const META_BOX_ID = 'wpd_invoices_checkout_eu_vat';
    private ConfigInterface $config;
    private WooCommerceHelper $woocommerce_helper;
    private SimpleTemplateRenderer $renderer;
    public function __construct(ConfigInterface $config, WooCommerceHelper $woocommerce_helper, SimpleTemplateRenderer $renderer)
    {
        $this->config = $config;
        $this->woocommerce_helper = $woocommerce_helper;
        $this->renderer = $renderer;
    }
    public function hooks(): void
    {
        add_action('add_meta_boxes', [$this, 'add_meta_box'], 30, 2);
    }
    /**
     * @param mixed $screen_id Screen id.
     * @param mixed $order     Order or post.
     */
    public function add_meta_box($screen_id, $order): void
    {
        if (!$this->config->should_show_vies_order_metabox()) {
            return;
        }
        $order = $this->get_order($order);
        if (!$order instanceof WC_Order) {
            return;
        }
        if (!$this->woocommerce_helper->is_eu_country((string) $order->get_billing_country())) {
            return;
        }
        add_meta_box(self::META_BOX_ID, __('EU VAT', 'flexible-invoices'), [$this, 'render'], ['shop_order', 'shop_subscription', 'woocommerce_page_wc-orders'], 'side');
    }
    /**
     * @param mixed $post_or_order_object Order or post.
     */
    public function render($post_or_order_object): void
    {
        $order = $this->get_order($post_or_order_object);
        if (!$order instanceof WC_Order) {
            return;
        }
        $vat_number = (string) $order->get_meta($this->config->get_billing_tax_id_key());
        $evidence_key = $this->config->get_vat_number_evidence_key();
        if ('' === $vat_number && '' !== $evidence_key) {
            $vat_number = (string) $order->get_meta($evidence_key);
        }
        $self_declared_vat = filter_var($order->get_meta($this->config->get_vat_confirmation_key()), \FILTER_VALIDATE_BOOLEAN) || filter_var($order->get_meta($this->config->get_vat_number_self_declared_key()), \FILTER_VALIDATE_BOOLEAN);
        $location_declared = filter_var($order->get_meta($this->config->get_location_confirmation_key()), \FILTER_VALIDATE_BOOLEAN) || filter_var($order->get_meta($this->config->get_customer_self_declared_country_key()), \FILTER_VALIDATE_BOOLEAN);
        $this->renderer->render_vies_order_metabox($vat_number, $self_declared_vat, filter_var($order->get_meta($this->config->get_vat_number_is_valid_key()), \FILTER_VALIDATE_BOOLEAN), filter_var($order->get_meta($this->config->get_vat_number_is_validated_key()), \FILTER_VALIDATE_BOOLEAN), (string) $order->get_billing_country(), (string) $order->get_customer_ip_address(), (string) $order->get_meta($this->config->get_ip_country_key()), $location_declared, WC()->countries->get_countries());
    }
    /**
     * @param mixed $object Order or post.
     */
    private function get_order($object): ?WC_Order
    {
        if ($object instanceof WC_Order) {
            return $object;
        }
        if ($object instanceof WP_Post) {
            $order = wc_get_order($object->ID);
            if ($order instanceof WC_Order) {
                return $order;
            }
        }
        if (is_numeric($object)) {
            $order = wc_get_order((int) $object);
            if ($order instanceof WC_Order) {
                return $order;
            }
        }
        return null;
    }
}
