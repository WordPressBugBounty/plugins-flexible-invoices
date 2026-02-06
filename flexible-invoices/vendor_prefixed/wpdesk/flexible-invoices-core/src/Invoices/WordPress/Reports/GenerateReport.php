<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\Reports;

use WP_Query;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Integration\DocumentFactory;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\LibraryInfo;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Settings;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\DateFromToMetaQuery;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\RegisterPostType;
use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDeskFIVendor\WPDesk\View\Renderer\Renderer;
/**
 * Generate report.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore
 */
class GenerateReport extends DateFromToMetaQuery implements Hookable
{
    /**
     * @var Settings
     */
    private $settings;
    /**
     * @var DocumentFactory
     */
    private $document_factory;
    /**
     * @var Renderer
     */
    private $renderer;
    /**
     * @var LibraryInfo
     */
    private $library_info;
    /**
     * @param Settings        $strategy
     * @param DocumentFactory $document_factory
     * @param Renderer        $renderer
     * @param LibraryInfo     $library_info
     */
    public function __construct(Settings $strategy, DocumentFactory $document_factory, Renderer $renderer, LibraryInfo $library_info)
    {
        $this->settings = $strategy;
        $this->document_factory = $document_factory;
        $this->renderer = $renderer;
        $this->library_info = $library_info;
    }
    /**
     * Fires hooks.
     */
    public function hooks()
    {
        add_action('wp_ajax_fiw_generate_report', [$this, 'generate_report_action']);
    }
    /**
     * @param string $currency
     *
     * @return array
     */
    private function get_currency_query(string $currency): array
    {
        if ($currency) {
            return ['key' => '_currency', 'value' => $currency];
        }
        return [];
    }
    /**
     * Generate invoice reports from settings page.
     */
    public function generate_report_action()
    {
        $post_data = isset($_POST['reports']) ? wp_unslash($_POST['reports']) : [];
        //phpcs:ignore
        if (isset($post_data['report_download']) && wp_verify_nonce($post_data['report_download'], 'download_report') && current_user_can('manage_options')) {
            $currency = isset($post_data['currency']) ? esc_html($post_data['currency']) : \false;
            if ($currency) {
                $currency_decimal_separator = '.';
                $inspire_invoices_currency = get_option('inspire_invoices_currency', []);
                if (is_array($inspire_invoices_currency)) {
                    foreach ($inspire_invoices_currency as $currency_config) {
                        if ($currency_config['currency'] === $currency) {
                            $currency_decimal_separator = $currency_config['decimal_separator'];
                            break;
                        }
                    }
                }
                $date_query = $this->get_meta_query($post_data);
                $currency_query = $this->get_currency_query($currency);
                $query_args = ['post_type' => RegisterPostType::POST_TYPE_NAME, 'orderby' => 'date', 'order' => 'ASC', 'post_status' => 'publish', 'nopaging' => \true, 'suppress_filters' => \true];
                if (!empty($date_query)) {
                    $query_args['meta_query'][] = $date_query;
                }
                if (!empty($date_query)) {
                    $query_args['meta_query'][] = $currency_query;
                }
                $query = new WP_Query($query_args);
                $documents = [];
                $posts = $query->get_posts();
                foreach ($posts as $post) {
                    $document = $this->document_factory->get_document_creator($post->ID)->get_document();
                    if ($document->get_type() !== 'proforma') {
                        $documents[] = $this->document_factory->get_document_creator($post->ID)->get_document();
                    }
                }
                $this->renderer->output_render('report/report', ['plugin' => $this, 'library_info' => $this->library_info, 'currency_decimal_separator' => $currency_decimal_separator, 'documents' => $documents, 'settings' => $this->settings, 'post_data' => $post_data]);
            }
            die;
        }
    }
}
