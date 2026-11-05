<?php

/**
 * Invoices. Invoice post columns.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore
 */
namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\Documents\Document;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Decorators\DocumentDecorator;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Documents\Invoice;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\WooCommerce;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Integration\DocumentFactory;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\InvoicesIntegration;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\SettingsStrategy\AbstractSettingsStrategy;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce\Links;
use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Add custom columns in documents listing.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Integration
 */
class PostTypeColumns implements Hookable
{
    const ORDER_POST_TYPE = 'shop_order';
    /**
     * @var AbstractSettingsStrategy
     */
    private $strategy;
    /**
     * @var DocumentFactory
     */
    private $document_factory;
    /**
     * @param AbstractSettingsStrategy $strategy
     * @param DocumentFactory  $document_factory
     */
    public function __construct(AbstractSettingsStrategy $strategy, DocumentFactory $document_factory)
    {
        $this->strategy = $strategy;
        $this->document_factory = $document_factory;
    }
    /**
     * Fires hooks.
     */
    public function hooks()
    {
        add_filter('manage_edit-' . RegisterPostType::POST_TYPE_NAME . '_columns', [$this, 'add_custom_columns_filter']);
        add_action('manage_' . RegisterPostType::POST_TYPE_NAME . '_posts_custom_column', [$this, 'custom_columns_body_action'], 10, 2);
        if (is_admin()) {
            add_filter('post_class', [$this, 'mark_duplicated_invoice_row'], 10, 3);
        }
        add_filter('manage_edit-' . self::ORDER_POST_TYPE . '_columns', [$this, 'add_order_columns_filter']);
        add_action('manage_' . self::ORDER_POST_TYPE . '_posts_custom_column', [$this, 'order_columns_body_action'], 10, 2);
        add_filter('manage_woocommerce_page_wc-orders_columns', [$this, 'add_order_columns_filter']);
        add_action('manage_woocommerce_page_wc-orders_custom_column', [$this, 'order_columns_body_action'], 10, 2);
    }
    /**
     * @param array $columns
     *
     * @return array
     * @internal You should not use this directly from another application
     */
    public function add_custom_columns_filter(array $columns): array
    {
        unset($columns['date']);
        $columns['client'] = esc_html__('Customer', 'flexible-invoices');
        $columns['netto'] = esc_html__('Net price', 'flexible-invoices');
        $columns['gross'] = esc_html__('Gross price', 'flexible-invoices');
        $columns['issue'] = esc_html__('Issue date', 'flexible-invoices');
        $columns['pay'] = esc_html__('Due date', 'flexible-invoices');
        $columns['sale'] = esc_html__('Date of sale', 'flexible-invoices');
        $columns['order'] = esc_html__('Order', 'flexible-invoices');
        $columns['status'] = esc_html__('Payment status', 'flexible-invoices');
        $columns['currency'] = esc_html__('Currency', 'flexible-invoices');
        $columns['paymethod'] = esc_html__('Payment method', 'flexible-invoices');
        $columns['fi_actions'] = esc_html__('Actions', 'flexible-invoices');
        return apply_filters('fi/core/lists/columns/header', $columns);
    }
    /**
     * Native title link has no class hook. The row class lets the list CSS color it when the number is repeated.
     *
     * @param string[] $classes
     * @param string[] $css_class
     * @param int      $post_id
     *
     * @return string[]
     */
    public function mark_duplicated_invoice_row($classes, $css_class, $post_id)
    {
        // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
        $screen = get_current_screen();
        if (!$screen || 'edit-' . RegisterPostType::POST_TYPE_NAME !== $screen->id) {
            return $classes;
        }
        $post = get_post((int) $post_id);
        if (!$post instanceof \WP_Post || '' === $post->post_title || 'publish' !== $post->post_status) {
            return $classes;
        }
        if ($this->find_duplicates($post->post_title) > 1) {
            $classes[] = 'is_duplicated';
        }
        return $classes;
    }
    /**
     * @param string $column_name Column name,
     * @param int    $post_id     Post ID.
     *
     * @internal You should not use this directly from another application
     */
    public function custom_columns_body_action(string $column_name, int $post_id)
    {
        //phpcs:ignore
        global $post;
        $creator = $this->document_factory->get_document_creator($post->ID);
        $document = $creator->get_document();
        $document = new DocumentDecorator($document, $this->strategy);
        switch ($column_name) {
            case 'client':
                echo esc_html($document->get_customer()->get_name());
                break;
            case 'netto':
                echo esc_html((string) $document->get_total_net());
                break;
            case 'gross':
                echo esc_html((string) $document->get_total_gross());
                break;
            case 'issue':
                echo esc_html($document->get_date_of_issue());
                break;
            case 'pay':
                echo esc_html($document->get_date_of_pay());
                break;
            case 'order':
                $order_id = $document->get_order_id();
                if ($order_id) {
                    $order_number = $order_id;
                    if (WooCommerce::is_active()) {
                        $order = wc_get_order($order_id);
                        if ($order) {
                            $order_number = $order->get_order_number();
                        }
                    }
                    echo '<a href="' . admin_url('post.php?post=' . $order_id . '&action=edit') . '">' . esc_html($order_number) . '</a>';
                    //phpcs:ignore
                }
                break;
            case 'status':
                echo esc_html($document->get_payment_status_name());
                break;
            case 'sale':
                echo esc_html($document->get_date_of_sale());
                break;
            case 'currency':
                echo esc_html($document->get_currency());
                break;
            case 'paymethod':
                echo esc_html($document->get_payment_method_name());
                break;
            case 'fi_actions':
                echo Links::download_email_links($document);
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo Links::create_invoice_link($document);
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo InvoicesIntegration::is_super() ? Links::create_correction_link($document) : '';
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                break;
            default:
                echo esc_html(get_post_meta($post_id, '_invoice_' . $column_name, \true));
                break;
        }
        /**
         * Adds body for custom columns to the documents list.
         *
         * @param string    $column_name Column name.
         * @param Document $document    Document.
         *
         * @since 3.0.0
         */
        do_action('fi/core/lists/columns/body', $column_name, $document);
    }
    /**
     * Find duplicates.
     *
     * @param string $post_title Post title.
     *
     * @return int
     */
    private function find_duplicates(string $post_title): int
    {
        static $cache = [];
        if (isset($cache[$post_title])) {
            return $cache[$post_title];
        }
        global $wpdb;
        $duplicates = $wpdb->get_var($wpdb->prepare("SELECT count(ID) FROM {$wpdb->posts} WHERE `post_title` = %s AND `post_type` = %s AND `post_status` = 'publish'", $post_title, RegisterPostType::POST_TYPE_NAME));
        //phpcs:ignore
        $cache[$post_title] = (int) $duplicates;
        return $cache[$post_title];
    }
    /**
     * @param array $columns
     *
     * @return array
     * @internal You should not use this directly from another application
     */
    public function add_order_columns_filter(array $columns): array
    {
        $columns['fi_documents'] = esc_html__('Invoice', 'flexible-invoices');
        $columns['fi_actions'] = esc_html__('Invoice Action', 'flexible-invoices');
        return $columns;
    }
    /**
     * @param string $column_name Column name,
     * @param int    $post_id_or_order     Post ID.
     *
     * @internal You should not use this directly from another application
     */
    public function order_columns_body_action(string $column_name, $post_id_or_order)
    {
        //phpcs:ignore
        $order = is_numeric($post_id_or_order) ? wc_get_order($post_id_or_order) : $post_id_or_order;
        $document_id = (int) $order->get_meta(Invoice::META_GENERATED, \true);
        $creator = $this->document_factory->get_document_creator($document_id);
        $document = $creator->get_document();
        $document = new DocumentDecorator($document, $this->strategy);
        if ($column_name === 'fi_actions') {
            if (!$document_id) {
                echo Links::generate_link($order->get_id(), $document->get_type(), $creator->get_button_label(), $order->get_status() !== 'refunded');
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            } else {
                echo Links::download_email_links($document);
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
        }
        if ($document_id && $column_name === 'fi_documents') {
            //@phpstan-ignore-line
            echo Links::view_link($document, !$creator->is_allowed_for_edit());
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
}
