<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce\Order;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Integration\DocumentFactory;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\RegisterPostType;
use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Deleting the relationship between a document and an order.
 *
 * When a document is deleted through the WordPress panel,
 * the relation is deleted and the document can be reissued for the order.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\WooCommerce
 */
class DeleteDocumentRelation implements \WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var DocumentFactory
     */
    private $document_factory;
    /**
     * @param DocumentFactory $document_factory
     */
    public function __construct(\WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Integration\DocumentFactory $document_factory)
    {
        $this->document_factory = $document_factory;
    }
    /**
     * Fires hooks
     */
    public function hooks()
    {
        \add_action('before_delete_post', [$this, 'remove_order_relation']);
    }
    /**
     * When document are deleting remove relation for order.
     *
     * @param $id
     *
     * @internal You should not use this directly from another application
     */
    public function remove_order_relation($id)
    {
        global $post_type;
        $document = $this->document_factory->get_document_creator($id);
        $type = '_' . $document->get_type();
        if (\WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\RegisterPostType::POST_TYPE_NAME === $post_type) {
            $is_correction = (int) \get_post_meta($id, $type, \true) === 1;
            $order_id = \get_post_meta($id, '_wc_order_id', \true);
            $order = \wc_get_order($order_id);
            if (!$order) {
                return;
            }
            if ($is_correction) {
                $order->delete_meta_data($type . '_corrections');
            }
            $order->delete_meta_data($type . '_generated');
            $order->save();
        }
    }
}
