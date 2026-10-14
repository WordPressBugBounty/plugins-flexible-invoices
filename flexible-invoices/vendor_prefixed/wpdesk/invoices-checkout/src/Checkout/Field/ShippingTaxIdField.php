<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout\Field;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
/**
 * Shipping VAT Number (NIP) text field for the shipping section.
 */
class ShippingTaxIdField extends AbstractCheckoutField
{
    private bool $required;
    public function __construct(ConfigInterface $config, bool $required = \false)
    {
        parent::__construct($config);
        $this->required = $required;
    }
    public function is_enabled(): bool
    {
        return $this->config->should_show_shipping_tax_id_field();
    }
    public function get_id(): string
    {
        return $this->config->get_shipping_tax_id_key();
    }
    public function get_section(): string
    {
        return 'shipping';
    }
    public function get_user_meta_key(): string
    {
        return $this->config->get_shipping_tax_id_user_key();
    }
    public function get_label(): string
    {
        return __('VAT Number', 'flexible-invoices');
    }
    public function get_copied_from_key(): string
    {
        return $this->config->get_billing_tax_id_user_key();
    }
    public function get_checkout_args(): array
    {
        return ['type' => 'text', 'label' => $this->get_label(), 'required' => $this->required, 'id' => $this->get_html_id(), 'class' => ['form-row-wide', 'wpd-invoices-shipping-tax-id-field', 'update_totals_on_change'], 'clear' => \true, 'default' => $this->get_saved_user_value(), 'priority' => 31];
    }
}
