<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\Documents\Document;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\EmailStatus;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\Invoice;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\WooCommerce;
/**
 * Define order actions on Woocommerce Order List.
 */
class Links
{
    /**
     * @param Document $document
     *
     * @return string
     */
    public static function view_link(Document $document, $without_url = \false): string
    {
        $url = wp_nonce_url(admin_url('post.php?post=' . $document->get_id() . '&action=edit'));
        if ($without_url) {
            return '<p>' . esc_html($document->get_formatted_number()) . '</p>';
        }
        return '<p><a class="view-document" href="' . esc_url($url) . '" title="' . esc_attr($document->get_formatted_number()) . '">' . esc_html($document->get_formatted_number()) . '</a></p>';
    }
    /**
     * @param int    $order_id
     * @param string $type
     * @param string $label
     *
     * @return string
     */
    public static function generate_link(int $order_id, string $type, string $label, $is_active = \true): string
    {
        $class_name = $tooltip = '';
        $url = wp_nonce_url(admin_url('admin-ajax.php?action=fi_generate_document&issue_type=action&type=' . $type . '&order_id=' . $order_id));
        if (empty($label)) {
            $label = esc_html__('Issue Invoice', 'flexible-invoices');
            $tooltip = $label;
        }
        if (!$is_active) {
            $url = '#';
            $class_name = 'button-disabled';
            $tooltip = esc_attr__('This order is refunded. Change the order status before you invoice', 'flexible-invoices');
        }
        return '<p><a class="button generate-document generate-' . $type . ' ' . $class_name . '" href="' . esc_url($url) . '" title="' . esc_attr($tooltip) . '">' . esc_html($label) . '</a></p>';
    }
    /**
     * @param Document $document
     *
     * @return string
     */
    public static function download_link(Document $document): string
    {
        $document_id = $document->get_id();
        $download_url = wp_nonce_url(admin_url('admin-ajax.php?action=fi_download_pdf&hash=' . Invoice::document_hash($document) . '&id=' . $document_id . '&save_file=1'));
        return '<p><a class="button get-document" href="' . esc_url($download_url) . '">' . esc_html__('Download', 'flexible-invoices') . '</a></p>';
    }
    /**
     * @param Document $document
     *
     * @return string
     */
    public static function email_link(Document $document): string
    {
        if (WooCommerce::is_active()) {
            $email_url = wp_nonce_url(admin_url('admin-ajax.php?action=fi_send_email&document_id=' . $document->get_id()));
            $email_status = EmailStatus::get($document);
            $data_attr = ' data-status="' . esc_attr($email_status) . '"';
            return '<p><a ' . $data_attr . ' class="button send_document ' . self::email_status_class($email_status) . '" href="' . esc_url($email_url) . '" title="' . self::get_email_tooltip_attr($email_status) . '" >' . esc_html__('Send email', 'flexible-invoices') . '</a></p>';
        }
        return '';
    }
    /**
     * @param Document $document
     *
     * @return string
     */
    public static function create_correction_link(Document $document): string
    {
        if ($document->get_type() === 'invoice') {
            $correction_id = 0;
            $class_name = $correction_id ? 'document-generated' : 'document-not-generated';
            $title = $correction_id ? esc_html__('Show Correction', 'flexible-invoices') : esc_html__('Create Correction', 'flexible-invoices');
            if (!$correction_id) {
                $url = wp_nonce_url(admin_url('post-new.php?post_type=inspire_invoice&document_type=correction&corrected_invoice_id=' . $document->get_id()));
            } else {
                $url = wp_nonce_url(admin_url('post.php?post=' . $correction_id . '&action=edit'));
            }
            return '<p><a title="' . $title . '" class="button create-correction ' . $class_name . '" href="' . esc_url($url) . '">' . $title . '</a></p>';
        }
        return '';
    }
    /**
     * @param Document $document
     *
     * @return string
     */
    public static function create_invoice_link(Document $document): string
    {
        if ($document->get_type() === 'proforma' || $document->get_type() === 'correction') {
            if ($document->get_type() === 'proforma') {
                $meta_name = '_document_invoice_relation';
            }
            if ($document->get_type() === 'correction') {
                $meta_name = '_corrected_invoice_id';
            }
            $invoice_id = (int) get_post_meta($document->get_id(), $meta_name, \true);
            $class_name = $invoice_id ? 'document-generated' : 'document-not-generated';
            $title = $invoice_id ? esc_html__('Show Invoice', 'flexible-invoices') : esc_html__('Create Invoice', 'flexible-invoices');
            if (!$invoice_id) {
                $url = wp_nonce_url(admin_url('post-new.php?post_type=inspire_invoice&document_type=invoice&related_proforma_id=' . $document->get_id()));
            } else {
                $url = wp_nonce_url(admin_url('post.php?post=' . $invoice_id . '&action=edit'));
            }
            return '<p><a title="' . $title . '" class="button create-invoice ' . $class_name . '" href="' . esc_url($url) . '">' . $title . '</a></p>';
        }
        return '';
    }
    public static function download_email_links(Document $document): string
    {
        $output = '<div class="fi-download-email-links">';
        $output .= self::download_link($document);
        $output .= self::email_link($document);
        $output .= '</div>';
        return $output;
    }
    private static function email_status_class($status): string
    {
        if ($status === 'yes') {
            return 'email-send';
        } elseif ($status === 'no') {
            return 'email-not-send';
        }
        return 'email-unknown';
    }
    private static function get_email_tooltip_attr($status): string
    {
        if ($status === 'yes') {
            return esc_html__('Click to resend the e-mail', 'flexible-invoices');
        } elseif ($status === 'no') {
            return esc_html__('Click to send the e-mail', 'flexible-invoices');
        }
        return esc_html__('Click to send the e-mail', 'flexible-invoices');
    }
}
