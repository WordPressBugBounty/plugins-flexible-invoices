<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout\Field\AbstractCheckoutField;
/**
 * Configuration contract for the invoices checkout fields.
 * Integrating plugins must implement this interface to provide all required settings.
 */
interface ConfigInterface
{
    public const BEHAVIOR_ASK_USER = 'ask_user';
    public const BEHAVIOR_ALWAYS_SHOW = 'always_show';
    public const VIES_FAILURE_REJECT = 'reject';
    public const VIES_FAILURE_ACCEPT_WITH_VAT = 'accept_with_vat';
    public const VIES_FAILURE_ACCEPT_WITHOUT_VAT = 'accept_without_vat';
    /**
     * Get the meta key used to store the "want invoice" flag on the Order (wp_postmeta).
     */
    public function get_want_invoice_key(): string;
    /**
     * Get the meta key used to store the "want invoice" preference on the User Profile (wp_usermeta).
     */
    public function get_want_invoice_user_key(): string;
    /**
     * Get the meta key used to store the Billing Tax ID/VAT on the Order (wp_postmeta).
     */
    public function get_billing_tax_id_key(): string;
    /**
     * Get the meta key used to store the Shipping Tax ID/VAT on the Order (wp_postmeta).
     */
    public function get_shipping_tax_id_key(): string;
    /**
     * Get the meta key used to store the Billing Tax ID/VAT preference on the User Profile (wp_usermeta).
     */
    public function get_billing_tax_id_user_key(): string;
    /**
     * Get the meta key used to store the Shipping Tax ID/VAT preference on the User Profile (wp_usermeta).
     */
    public function get_shipping_tax_id_user_key(): string;
    /**
     * Get the meta key used to store the Billing Company Name on the Order (wp_postmeta).
     */
    public function get_billing_company_key(): string;
    /**
     * Get the meta key used to store the Shipping Company Name on the Order (wp_postmeta).
     */
    public function get_shipping_company_key(): string;
    /**
     * Get the meta key used to store the Billing Company Name preference on the User Profile (wp_usermeta).
     */
    public function get_billing_company_user_key(): string;
    /**
     * Get the meta key used to store the Shipping Company Name preference on the User Profile (wp_usermeta).
     */
    public function get_shipping_company_user_key(): string;
    /**
     * Get the meta key used to store the VAT confirmation status on the Order (wp_postmeta).
     */
    public function get_vat_confirmation_key(): string;
    /**
     * Get the meta key used to store the location confirmation status on the Order (wp_postmeta).
     */
    public function get_location_confirmation_key(): string;
    /**
     * Get the meta key used to store the customer IP country on the Order (wp_postmeta).
     */
    public function get_ip_country_key(): string;
    /**
     * Get the meta key used to store whether the VAT number passed VIES on the Order (wp_postmeta).
     */
    public function get_vat_number_is_valid_key(): string;
    /**
     * Get the meta key used to store whether VIES was queried for the VAT number on the Order (wp_postmeta).
     */
    public function get_vat_number_is_validated_key(): string;
    /**
     * Get the registered Gutenberg block name (e.g. 'wpdesk/invoices-checkout').
     */
    public function get_block_name(): string;
    /**
     * Get the normalized block name (used for HTML IDs and classes, e.g. 'wpdesk-invoices-checkout').
     */
    public function get_normalized_block_name(): string;
    /**
     * Get the localized title of the block displayed in the editor.
     */
    public function get_block_title(): string;
    /**
     * Get the localized description of the block displayed in the editor.
     */
    public function get_block_description(): string;
    /**
     * Determine if the company field should be shown in the checkout form.
     */
    public function should_show_company_field(): bool;
    /**
     * Get the array of custom validators used to validate checkout fields (e.g., VAT format).
     *
     * @return Validation\Validator\VatValidator[]
     */
    public function get_validators(): array;
    /**
     * Determine if VAT should be removed from the base price for B2B customers.
     */
    public function should_remove_vat_from_base_b2b(): bool;
    /**
     * Determine if checkout should apply B2B VAT exemption (reverse charge) from a tax ID.
     *
     * When false, entering a VAT number must not strip VAT from the cart.
     */
    public function should_apply_tax_exemption(): bool;
    /**
     * Determine if non-EU buyers should have VAT stripped at checkout (export 0%).
     */
    public function should_exempt_non_eu_customers(): bool;
    /**
     * Determine if B2B/export tax exemption should run only when the cart has OSS tax-class products.
     */
    public function should_gate_tax_exemption_on_oss_products(): bool;
    /**
     * Determine if the shipping VAT field should be shown (classic checkout, admin, Blocks).
     * When false, shipping NIP is not registered, not validated, and Blocks hide the receiver UI.
     */
    public function should_show_shipping_tax_id_field(): bool;
    /**
     * Get the meta key used to store a copy of the billing VAT number for VIES evidence.
     *
     * Return an empty string to skip writing this key.
     */
    public function get_vat_number_evidence_key(): string;
    /**
     * Get the meta key used to store whether the customer self-declared a VAT number.
     *
     * Return an empty string to skip writing this key.
     */
    public function get_vat_number_self_declared_key(): string;
    /**
     * Get the meta key used to store whether the customer self-declared their country (OSS).
     *
     * Return an empty string to skip writing this key.
     */
    public function get_customer_self_declared_country_key(): string;
    /**
     * Determine what to do when VIES rejects the number or the VIES API is unavailable.
     *
     * Format errors (length / control sum) always block checkout.
     *
     * @return self::VIES_FAILURE_REJECT|self::VIES_FAILURE_ACCEPT_WITH_VAT|self::VIES_FAILURE_ACCEPT_WITHOUT_VAT
     */
    public function get_vies_failure_handling(): string;
    /**
     * Determine if billing country should be compared with the customer IP country (OSS).
     */
    public function should_validate_customer_ip(): bool;
    /**
     * Determine if the EU VAT / VIES metabox should be shown on the order edit screen.
     */
    public function should_show_vies_order_metabox(): bool;
    /**
     * Get the array of tax classes considered as OSS (One Stop Shop) tax classes.
     *
     * @return string[]
     */
    public function get_oss_tax_classes(): array;
    /**
     * Get the behavior of the invoice fields in checkout (e.g. self::BEHAVIOR_ASK_USER).
     */
    public function get_field_behavior(): string;
    /**
     * Returns the checkout fields to be registered on the classic shortcode checkout.
     * Integrating plugins can override this to add, remove, or reorder fields.
     *
     * @return AbstractCheckoutField[]
     */
    public function get_checkout_fields(): array;
    /**
     * Plugin lang/ directory used by wp_set_script_translations after merge-translations.
     */
    public function get_plugin_lang_dir(): string;
    /**
     * Integrating plugin text domain after merge-translations rewrites library JS.
     */
    public function get_plugin_text_domain(): string;
}
