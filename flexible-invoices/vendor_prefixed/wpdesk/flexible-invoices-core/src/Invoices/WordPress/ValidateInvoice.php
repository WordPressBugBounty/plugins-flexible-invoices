<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress;

use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Show a warning on the invoice edit screen when the number is already used.
 */
class ValidateInvoice implements Hookable
{
    public function hooks()
    {
        add_action('edit_form_before_permalink', [$this, 'validate_invoice']);
    }
    /**
     * @param \WP_Post $post Post object.
     */
    public function validate_invoice($post)
    {
        if (!$post instanceof \WP_Post || RegisterPostType::POST_TYPE_NAME !== $post->post_type) {
            return;
        }
        $post_title = $post->post_title;
        if ('' === $post_title) {
            return;
        }
        global $wpdb;
        $post_id = (int) $post->ID;
        $invoice_number = $wpdb->get_var(
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $wpdb->prepare("SELECT post_title FROM {$wpdb->posts} WHERE post_title = %s AND post_type = %s AND post_status = 'publish' AND ID != %d LIMIT 1", $post_title, RegisterPostType::POST_TYPE_NAME, $post_id)
        );
        if ($invoice_number) {
            // translators: %s invoice number.
            echo '<span style="color: red;">' . sprintf(esc_html__('The invoice with the number %s already exists!', 'flexible-invoices'), esc_html($invoice_number)) . '</span>';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
}
