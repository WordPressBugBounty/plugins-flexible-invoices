<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce\Checkout;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout\Field\BillingTaxIdField;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
/**
 * Billing VAT field with the label and placeholder from Flexible Invoices settings.
 */
class BillingVatNumberField extends BillingTaxIdField
{
    private string $vat_label;
    private string $placeholder;
    public function __construct(ConfigInterface $config, bool $required, string $vat_label, string $placeholder)
    {
        parent::__construct($config, $required);
        $this->vat_label = $vat_label;
        $this->placeholder = $placeholder;
    }
    public function is_enabled(): bool
    {
        return $this->config->should_show_shipping_tax_id_field();
    }
    public function get_label(): string
    {
        return $this->vat_label;
    }
    public function get_checkout_args(): array
    {
        $args = parent::get_checkout_args();
        if ('' !== $this->placeholder) {
            $args['placeholder'] = $this->placeholder;
        }
        return $args;
    }
}
