<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout\Field;

use WC_Order;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
/**
 * Base class providing sensible defaults for all lifecycle methods.
 * New fields only need to define: get_id(), get_section(), get_user_meta_key(), get_label(), get_checkout_args().
 * get_id() is the order meta key. The checkout input name comes from get_checkout_key().
 */
abstract class AbstractCheckoutField
{
    protected ConfigInterface $config;
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }
    /**
     * Order meta key. Classic checkout posts a separate key from get_checkout_key().
     */
    abstract public function get_id(): string;
    /**
     * Checkout field array key and input name.
     */
    public function get_checkout_key(): string
    {
        return $this->get_user_meta_key();
    }
    /**
     * HTML id attribute, independent of the input name and the order meta key.
     */
    public function get_html_id(): string
    {
        return $this->get_checkout_key();
    }
    /**
     * Checkout section this field belongs to ('billing' or 'shipping').
     */
    abstract public function get_section(): string;
    /**
     * Meta key used to store this field's value on the user profile (wp_usermeta).
     */
    abstract public function get_user_meta_key(): string;
    /**
     * Human-readable label for this field.
     */
    abstract public function get_label(): string;
    /**
     * WooCommerce field definition array for the classic shortcode checkout form.
     */
    abstract public function get_checkout_args(): array;
    public function is_enabled(): bool
    {
        return \true;
    }
    /**
     * Checkout field id whose value is copied here when the customer does not enter a separate shipping address.
     *
     * Empty means this field keeps whatever was posted.
     */
    public function get_copied_from_key(): string
    {
        return '';
    }
    public function get_admin_order_args(WC_Order $order): array
    {
        return ['id' => $this->get_id(), 'label' => $this->get_label(), 'show' => \false, 'wrapper_class' => $this->get_id() . '_field', 'value' => (string) $order->get_meta($this->get_id())];
    }
    public function get_my_account_args(int $user_id): array
    {
        $saved = $user_id ? (string) get_user_meta($user_id, $this->get_user_meta_key(), \true) : '';
        return ['type' => 'text', 'label' => $this->get_label(), 'required' => \false, 'id' => $this->get_html_id(), 'class' => ['form-row-wide'], 'clear' => \true, 'value' => $saved, 'priority' => 31];
    }
    public function get_user_profile_args(): array
    {
        return ['label' => $this->get_label(), 'description' => ''];
    }
    public function get_address_format_tag(): string
    {
        return '{' . $this->get_id() . '}';
    }
    public function format_address_replacement(string $value): string
    {
        if (empty($value)) {
            return '';
        }
        return $this->get_label() . ': ' . $value;
    }
    /**
     * @param mixed $raw_value
     */
    public function sanitize_for_storage($raw_value): string
    {
        return sanitize_text_field((string) $raw_value);
    }
    protected function get_saved_user_value(): string
    {
        $user_id = get_current_user_id();
        if (!$user_id) {
            return '';
        }
        return (string) get_user_meta($user_id, $this->get_user_meta_key(), \true);
    }
}
