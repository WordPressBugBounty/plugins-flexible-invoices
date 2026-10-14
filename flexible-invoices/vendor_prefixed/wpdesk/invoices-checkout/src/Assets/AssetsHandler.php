<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Assets;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\I18n\ScriptTranslations;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\LibraryInfo;
/**
 * Handles enqueuing assets for the shortcode checkout page.
 */
class AssetsHandler
{
    private ConfigInterface $config;
    private LibraryInfo $library_info;
    private ScriptTranslations $script_translations;
    public function __construct(ConfigInterface $config, LibraryInfo $library_info, ScriptTranslations $script_translations)
    {
        $this->config = $config;
        $this->library_info = $library_info;
        $this->script_translations = $script_translations;
    }
    /**
     * Hooks into WordPress.
     */
    public function hooks(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }
    /**
     * Enqueues scripts and localizes configuration variables.
     */
    public function enqueue_scripts(): void
    {
        if (is_checkout()) {
            $asset_path = $this->library_info->get_library_dir() . '/assets/js/checkout.asset.php';
            $asset = require $asset_path;
            $handle_prefix = $this->config->get_block_name();
            $handle = $handle_prefix . '-checkout';
            wp_enqueue_script($handle, $this->library_info->get_js_assets_url() . 'checkout.js', $asset['dependencies'], $asset['version'], \true);
            $this->script_translations->set_for_handle($handle, 'assets/js/checkout.js');
            wp_localize_script($handle, 'wpdInvoicesCheckout', ['wantInvoiceKey' => $this->config->get_want_invoice_user_key(), 'billingTaxIdKey' => $this->config->get_billing_tax_id_user_key(), 'shippingTaxIdKey' => $this->config->get_shipping_tax_id_user_key(), 'fieldBehavior' => $this->config->get_field_behavior()]);
        }
    }
}
