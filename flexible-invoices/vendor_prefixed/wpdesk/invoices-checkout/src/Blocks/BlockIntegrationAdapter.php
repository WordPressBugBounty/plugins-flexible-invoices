<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Blocks;

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\I18n\ScriptTranslations;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\LibraryInfo;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Service\WooCommerceHelper;
use WC_Geolocation;
/**
 * WooCommerce Blocks Integration Adapter.
 */
class BlockIntegrationAdapter implements IntegrationInterface
{
    private ConfigInterface $config;
    private LibraryInfo $library_info;
    private WooCommerceHelper $woocommerce_helper;
    private ScriptTranslations $script_translations;
    public function __construct(ConfigInterface $config, LibraryInfo $library_info, WooCommerceHelper $woocommerce_helper, ScriptTranslations $script_translations)
    {
        $this->config = $config;
        $this->library_info = $library_info;
        $this->woocommerce_helper = $woocommerce_helper;
        $this->script_translations = $script_translations;
    }
    /**
     * When called, invokes setup for the integration.
     */
    public function initialize(): void
    {
        $this->register_scripts();
    }
    /**
     * Returns the integration name.
     */
    public function get_name(): string
    {
        return $this->config->get_normalized_block_name();
    }
    /**
     * Returns frontend script handles.
     *
     * @return string[]
     */
    public function get_script_handles(): array
    {
        return [$this->get_name() . '-frontend'];
    }
    /**
     * Returns editor script handles.
     *
     * @return string[]
     */
    public function get_editor_script_handles(): array
    {
        return [$this->get_name() . '-frontend', $this->get_name() . '-editor'];
    }
    /**
     * Data made available to client side.
     */
    public function get_script_data(): array
    {
        $user_id = get_current_user_id();
        $want_key = $this->config->get_want_invoice_key();
        $billing_tax_key = $this->config->get_billing_tax_id_key();
        $shipping_tax_key = $this->config->get_shipping_tax_id_key();
        $billing_company = $this->config->get_billing_company_key();
        $shipping_company = $this->config->get_shipping_company_key();
        $data = [$want_key => 'no', $billing_tax_key => '', $shipping_tax_key => '', $billing_company => '', $shipping_company => ''];
        if ($user_id) {
            $want_val = get_user_meta($user_id, $this->config->get_want_invoice_user_key(), \true);
            $data[$want_key] = filter_var($want_val, \FILTER_VALIDATE_BOOLEAN) ? 'yes' : 'no';
            $data[$billing_tax_key] = (string) get_user_meta($user_id, $this->config->get_billing_tax_id_user_key(), \true);
            $data[$shipping_tax_key] = (string) get_user_meta($user_id, $this->config->get_shipping_tax_id_user_key(), \true);
            $data[$billing_company] = (string) get_user_meta($user_id, $this->config->get_billing_company_user_key(), \true);
            $data[$shipping_company] = (string) get_user_meta($user_id, $this->config->get_shipping_company_user_key(), \true);
        }
        return $data;
    }
    private function get_common_script_data(): array
    {
        return ['block' => ['name' => $this->config->get_block_name(), 'normalized_name' => $this->config->get_normalized_block_name(), 'title' => $this->config->get_block_title(), 'description' => $this->config->get_block_description()], 'show_company_field' => $this->config->should_show_company_field() ? 'yes' : 'no', 'show_billing_tax_id_field' => $this->is_tax_id_field_enabled($this->config->get_billing_tax_id_key()) ? 'yes' : 'no', 'show_shipping_tax_id_field' => $this->config->should_show_shipping_tax_id_field() ? 'yes' : 'no', 'vat_number_required' => $this->checkout_field_is_required($this->config->get_billing_tax_id_key()) ? 'yes' : 'no', 'shipping_vat_number_required' => $this->checkout_field_is_required($this->config->get_shipping_tax_id_key()) ? 'yes' : 'no'];
    }
    private function is_tax_id_field_enabled(string $field_id): bool
    {
        foreach ($this->config->get_checkout_fields() as $field) {
            if ($field->get_id() === $field_id) {
                return $field->is_enabled();
            }
        }
        return \false;
    }
    private function checkout_field_is_required(string $field_id): bool
    {
        foreach ($this->config->get_checkout_fields() as $field) {
            if ($field->get_id() !== $field_id) {
                continue;
            }
            return !empty($field->get_checkout_args()['required']);
        }
        return \false;
    }
    /**
     * Registers scripts for the block.
     */
    private function register_scripts(): void
    {
        $handle_prefix = $this->get_name();
        $editor_asset_path = $this->library_info->get_library_dir() . '/assets/js/editor.asset.php';
        $frontend_asset_path = $this->library_info->get_library_dir() . '/assets/js/frontend.asset.php';
        $editor_asset = require $editor_asset_path;
        $frontend_asset = require $frontend_asset_path;
        wp_register_script($handle_prefix . '-frontend', $this->library_info->get_js_assets_url() . 'frontend.js', $frontend_asset['dependencies'], $frontend_asset['version'], \true);
        wp_register_script($handle_prefix . '-editor', $this->library_info->get_js_assets_url() . 'editor.js', $editor_asset['dependencies'], $editor_asset['version'], \true);
        $this->script_translations->set_for_handle($handle_prefix . '-frontend', 'assets/js/frontend.js');
        $this->script_translations->set_for_handle($handle_prefix . '-editor', 'assets/js/editor.js');
        wp_localize_script($handle_prefix . '-editor', 'wpdInvoicesCheckoutBlockConfig', $this->get_common_script_data());
        $frontend_data = array_merge($this->get_common_script_data(), ['wc_company_field' => get_option('woocommerce_checkout_company_field', 'optional'), 'initData' => $this->get_script_data(), 'ip_country' => $this->config->should_validate_customer_ip() ? $this->get_ip_country() : '', 'has_oss_products' => $this->woocommerce_helper->cart_has_oss_products(), 'validate_customer_ip' => $this->config->should_validate_customer_ip() ? 'yes' : 'no', 'eu_countries' => $this->get_eu_countries(), 'field_behavior' => $this->config->get_field_behavior(), 'needs_shipping' => WC()->cart && WC()->cart->needs_shipping() ? 'yes' : 'no', 'keys' => ['want_invoice' => $this->config->get_want_invoice_key(), 'billing_tax_id' => $this->config->get_billing_tax_id_key(), 'shipping_tax_id' => $this->config->get_shipping_tax_id_key(), 'billing_company' => $this->config->get_billing_company_key(), 'shipping_company' => $this->config->get_shipping_company_key(), 'vat_confirmation' => $this->config->get_vat_confirmation_key(), 'location_confirmation' => $this->config->get_location_confirmation_key()]]);
        wp_localize_script($handle_prefix . '-frontend', 'wpdInvoicesCheckoutBlockConfig', $frontend_data);
    }
    /**
     * Gets the IP-based country code for the current visitor.
     */
    private function get_ip_country(): string
    {
        if (!class_exists('WC_Geolocation')) {
            return '';
        }
        $ip_address = WC_Geolocation::get_ip_address();
        $geo = WC_Geolocation::geolocate_ip($ip_address);
        return $geo['country'] ?? '';
    }
    /**
     * Gets the list of EU member states country codes.
     */
    private function get_eu_countries(): array
    {
        $countries = new \WC_Countries();
        return $countries->get_european_union_countries('eu_vat');
    }
}
