<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\Assets\AssetsHandler;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Admin\OrderEdit;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Blocks\BlocksIntegration;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\I18n\ScriptTranslations;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout\CheckoutFields;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout\CheckoutSave;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Order\AddressFormatter;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\User\UserProfile;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\ViesApiFailurePolicy;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\View\SimpleTemplateRenderer;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\Validator\LengthValidation;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\Validator\ControlSumValidation;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\Validator\ViesValidation;
/**
 * Main entry point for the invoices-checkout library.
 */
class InvoicesCheckout
{
    public const KEY_WANT_INVOICE = 'want_invoice';
    public const KEY_BILLING_TAX_ID = 'billing_tax_id';
    public const KEY_SHIPPING_TAX_ID = 'shipping_tax_id';
    public const KEY_BILLING_COMPANY = 'billing_company';
    public const KEY_SHIPPING_COMPANY = 'shipping_company';
    public const SESSION_KEY_BLOCKS_DATA = 'wpd_invoices_checkout_blocks_data';
    private ConfigInterface $config;
    private LibraryInfo $library_info;
    public function __construct(ConfigInterface $config, LibraryInfo $library_info)
    {
        $this->config = $config;
        $this->library_info = $library_info;
    }
    public function hooks(): void
    {
        $validators = $this->config->get_validators();
        if ([] === $validators) {
            $validators = [new LengthValidation(), new ControlSumValidation(), new ViesValidation()];
        }
        $validator_service = new Service\VatNumberValidator($validators);
        $order_saver = new Service\OrderInvoiceDataSaver($this->config);
        $user_saver = new Service\UserInvoiceDataSaver($this->config);
        $vies_meta_writer = new Service\ViesOrderMetaWriter($this->config, $validator_service);
        $template_renderer = new SimpleTemplateRenderer($this->library_info);
        $checkout_confirmations = new Checkout\CheckoutConfirmations($this->config, $template_renderer);
        $checkout_confirmations->hooks();
        $vies_api_failure_policy = new ViesApiFailurePolicy($this->config);
        $checkout_fields = new CheckoutFields($this->config, $validator_service, $checkout_confirmations, $vies_api_failure_policy);
        $checkout_fields->hooks();
        $woocommerce_helper = new Service\WooCommerceHelper($this->config);
        $oss_geolocation = new Checkout\OssGeolocationService($this->config, $woocommerce_helper, $validator_service, $checkout_confirmations);
        $oss_geolocation->hooks();
        $tax_exemption_manager = new Checkout\TaxExemptionManager($this->config, $validator_service, $woocommerce_helper, $vies_api_failure_policy);
        $tax_exemption_manager->hooks();
        $checkout_save = new CheckoutSave($this->config, $order_saver, $vies_meta_writer);
        $checkout_save->hooks();
        $script_translations = new ScriptTranslations($this->config);
        $script_translations->hooks();
        $assets_handler = new AssetsHandler($this->config, $this->library_info, $script_translations);
        $assets_handler->hooks();
        $user_profile = new UserProfile($this->config, $validator_service);
        $user_profile->hooks();
        $my_account_address = new User\MyAccountAddress($this->config, $validator_service);
        $my_account_address->hooks();
        $user_meta_sync = new User\UserMetaSync($this->config, $user_saver);
        $user_meta_sync->hooks();
        $admin_ajax = new User\CustomerAdminAjax($this->config);
        $admin_ajax->hooks();
        $order_edit = new OrderEdit($this->config);
        $order_edit->hooks();
        $vies_metabox = new Admin\ViesOrderMetaBox($this->config, $woocommerce_helper, $template_renderer);
        $vies_metabox->hooks();
        $address_formatter = new AddressFormatter($this->config);
        $address_formatter->hooks();
        $blocks_integration = new BlocksIntegration($this->config, $this->library_info, $validator_service, $order_saver, $user_saver, $woocommerce_helper, $script_translations, $vies_api_failure_policy, $vies_meta_writer);
        $blocks_integration->hooks();
    }
}
