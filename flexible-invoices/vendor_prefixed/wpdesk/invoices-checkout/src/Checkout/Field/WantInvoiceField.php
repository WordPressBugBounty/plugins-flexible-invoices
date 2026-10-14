<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout\Field;

use WC_Order;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
/**
 * "I want an invoice" checkbox field for the billing section.
 */
class WantInvoiceField extends AbstractCheckoutField
{
    public function get_id(): string
    {
        return $this->config->get_want_invoice_key();
    }
    public function get_section(): string
    {
        return 'billing';
    }
    public function get_user_meta_key(): string
    {
        return $this->config->get_want_invoice_user_key();
    }
    public function get_label(): string
    {
        return __('I want an invoice', 'flexible-invoices');
    }
    public function is_enabled(): bool
    {
        return ConfigInterface::BEHAVIOR_ASK_USER === $this->config->get_field_behavior();
    }
    public function get_checkout_args(): array
    {
        $default = 0;
        $saved = $this->get_saved_user_value();
        if (filter_var($saved, \FILTER_VALIDATE_BOOLEAN)) {
            $default = 1;
        }
        return ['type' => 'checkbox', 'label' => $this->get_label(), 'required' => \false, 'id' => $this->get_html_id(), 'class' => ['form-row-wide', 'wpd-invoices-want-invoice-field', 'update_totals_on_change'], 'clear' => \true, 'default' => $default, 'priority' => 31];
    }
    public function get_admin_order_args(WC_Order $order): array
    {
        $val = $order->get_meta($this->get_id());
        $want_invoice = filter_var($val, \FILTER_VALIDATE_BOOLEAN);
        return ['label' => __('Requested Invoice', 'flexible-invoices'), 'show' => \false, 'type' => 'checkbox', 'value' => $want_invoice ? '1' : '0', 'cbvalue' => '1', 'wrapper_class' => $this->get_id() . '_field'];
    }
    public function get_my_account_args(int $user_id): array
    {
        $saved = $user_id ? get_user_meta($user_id, $this->get_user_meta_key(), \true) : '0';
        $is_checked = filter_var($saved, \FILTER_VALIDATE_BOOLEAN);
        return ['type' => 'checkbox', 'label' => $this->get_label(), 'required' => \false, 'class' => ['form-row-wide'], 'clear' => \true, 'id' => $this->get_html_id(), 'value' => $is_checked ? 1 : 0, 'default' => $is_checked ? 1 : 0, 'priority' => 31];
    }
    public function get_user_profile_args(): array
    {
        return ['label' => $this->get_label(), 'description' => '', 'type' => 'checkbox'];
    }
    public function sanitize_for_storage($raw_value): string
    {
        return filter_var($raw_value, \FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
    }
    public function format_address_replacement(string $value): string
    {
        if (empty($value)) {
            return '';
        }
        $display = filter_var($value, \FILTER_VALIDATE_BOOLEAN) ? __('Yes', 'flexible-invoices') : __('No', 'flexible-invoices');
        return __('Requested Invoice', 'flexible-invoices') . ': ' . $display;
    }
}
