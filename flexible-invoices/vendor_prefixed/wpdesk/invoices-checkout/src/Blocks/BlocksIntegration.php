<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Blocks;

use InvalidArgumentException;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\InvoicesCheckout;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout\SanitizedCheckoutData;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\I18n\ScriptTranslations;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\LibraryInfo;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Service\VatNumberValidator;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Service\OrderInvoiceDataSaver;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Service\UserInvoiceDataSaver;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\Country;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\InvoiceData;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\VatNumber;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\ViesApiFailurePolicy;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Service\WooCommerceHelper;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Service\ViesOrderMetaWriter;
use WC_Order;
use WP_REST_Request;
use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
/**
 * Handles WooCommerce Blocks Checkout integration.
 */
class BlocksIntegration
{
    private ConfigInterface $config;
    private LibraryInfo $library_info;
    private VatNumberValidator $validator;
    private OrderInvoiceDataSaver $order_saver;
    private UserInvoiceDataSaver $user_saver;
    private WooCommerceHelper $woocommerce_helper;
    private ScriptTranslations $script_translations;
    private ViesApiFailurePolicy $vies_failure_policy;
    private ViesOrderMetaWriter $vies_meta_writer;
    public function __construct(ConfigInterface $config, LibraryInfo $library_info, VatNumberValidator $validator, OrderInvoiceDataSaver $order_saver, UserInvoiceDataSaver $user_saver, WooCommerceHelper $woocommerce_helper, ScriptTranslations $script_translations, ViesApiFailurePolicy $vies_failure_policy, ViesOrderMetaWriter $vies_meta_writer)
    {
        $this->config = $config;
        $this->library_info = $library_info;
        $this->validator = $validator;
        $this->order_saver = $order_saver;
        $this->user_saver = $user_saver;
        $this->woocommerce_helper = $woocommerce_helper;
        $this->script_translations = $script_translations;
        $this->vies_failure_policy = $vies_failure_policy;
        $this->vies_meta_writer = $vies_meta_writer;
    }
    /**
     * Hooks into WordPress.
     */
    public function hooks(): void
    {
        add_action('init', [$this, 'register_block']);
        add_action('woocommerce_blocks_checkout_block_registration', [$this, 'register_block_integration']);
        add_action('woocommerce_blocks_loaded', [$this, 'register_store_api_endpoint_data']);
        add_action('init', [$this, 'register_store_api_endpoint_data']);
        add_action('woocommerce_store_api_checkout_update_order_from_request', [$this, 'save_blocks_data_to_order'], 10, 2);
        add_action('woocommerce_blocks_loaded', [$this, 'register_store_api_update_callbacks']);
        add_action('init', [$this, 'register_store_api_update_callbacks']);
    }
    /**
     * Registers update callbacks with WooCommerce Store API.
     */
    public function register_store_api_update_callbacks(): void
    {
        woocommerce_store_api_register_update_callback(
            // @phpstan-ignore function.notFound
            ['namespace' => $this->config->get_normalized_block_name(), 'callback' => [$this, 'validate_store_api_update']]
        );
    }
    /**
     * Registers the block type on the server to make it available in Gutenberg.
     */
    public function register_block(): void
    {
        $block_json_path = dirname(__DIR__, 2) . '/assets-src/block.json';
        if (!file_exists($block_json_path)) {
            return;
        }
        add_filter('block_type_metadata', [$this, 'filter_block_metadata']);
        register_block_type($block_json_path, ['name' => $this->config->get_block_name()]);
        remove_filter('block_type_metadata', [$this, 'filter_block_metadata']);
    }
    /**
     * Filters the block type metadata to inject a dynamic block name.
     *
     * @param array $metadata Block metadata from block.json.
     * @return array
     */
    public function filter_block_metadata(array $metadata): array
    {
        if (isset($metadata['name']) && 'woocommerce/invoices-checkout' === $metadata['name']) {
            $metadata['name'] = $this->config->get_block_name();
        }
        return $metadata;
    }
    /**
     * Registers endpoint data schema with WooCommerce Store API.
     */
    public function register_store_api_endpoint_data(): void
    {
        $namespace = $this->config->get_normalized_block_name();
        $schema_callback = function () {
            return [$this->config->get_want_invoice_key() => ['description' => __('I want an invoice', 'flexible-invoices'), 'type' => ['string', 'null'], 'context' => ['view', 'edit'], 'readonly' => \false], $this->config->get_billing_tax_id_key() => ['description' => __('Billing VAT Number', 'flexible-invoices'), 'type' => ['string', 'null'], 'context' => ['view', 'edit'], 'readonly' => \false], $this->config->get_shipping_tax_id_key() => ['description' => __('Shipping VAT Number', 'flexible-invoices'), 'type' => ['string', 'null'], 'context' => ['view', 'edit'], 'readonly' => \false], $this->config->get_billing_company_key() => ['description' => __('Billing Company', 'flexible-invoices'), 'type' => ['string', 'null'], 'context' => ['view', 'edit'], 'readonly' => \false], $this->config->get_shipping_company_key() => ['description' => __('Shipping Company', 'flexible-invoices'), 'type' => ['string', 'null'], 'context' => ['view', 'edit'], 'readonly' => \false], $this->config->get_vat_confirmation_key() => ['description' => __('VAT Confirmation', 'flexible-invoices'), 'type' => ['boolean', 'null'], 'context' => ['view', 'edit'], 'readonly' => \false], $this->config->get_location_confirmation_key() => ['description' => __('Location Confirmation', 'flexible-invoices'), 'type' => ['boolean', 'null'], 'context' => ['view', 'edit'], 'readonly' => \false]];
        };
        $data_callback = function () {
            return [];
        };
        woocommerce_store_api_register_endpoint_data(['endpoint' => 'checkout', 'namespace' => $namespace, 'schema_callback' => $schema_callback, 'data_callback' => $data_callback, 'schema_type' => \ARRAY_A]);
    }
    /**
     * Callback triggered when Store API updates extension data.
     *
     * @param array $data Extension payload from the checkout block.
     */
    public function validate_store_api_update($data): void
    {
        if ($data instanceof \stdClass) {
            $data = (array) $data;
        }
        if (!is_array($data)) {
            return;
        }
        if ($this->customer_uses_shared_shipping_address()) {
            $data = $this->copy_billing_values_for_shared_shipping_address($data);
        }
        WC()->session->set(InvoicesCheckout::SESSION_KEY_BLOCKS_DATA, $data);
        $checkout_data = SanitizedCheckoutData::from_array($data, $this->config);
        $wants_invoice = $this->resolve_wants_invoice_and_validate($checkout_data);
        if (!$wants_invoice) {
            return;
        }
        if ($this->is_tax_id_field_enabled($this->config->get_billing_tax_id_key())) {
            $billing_vat = $checkout_data->get_billing_tax_id();
            $this->do_validate_vat($billing_vat, $this->get_customer_billing_country(), $checkout_data);
        }
        if ($this->should_validate_receiver_vat(!$this->customer_uses_shared_shipping_address(), $checkout_data)) {
            $shipping_vat = $checkout_data->get_shipping_tax_id();
            $this->do_validate_vat($shipping_vat, $this->get_customer_shipping_country(), $checkout_data);
        }
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
    private function do_validate_vat(string $vat, string $country_code, SanitizedCheckoutData $checkout_data): void
    {
        try {
            $country = new Country($country_code);
        } catch (InvalidArgumentException $ie) {
            if ('' !== trim($vat)) {
                return;
            }
            $country = null;
        }
        $vat_number = new VatNumber($vat);
        $result = $this->validator->validate($vat_number, $country);
        if ($this->vies_failure_policy->should_block_checkout($result, $checkout_data->has_vat_confirmation())) {
            throw new RouteException((string) $result->get_error_code(), (string) $result->get_error_message(), 400);
        }
    }
    /**
     * Registers integration with WooCommerce Blocks registry.
     *
     * @param object $integration_registry WooCommerce Blocks integration registry.
     */
    public function register_block_integration($integration_registry): void
    {
        $integration_registry->register(new BlockIntegrationAdapter($this->config, $this->library_info, $this->woocommerce_helper, $this->script_translations));
    }
    /**
     * Saves data submitted via the Store API checkout to order meta and user meta.
     *
     * @param WC_Order             $order   Order being updated.
     * @param WP_REST_Request|null $request Store API request when available.
     */
    public function save_blocks_data_to_order(WC_Order $order, ?WP_REST_Request $request = null): void
    {
        if (!$request instanceof WP_REST_Request) {
            return;
        }
        $data = $this->extract_blocks_data($request->get_param('extensions'));
        if ([] === $data) {
            return;
        }
        $shared_shipping_address = $this->request_uses_shared_shipping_address($request);
        if ($shared_shipping_address) {
            $data = $this->copy_billing_values_for_shared_shipping_address($data);
        }
        $checkout_data = SanitizedCheckoutData::from_array($data, $this->config);
        $wants_invoice = $this->resolve_wants_invoice_and_validate($checkout_data);
        if ($wants_invoice) {
            $this->validate_order_vats($checkout_data, $shared_shipping_address);
        }
        $invoice_data = InvoiceData::from_sanitized_checkout_data($checkout_data);
        if ($wants_invoice !== $checkout_data->wants_invoice()) {
            $invoice_data = $invoice_data->with_wants_invoice($wants_invoice);
        }
        $this->order_saver->save_to_order_and_apply($order, $invoice_data);
        $this->vies_meta_writer->write($order, $checkout_data, (string) $order->get_billing_country());
        $order->save();
        $this->user_saver->sync_order_to_user($order);
    }
    private function validate_order_vats(SanitizedCheckoutData $checkout_data, bool $shared_shipping_address): void
    {
        if ($this->is_tax_id_field_enabled($this->config->get_billing_tax_id_key())) {
            $billing_vat = $checkout_data->get_billing_tax_id();
            $this->do_validate_vat($billing_vat, $this->get_customer_billing_country(), $checkout_data);
        }
        if (!$this->should_validate_receiver_vat(!$shared_shipping_address, $checkout_data)) {
            return;
        }
        $shipping_vat = $checkout_data->get_shipping_tax_id();
        $this->do_validate_vat($shipping_vat, $this->get_customer_shipping_country(), $checkout_data);
    }
    /**
     * Receiver VAT is checked when the shipping address is separate, and also when a shared address still carries a different non-empty shipping VAT.
     *
     * The extension payload cannot decide whether the addresses are shared. A client can send a distinct shipping_address and still claim the addresses are shared.
     */
    private function should_validate_receiver_vat(bool $shipping_address_is_separate, SanitizedCheckoutData $checkout_data): bool
    {
        if (!$this->config->should_show_shipping_tax_id_field()) {
            return \false;
        }
        if ($shipping_address_is_separate) {
            return \true;
        }
        $shipping_vat = $checkout_data->get_shipping_tax_id();
        return '' !== $shipping_vat && $shipping_vat !== $checkout_data->get_billing_tax_id();
    }
    /**
     * Place-order compares the checkout request, which is what WooCommerce stores on the order.
     */
    private function request_uses_shared_shipping_address(WP_REST_Request $request): bool
    {
        if (!$this->cart_needs_shipping_address()) {
            return \true;
        }
        $shipping_address = $request->get_param('shipping_address');
        if (null === $shipping_address) {
            return \true;
        }
        return $this->addresses_match($this->address_to_array($request->get_param('billing_address')), $this->address_to_array($shipping_address));
    }
    /**
     * Cart updates have no checkout body yet. An empty shipping address is not a declared receiver.
     */
    private function customer_uses_shared_shipping_address(): bool
    {
        $customer = WC()->customer;
        if (!$customer || !$this->cart_needs_shipping_address()) {
            return \true;
        }
        $shipping_address = ['first_name' => (string) $customer->get_shipping_first_name(), 'last_name' => (string) $customer->get_shipping_last_name(), 'company' => (string) $customer->get_shipping_company(), 'address_1' => (string) $customer->get_shipping_address_1(), 'address_2' => (string) $customer->get_shipping_address_2(), 'city' => (string) $customer->get_shipping_city(), 'state' => (string) $customer->get_shipping_state(), 'postcode' => (string) $customer->get_shipping_postcode(), 'country' => (string) $customer->get_shipping_country(), 'phone' => (string) $customer->get_shipping_phone()];
        if ('' === $this->normalize_address_value($shipping_address['address_1']) && '' === $this->normalize_address_value($shipping_address['first_name'])) {
            return \true;
        }
        return $this->addresses_match(['first_name' => (string) $customer->get_billing_first_name(), 'last_name' => (string) $customer->get_billing_last_name(), 'company' => (string) $customer->get_billing_company(), 'address_1' => (string) $customer->get_billing_address_1(), 'address_2' => (string) $customer->get_billing_address_2(), 'city' => (string) $customer->get_billing_city(), 'state' => (string) $customer->get_billing_state(), 'postcode' => (string) $customer->get_billing_postcode(), 'country' => (string) $customer->get_billing_country(), 'phone' => (string) $customer->get_billing_phone()], $shipping_address);
    }
    private function cart_needs_shipping_address(): bool
    {
        $cart = WC()->cart;
        return $cart && $cart->needs_shipping_address();
    }
    /**
     * @param mixed $address
     */
    private function address_to_array($address): array
    {
        if ($address instanceof \stdClass) {
            $address = (array) $address;
        }
        return is_array($address) ? $address : [];
    }
    /**
     * Store API address contract, including the person, so the same street with another recipient stays separate.
     *
     * @param array<string, mixed> $billing_address
     * @param array<string, mixed> $shipping_address
     */
    private function addresses_match(array $billing_address, array $shipping_address): bool
    {
        $keys = ['first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'state', 'postcode', 'country', 'phone'];
        foreach ($keys as $key) {
            $billing_value = $this->normalize_address_value((string) ($billing_address[$key] ?? ''));
            $shipping_value = $this->normalize_address_value((string) ($shipping_address[$key] ?? ''));
            if ($billing_value !== $shipping_value) {
                return \false;
            }
        }
        return \true;
    }
    private function normalize_address_value(string $value): string
    {
        return strtolower(trim((string) preg_replace('/\s+/', ' ', $value)));
    }
    /**
     * Fills an empty shipping VAT or company from billing when the Store API addresses match.
     *
     * A non-empty shipping VAT stays only while the receiver field is enabled, because that is the value the customer typed.
     * A disabled receiver field still stores the billing VAT, including when the client posted a different one.
     * A non-empty shipping company stays. A missing billing key still clears an empty shipping value.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function copy_billing_values_for_shared_shipping_address(array $data): array
    {
        $pairs = [[$this->config->get_billing_tax_id_key(), $this->config->get_shipping_tax_id_key()]];
        if ($this->config->should_show_company_field()) {
            $pairs[] = [$this->config->get_billing_company_key(), $this->config->get_shipping_company_key()];
        }
        $shipping_tax_id_key = $this->config->get_shipping_tax_id_key();
        foreach ($pairs as $pair) {
            $shipping_value = isset($data[$pair[1]]) ? trim((string) $data[$pair[1]]) : '';
            $keep_non_empty = $pair[1] !== $shipping_tax_id_key || $this->config->should_show_shipping_tax_id_field();
            if ($keep_non_empty && '' !== $shipping_value) {
                continue;
            }
            $data[$pair[1]] = array_key_exists($pair[0], $data) ? $data[$pair[0]] : '';
        }
        return $data;
    }
    /**
     * Extracts invoice data from extensions block payload.
     * Note: Key checks like 'want_invoice', 'billing_tax_id', and 'billing_company'
     * match the internal Store API schema payload keys.
     */
    private function extract_blocks_data($extensions): array
    {
        if ($extensions instanceof \stdClass) {
            $extensions = (array) $extensions;
        }
        if (!is_array($extensions)) {
            return [];
        }
        $data = $extensions[$this->config->get_normalized_block_name()] ?? [];
        if ($data instanceof \stdClass) {
            $data = (array) $data;
        }
        return is_array($data) ? $data : [];
    }
    /**
     * Helper method to extract intentional 'want_invoice' and validate required VAT.
     */
    private function resolve_wants_invoice_and_validate(SanitizedCheckoutData $checkout_data): bool
    {
        $wants_invoice = $checkout_data->wants_invoice();
        $behavior = $this->config->get_field_behavior();
        $billing_vat = $checkout_data->get_billing_tax_id();
        if (ConfigInterface::BEHAVIOR_ALWAYS_SHOW === $behavior && !empty($billing_vat)) {
            $wants_invoice = \true;
        }
        return $wants_invoice;
    }
    private function get_customer_billing_country(): string
    {
        return WC()->customer ? (string) WC()->customer->get_billing_country() : '';
    }
    private function get_customer_shipping_country(): string
    {
        return WC()->customer ? (string) WC()->customer->get_shipping_country() ?: WC()->customer->get_billing_country() : '';
    }
}
