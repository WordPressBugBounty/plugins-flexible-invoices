<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce\Order;

use WC_Order;
use WP_Post;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Integration\DocumentFactory;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce\Links;
use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDeskFIVendor\WPDesk\View\Renderer\Renderer;
/**
 * Adds a meta box in the order with buttons for generating and displaying the created documents.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\WooCommerce
 */
class RegisterMetaBox implements Hookable
{
    /**
     * @var DocumentFactory
     */
    private $document_factory;
    /**
     * @param DocumentFactory $document_factory
     */
    public function __construct(DocumentFactory $document_factory)
    {
        $this->document_factory = $document_factory;
    }
    /**
     * Fires hooks
     */
    public function hooks()
    {
        add_action('add_meta_boxes', [$this, 'add_meta_box'], 10);
    }
    /**
     * Add meta box for order.
     *
     * @internal You should not use this directly from another application
     */
    public function add_meta_box()
    {
        $screens = ['shop_order', 'woocommerce_page_wc-orders'];
        add_meta_box('flexible-invoices', esc_html__('Invoice', 'flexible-invoices'), [$this, 'order_meta_box_view'], $screens, 'side', 'core');
    }
    /**
     * @param object $post
     *
     * @return void
     * @internal You should not use this directly from another application
     */
    public function order_meta_box_view($post_or_order_object)
    {
        $order = $post_or_order_object instanceof WP_Post ? wc_get_order($post_or_order_object->ID) : $post_or_order_object;
        foreach ($this->document_factory->get_creators() as $creator) {
            $meta_type = '_' . $creator->get_type() . '_generated';
            $document_id = (int) $order->get_meta($meta_type);
            $creator->set_order_id($order->get_id());
            $should_skip = (bool) apply_filters('fi/core/order/generate/document/skip/' . $creator->get_type(), \false, $creator, $order);
            if ($should_skip) {
                continue;
            }
            if (!$document_id) {
                if (!$creator->is_allowed_for_create()) {
                    continue;
                }
                echo Links::generate_link($order->get_id(), $creator->get_type(), $creator->get_button_label(), $order->get_status() !== 'refunded');
            } else {
                $document_meta_ids = $this->get_unique_meta_ids($order->get_meta($meta_type, \false));
                foreach ($document_meta_ids as $document_meta_id) {
                    $creator->set_order_id($order->get_id());
                    $document = $this->document_factory->get_document_creator($document_meta_id)->get_document();
                    echo Links::view_link($document, !$creator->is_allowed_for_edit());
                    echo Links::download_email_links($document);
                }
            }
        }
    }
    public function get_unique_meta_ids($document_meta_ids): array
    {
        $ids = [];
        foreach ($document_meta_ids as $document_meta_id) {
            $document_id = $document_meta_id->get_data();
            $ids[$document_id['value']] = $document_id['value'];
        }
        return $ids;
    }
}
