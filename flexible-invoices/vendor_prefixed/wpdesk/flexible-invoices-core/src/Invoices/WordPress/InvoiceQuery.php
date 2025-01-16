<?php

/**
 * Get document from wp tables
 *
 * @package WPDesk\Library\FlexibleInvoicesCore
 */
namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress;

/**
 * @package WPDesk\Library\FlexibleInvoicesCore\WordPress
 */
class InvoiceQuery
{
    /**
     * @param $type
     *
     * @return \WP_Query
     */
    public function get_documents_by_type($type): \WP_Query
    {
        $args = ['post_type' => 'inspire_invoice', 'meta_key' => '_type', 'meta_value' => $type, 'posts_per_page' => 50];
        return new \WP_Query($args);
    }
    /**
     * @param int $id
     *
     * @return array|\WP_Post|null
     */
    public function get_document_by_id(int $id)
    {
        if (!$id) {
            return [];
        }
        return get_post($id);
    }
}
