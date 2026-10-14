<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Service\VatNumberValidator;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\Country;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\VatNumber;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\ValidationResult;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\ViesApiFailurePolicy;
/**
 * Handles adding fields to the WooCommerce checkout.
 */
class CheckoutFields
{
    private ConfigInterface $config;
    private VatNumberValidator $validator;
    private CheckoutConfirmations $checkout_confirmations;
    private ViesApiFailurePolicy $vies_failure_policy;
    public function __construct(ConfigInterface $config, VatNumberValidator $validator, CheckoutConfirmations $checkout_confirmations, ViesApiFailurePolicy $vies_failure_policy)
    {
        $this->config = $config;
        $this->validator = $validator;
        $this->checkout_confirmations = $checkout_confirmations;
        $this->vies_failure_policy = $vies_failure_policy;
    }
    /**
     * Hooks into WordPress.
     */
    public function hooks(): void
    {
        add_filter('woocommerce_billing_fields', [$this, 'add_billing_fields'], 10000, 1);
        add_filter('woocommerce_shipping_fields', [$this, 'add_shipping_fields'], 10000, 1);
        add_filter('woocommerce_checkout_fields', [$this, 'add_checkout_fields'], 10, 1);
        add_filter('woocommerce_checkout_posted_data', [$this, 'copy_fields_for_same_shipping_address'], 9, 1);
        add_action('woocommerce_after_checkout_validation', [$this, 'validate_checkout_fields'], 10, 2);
        add_action('woocommerce_checkout_update_order_review', [$this, 'check_update_order_review'], 10, 1);
        add_filter('woocommerce_checkout_get_value', [$this, 'filter_checkout_get_value'], 10, 2);
    }
    /**
     * Filters checkout field value so checkbox gets '1' when saved as 'yes'.
     *
     * @param mixed  $value Current field value.
     * @param string $input Field key.
     * @return mixed
     */
    public function filter_checkout_get_value($value, $input)
    {
        if ($input === $this->config->get_want_invoice_user_key()) {
            // phpcs:disable WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
            if (isset($_POST[$input])) {
                return filter_var($_POST[$input], \FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
            // phpcs:enable
            $user_id = get_current_user_id();
            if ($user_id) {
                $saved = get_user_meta($user_id, $this->config->get_want_invoice_user_key(), \true);
                if (filter_var($saved, \FILTER_VALIDATE_BOOLEAN)) {
                    return 1;
                }
                return 0;
            }
        }
        return $value;
    }
    /**
     * Adds billing fields through WC_Countries::get_address_fields().
     */
    public function add_billing_fields(array $fields): array
    {
        return $this->add_address_fields($fields, 'billing');
    }
    /**
     * Adds shipping fields through WC_Countries::get_address_fields().
     */
    public function add_shipping_fields(array $fields): array
    {
        return $this->add_address_fields($fields, 'shipping');
    }
    /**
     * Adds checkout fields.
     */
    public function add_checkout_fields(array $fields): array
    {
        foreach ($this->config->get_checkout_fields() as $field) {
            if (!$field->is_enabled()) {
                continue;
            }
            $section = $field->get_section();
            // Billing and shipping are already added on the address filters, so they are not repeated here.
            if ('billing' === $section || 'shipping' === $section) {
                continue;
            }
            if (!isset($fields[$section]) || !is_array($fields[$section])) {
                $fields[$section] = [];
            }
            $this->assign_field($fields[$section], $field);
        }
        return $fields;
    }
    private function add_address_fields(array $fields, string $section): array
    {
        foreach ($this->config->get_checkout_fields() as $field) {
            if (!$field->is_enabled() || $field->get_section() !== $section) {
                continue;
            }
            $this->assign_field($fields, $field);
        }
        return $fields;
    }
    private function assign_field(array &$fields, Field\AbstractCheckoutField $field): void
    {
        $key = $field->get_checkout_key();
        // Skip already defined field.
        if (isset($fields[$key])) {
            return;
        }
        $args = $field->get_checkout_args();
        // WooCommerce validates required fields before it knows the invoice checkbox is off.
        $is_vat_field = $field->get_id() === $this->config->get_billing_tax_id_key() || $field->get_id() === $this->config->get_shipping_tax_id_key();
        if ($is_vat_field && ConfigInterface::BEHAVIOR_ASK_USER === $this->config->get_field_behavior() && !empty($args['required'])) {
            $args['required'] = \false;
            $args['class'] = isset($args['class']) && is_array($args['class']) ? $args['class'] : [];
            $args['class'][] = 'wpd-invoices-required-with-invoice';
        }
        $fields[$key] = $args;
    }
    /**
     * Replaces shipping fields that declare a source key with the billing value, including an empty one.
     *
     * WooCommerce mirrors shipping_{suffix} from billing_{suffix}. Fields that declare another source,
     * or a legacy order-meta key with a leading underscore, are copied here.
     * An earlier receiver value must not survive once the address is shared.
     *
     * @param mixed $data Posted checkout data.
     * @return mixed
     */
    public function copy_fields_for_same_shipping_address($data)
    {
        if (!is_array($data) || !empty($data['ship_to_different_address'])) {
            return $data;
        }
        foreach ($this->config->get_checkout_fields() as $field) {
            if (!$field->is_enabled()) {
                continue;
            }
            $source_key = $this->resolve_posted_key($field->get_copied_from_key(), $data);
            if (null === $source_key) {
                continue;
            }
            $data[$field->get_checkout_key()] = $data[$source_key];
        }
        return $data;
    }
    /**
     * Validates VAT number / NIP fields during classic checkout.
     *
     * @param array     $data   Posted checkout data.
     * @param \WP_Error $errors Validation errors collector.
     */
    public function validate_checkout_fields($data, $errors): void
    {
        $checkout_data = SanitizedCheckoutData::from_woocommerce_checkout($data, $this->config);
        $wants_invoice = $checkout_data->wants_invoice();
        if (!$wants_invoice) {
            return;
        }
        $this->validate_billing_vat_field($checkout_data, $errors);
        $this->validate_shipping_vat_field($checkout_data, $errors);
    }
    private function validate_billing_vat_field(SanitizedCheckoutData $checkout_data, $errors): void
    {
        if (!$this->is_tax_id_field_enabled($this->config->get_billing_tax_id_key())) {
            return;
        }
        $billing_vat = $checkout_data->get_billing_tax_id();
        $country = $this->country_for_vat($checkout_data->get_billing_country(), $billing_vat, $errors, 'billing_tax_id_invalid');
        if (!$country instanceof Country && '' !== trim($billing_vat)) {
            return;
        }
        $vat_number = new VatNumber($billing_vat);
        $result = $this->validator->validate($vat_number, $country);
        if (!$result->is_valid()) {
            $this->handle_validation_result($result, $checkout_data, $errors, __('Billing VAT Number', 'flexible-invoices'), $this->config->get_billing_tax_id_key());
            return;
        }
        $this->reject_empty_required_vat($billing_vat, $this->config->get_billing_tax_id_key(), $errors, __('Billing VAT Number', 'flexible-invoices'));
        //todo: refactor validators to accept orders and move it there.
    }
    private function validate_shipping_vat_field(SanitizedCheckoutData $checkout_data, $errors): void
    {
        if (!$this->config->should_show_shipping_tax_id_field()) {
            return;
        }
        if (!$this->cart_needs_shipping()) {
            return;
        }
        if (empty($checkout_data->get_data()['ship_to_different_address'])) {
            return;
        }
        $shipping_vat = $checkout_data->get_shipping_tax_id();
        $country = $this->country_for_vat($checkout_data->get_shipping_country(), $shipping_vat, $errors, 'shipping_tax_id_invalid');
        if (!$country instanceof Country && '' !== trim($shipping_vat)) {
            return;
        }
        $vat_number = new VatNumber($shipping_vat);
        $result = $this->validator->validate($vat_number, $country);
        if (!$result->is_valid()) {
            $this->handle_validation_result($result, $checkout_data, $errors, __('Shipping VAT Number', 'flexible-invoices'), $this->config->get_shipping_tax_id_key());
            return;
        }
        $this->reject_empty_required_vat($shipping_vat, $this->config->get_shipping_tax_id_key(), $errors, __('Shipping VAT Number', 'flexible-invoices'));
        //todo: refactor validators to accept orders and move it there.
    }
    /**
     * An empty number does not need a country, so a missing code returns null and the caller still validates it.
     * A missing code with a non-empty number records an error and returns null; the caller stops.
     */
    private function country_for_vat($country_code, $vat, $errors, $error_code): ?Country
    {
        try {
            return new Country((string) $country_code);
        } catch (\InvalidArgumentException $ie) {
            if ('' === trim((string) $vat)) {
                return null;
            }
            $message = 'shipping_tax_id_invalid' === $error_code ? __('Shipping country is required for VAT validation.', 'flexible-invoices') : __('Billing country is required for VAT validation.', 'flexible-invoices');
            $this->add_validation_error($errors, $error_code, $message);
            return null;
        }
    }
    /**
     * Validates checkout fields during AJAX order review.
     *
     * @param string $posted_data URL-encoded checkout post data.
     */
    public function check_update_order_review($posted_data): void
    {
        $checkout_data = SanitizedCheckoutData::from_url_encoded_string((string) $posted_data, $this->config);
        $this->validate_checkout_fields($checkout_data->get_data(), new \WP_Error());
    }
    /**
     * Format and VIES validators accept an empty number. The field's own required flag still rejects it.
     */
    private function reject_empty_required_vat(string $vat, string $field_id, $errors, string $label): void
    {
        //todo: refactor validators to accept orders and move it there.
        if ('' !== trim($vat) || !$this->checkout_field_is_required($field_id) || $this->woo_reports_empty_vat($field_id)) {
            return;
        }
        $this->add_validation_error($errors, ValidationResult::ERROR_VAT_REQUIRED, sprintf(
            /* translators: %s: VAT field name. */
            __('%s is a required field.', 'flexible-invoices'),
            $label
        ));
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
        //todo: refactor validators to accept orders and move it there.
        foreach ($this->config->get_checkout_fields() as $field) {
            if ($field->get_id() !== $field_id) {
                continue;
            }
            return !empty($field->get_checkout_args()['required']);
        }
        return \false;
    }
    /**
     * Outside the invoice checkbox, WooCommerce still has required set and already reports an empty VAT field.
     */
    private function woo_reports_empty_vat(string $field_id): bool
    {
        return ConfigInterface::BEHAVIOR_ASK_USER !== $this->config->get_field_behavior() && $this->checkout_field_is_required($field_id);
    }
    private function handle_validation_result(ValidationResult $result, SanitizedCheckoutData $checkout_data, $errors, string $required_field_label, string $field_id): void
    {
        if ($this->vies_failure_policy->should_show_vat_confirmation($result)) {
            $this->checkout_confirmations->set_show_vat(\true);
        }
        if (ValidationResult::ERROR_VAT_REQUIRED === $result->get_error_code() && $this->woo_reports_empty_vat($field_id)) {
            return;
        }
        if ($this->vies_failure_policy->should_block_checkout($result, $checkout_data->has_vat_confirmation())) {
            $message = (string) $result->get_error_message();
            if (ValidationResult::ERROR_VAT_REQUIRED === $result->get_error_code()) {
                $message = sprintf(
                    /* translators: %s: VAT field name. */
                    __('%s is a required field.', 'flexible-invoices'),
                    $required_field_label
                );
            }
            $this->add_validation_error($errors, (string) $result->get_error_code(), $message);
        }
    }
    private function add_validation_error($errors, string $code, string $message): void
    {
        if ($errors instanceof \WP_Error) {
            $errors->add($code, $message);
        } else {
            wc_add_notice($message, 'error');
        }
    }
    private function cart_needs_shipping(): bool
    {
        $cart = WC()->cart;
        return $cart && $cart->needs_shipping();
    }
    /**
     * Posted checkout data uses the form key. Plugins may still point at the order meta key.
     *
     * @param string $key  Declared source key.
     * @param array  $data Posted data.
     * @return string|null
     */
    private function resolve_posted_key($key, array $data)
    {
        if (!is_string($key) || '' === $key) {
            return null;
        }
        if (array_key_exists($key, $data)) {
            return $key;
        }
        if ('_' === $key[0]) {
            $legacy = substr($key, 1);
            if (array_key_exists($legacy, $data)) {
                return $legacy;
            }
        }
        return null;
    }
}
