<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce\Checkout;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout\Field\WantInvoiceField;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\Validator\NoValidation;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\Validator\NonEmptyVatNumber;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\Validator\ViesValidation;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\InvoicesIntegration;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Settings;
/**
 * Maps Flexible Invoices checkout settings onto invoices-checkout.
 */
class InvoicesCheckoutConfig implements ConfigInterface
{
    public const WANT_INVOICE_KEY = '_billing_invoice_ask';
    public const WANT_INVOICE_USER_KEY = 'billing_invoice_ask';
    public const BILLING_TAX_ID_KEY = '_billing_vat_number';
    public const SHIPPING_TAX_ID_KEY = '_shipping_vat_number';
    public const BILLING_TAX_ID_USER_KEY = 'billing_vat_number';
    public const SHIPPING_TAX_ID_USER_KEY = 'shipping_vat_number';
    public const BILLING_COMPANY_KEY = '_billing_company';
    public const SHIPPING_COMPANY_KEY = '_shipping_company';
    public const BILLING_COMPANY_USER_KEY = 'billing_company';
    public const SHIPPING_COMPANY_USER_KEY = 'shipping_company';
    public const VAT_CONFIRMATION_KEY = '_vat_confirmation';
    public const LOCATION_CONFIRMATION_KEY = '_location_confirmation';
    public const IP_COUNTRY_KEY = '_customer_ip_country';
    public const VAT_NUMBER_IS_VALID_KEY = '_vat_number_is_valid';
    public const VAT_NUMBER_IS_VALIDATED_KEY = '_vat_number_is_validated';
    public const VAT_NUMBER_EVIDENCE_KEY = '_vat_number';
    public const VAT_NUMBER_SELF_DECLARED_KEY = '_vat_number_self_declared';
    public const CUSTOMER_SELF_DECLARED_COUNTRY_KEY = '_customer_self_declared_country';
    private Settings $settings;
    private string $lang_dir;
    public function __construct(Settings $settings, string $lang_dir)
    {
        $this->settings = $settings;
        $this->lang_dir = $lang_dir;
    }
    public function get_want_invoice_key(): string
    {
        return self::WANT_INVOICE_KEY;
    }
    public function get_want_invoice_user_key(): string
    {
        return self::WANT_INVOICE_USER_KEY;
    }
    public function get_billing_tax_id_key(): string
    {
        return self::BILLING_TAX_ID_KEY;
    }
    public function get_shipping_tax_id_key(): string
    {
        return self::SHIPPING_TAX_ID_KEY;
    }
    public function get_billing_tax_id_user_key(): string
    {
        return self::BILLING_TAX_ID_USER_KEY;
    }
    public function get_shipping_tax_id_user_key(): string
    {
        return self::SHIPPING_TAX_ID_USER_KEY;
    }
    public function get_billing_company_key(): string
    {
        return self::BILLING_COMPANY_KEY;
    }
    public function get_shipping_company_key(): string
    {
        return self::SHIPPING_COMPANY_KEY;
    }
    public function get_billing_company_user_key(): string
    {
        return self::BILLING_COMPANY_USER_KEY;
    }
    public function get_shipping_company_user_key(): string
    {
        return self::SHIPPING_COMPANY_USER_KEY;
    }
    public function get_vat_confirmation_key(): string
    {
        return self::VAT_CONFIRMATION_KEY;
    }
    public function get_location_confirmation_key(): string
    {
        return self::LOCATION_CONFIRMATION_KEY;
    }
    public function get_ip_country_key(): string
    {
        return self::IP_COUNTRY_KEY;
    }
    public function get_vat_number_is_valid_key(): string
    {
        return self::VAT_NUMBER_IS_VALID_KEY;
    }
    public function get_vat_number_is_validated_key(): string
    {
        return self::VAT_NUMBER_IS_VALIDATED_KEY;
    }
    public function get_vat_number_evidence_key(): string
    {
        return self::VAT_NUMBER_EVIDENCE_KEY;
    }
    public function get_vat_number_self_declared_key(): string
    {
        return self::VAT_NUMBER_SELF_DECLARED_KEY;
    }
    public function get_customer_self_declared_country_key(): string
    {
        return self::CUSTOMER_SELF_DECLARED_COUNTRY_KEY;
    }
    public function get_block_name(): string
    {
        return 'wpdesk/flexible-invoices-checkout';
    }
    public function get_normalized_block_name(): string
    {
        return str_replace('/', '-', $this->get_block_name());
    }
    public function get_block_title(): string
    {
        return __('Flexible Invoices Checkout', 'flexible-invoices');
    }
    public function get_block_description(): string
    {
        return __('Invoice fields for Flexible Invoices.', 'flexible-invoices');
    }
    /**
     * The block toggle can turn company replacement off, not on.
     * PHP allows it. The block keeps the library default, which is on.
     */
    public function should_show_company_field(): bool
    {
        return \true;
    }
    /**
     * Shipping VAT follows the billing VAT field. It is shown only when the
     * customer enters a separate shipping address.
     */
    public function should_show_shipping_tax_id_field(): bool
    {
        return 'yes' === $this->settings->get('woocommerce_add_nip_field', 'no');
    }
    public function get_validators(): array
    {
        $validators = [];
        if ($this->is_vat_required()) {
            $validators[] = new NonEmptyVatNumber();
        }
        // Format checks treat an empty number as valid, so the required check stays ahead of them.
        // Punctuation is first: the library strips hyphens and would otherwise accept them.
        if ('yes' === $this->settings->get('woocommerce_validate_nip', 'no')) {
            $validators[] = new LegacyNipPunctuationValidator($this->get_vat_label());
            $validators[] = new LegacyNipFormatValidator($this->get_vat_label());
        }
        if ($this->is_vies_enabled()) {
            $validators[] = new ViesValidation();
        }
        if ([] === $validators) {
            $validators[] = new NoValidation();
        }
        return $validators;
    }
    public function should_remove_vat_from_base_b2b(): bool
    {
        return \false;
    }
    public function should_apply_tax_exemption(): bool
    {
        return $this->is_vies_enabled();
    }
    public function should_exempt_non_eu_customers(): bool
    {
        return \false;
    }
    public function should_gate_tax_exemption_on_oss_products(): bool
    {
        return \true;
    }
    public function get_vies_failure_handling(): string
    {
        $handling = (string) $this->settings->get('woocommerce_eu_vat_failure_handling', self::VIES_FAILURE_REJECT);
        $allowed = [self::VIES_FAILURE_REJECT, self::VIES_FAILURE_ACCEPT_WITH_VAT, self::VIES_FAILURE_ACCEPT_WITHOUT_VAT];
        if (in_array($handling, $allowed, \true)) {
            return $handling;
        }
        return self::VIES_FAILURE_REJECT;
    }
    public function should_validate_customer_ip(): bool
    {
        return $this->is_vies_enabled() && 'yes' === $this->settings->get('woocommerce_moss_validate_ip', 'no');
    }
    public function should_show_vies_order_metabox(): bool
    {
        return $this->is_vies_enabled();
    }
    public function get_oss_tax_classes(): array
    {
        if (!$this->pro_tax_features_enabled()) {
            return [];
        }
        $classes = $this->settings->get('woocommerce_moss_tax_classes', []);
        if (!is_array($classes)) {
            return [];
        }
        return $classes;
    }
    public function get_field_behavior(): string
    {
        if ('yes' === $this->settings->get('woocommerce_add_invoice_ask_field', 'no')) {
            return self::BEHAVIOR_ASK_USER;
        }
        return self::BEHAVIOR_ALWAYS_SHOW;
    }
    public function get_checkout_fields(): array
    {
        $fields = [];
        $ask_user = self::BEHAVIOR_ASK_USER === $this->get_field_behavior();
        if ($ask_user) {
            $fields[] = new WantInvoiceField($this);
        }
        $vat_required = $this->is_vat_required();
        $fields[] = new BillingVatNumberField($this, $vat_required, $this->get_vat_label(), $this->get_vat_placeholder());
        $fields[] = new ShippingVatNumberField($this, $vat_required, $this->get_vat_label(), $this->get_vat_placeholder());
        return $fields;
    }
    public function get_plugin_lang_dir(): string
    {
        return $this->lang_dir;
    }
    public function get_plugin_text_domain(): string
    {
        return 'flexible-invoices';
    }
    private function is_vat_required(): bool
    {
        return 'yes' === $this->settings->get('woocommerce_nip_required', 'no');
    }
    /**
     * VIES and OSS are PRO settings. The free screen cannot save them, but a leftover
     * "yes" must not turn the library behavior on.
     */
    private function pro_tax_features_enabled(): bool
    {
        return InvoicesIntegration::is_super();
    }
    private function is_vies_enabled(): bool
    {
        if (!$this->pro_tax_features_enabled()) {
            return \false;
        }
        if (!function_exists('wc_tax_enabled') || !wc_tax_enabled()) {
            return \false;
        }
        return 'yes' === $this->settings->get('woocommerce_eu_vat_vies_validate', 'no');
    }
    private function get_vat_label(): string
    {
        $label = $this->settings->get('woocommerce_nip_label', '');
        if (!is_string($label) || '' === trim($label)) {
            return __('VAT Number', 'flexible-invoices');
        }
        return __($label, 'flexible-invoices');
        //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
    }
    private function get_vat_placeholder(): string
    {
        $placeholder = $this->settings->get('woocommerce_nip_placeholder', '');
        if (!is_string($placeholder) || '' === trim($placeholder)) {
            return '';
        }
        return __($placeholder, 'flexible-invoices');
        //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
    }
}
