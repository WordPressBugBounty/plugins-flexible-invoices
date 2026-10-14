<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\InvoicesCheckout;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Service\VatNumberValidator;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\Country;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\VatNumber;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Service\WooCommerceHelper;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\ViesApiFailurePolicy;
use WC_Order;
use WP_REST_Request;
/**
 * Handles dynamic tax exemption based on buyer country and VAT validity.
 */
class TaxExemptionManager
{
    private ConfigInterface $config;
    private VatNumberValidator $validator;
    private WooCommerceHelper $woocommerce_helper;
    private ViesApiFailurePolicy $vies_failure_policy;
    public function __construct(ConfigInterface $config, VatNumberValidator $validator, WooCommerceHelper $woocommerce_helper, ViesApiFailurePolicy $vies_failure_policy)
    {
        $this->vies_failure_policy = $vies_failure_policy;
        $this->config = $config;
        $this->validator = $validator;
        $this->woocommerce_helper = $woocommerce_helper;
    }
    public function hooks(): void
    {
        add_action('woocommerce_checkout_update_order_review', [$this, 'update_tax_exemption_classic'], 10, 1);
        add_action('woocommerce_store_api_checkout_update_order_from_request', [$this, 'update_tax_exemption_blocks'], 10, 2);
        add_action('woocommerce_before_calculate_totals', [$this, 'update_tax_exemption_blocks_cart'], 10, 1);
        add_action('woocommerce_store_api_cart_update_customer_from_request', [$this, 'update_tax_exemption_blocks_customer_update'], 10, 1);
    }
    /**
     * Handles tax exemption for Classic Checkout.
     *
     * @param string $posted_data
     */
    public function update_tax_exemption_classic($posted_data): void
    {
        if (!is_string($posted_data)) {
            return;
        }
        $checkout_data = SanitizedCheckoutData::from_url_encoded_string($posted_data, $this->config);
        $posted = $checkout_data->get_data();
        $wants_invoice = $checkout_data->wants_invoice();
        $ships_to_different = !empty($posted['ship_to_different_address']);
        $billing_country = $checkout_data->get_billing_country();
        $shipping_country = $ships_to_different ? $checkout_data->get_shipping_country() : $billing_country;
        $location = $this->location_for_exemption($billing_country, $wants_invoice ? $checkout_data->get_billing_tax_id() : '', $shipping_country, $wants_invoice ? $checkout_data->get_shipping_tax_id() : '', $ships_to_different);
        $this->apply_tax_exemption($location['country'], $location['vat'], null, $checkout_data->has_vat_confirmation());
    }
    /**
     * Handles tax exemption for WooCommerce Blocks Checkout.
     *
     * @param WC_Order        $order   Order being updated.
     * @param WP_REST_Request $request Store API request.
     */
    public function update_tax_exemption_blocks($order, $request): void
    {
        $extensions = $request->get_param('extensions');
        if (empty($extensions) || !is_array($extensions)) {
            return;
        }
        $data = $extensions[$this->config->get_normalized_block_name()] ?? null;
        if (!is_array($data)) {
            return;
        }
        $checkout_data = SanitizedCheckoutData::from_array($data, $this->config);
        $wants_invoice = $checkout_data->wants_invoice();
        $billing_country = (string) $order->get_billing_country();
        $shipping_country = (string) $order->get_shipping_country();
        $billing_vat = $wants_invoice ? $checkout_data->get_billing_tax_id() : '';
        $shipping_vat = $wants_invoice ? $checkout_data->get_shipping_tax_id() : '';
        $location = $this->location_for_exemption($billing_country, $billing_vat, $shipping_country, $shipping_vat, $this->ships_to_different_address($billing_country, $shipping_country, $billing_vat, $shipping_vat));
        $this->apply_tax_exemption($location['country'], $location['vat'], null, $checkout_data->has_vat_confirmation());
    }
    /**
     * Chooses the country and VAT number for the WooCommerce tax address.
     *
     * A separate shipping address uses only the shipping VAT number, including when the country matches billing.
     * An empty number does not inherit the billing number.
     * Shop base address uses the shop country. A VAT number counts only when billing is in that country.
     *
     * @param bool $ships_to_different_address Whether the customer entered a separate shipping address.
     *
     * @return array{country: string, vat: string}
     */
    private function location_for_exemption(string $billing_country, string $billing_vat, string $shipping_country, string $shipping_vat, bool $ships_to_different_address): array
    {
        $tax_based_on = (string) get_option('woocommerce_tax_based_on');
        if ('base' === $tax_based_on) {
            $base_country = (string) WC()->countries->get_base_country();
            return ['country' => $base_country, 'vat' => $billing_country === $base_country ? $billing_vat : ''];
        }
        if ('shipping' === $tax_based_on && $ships_to_different_address) {
            return ['country' => '' !== $shipping_country ? $shipping_country : $billing_country, 'vat' => $shipping_vat];
        }
        return ['country' => $billing_country, 'vat' => $billing_vat];
    }
    /**
     * Blocks has no checkbox. A different country or a different non-empty shipping VAT is a separate address.
     */
    private function ships_to_different_address(string $billing_country, string $shipping_country, string $billing_vat, string $shipping_vat): bool
    {
        if ('' !== $shipping_country && $shipping_country !== $billing_country) {
            return \true;
        }
        return '' !== $shipping_vat && $shipping_vat !== $billing_vat;
    }
    /**
     * Handles live tax calculation in WooCommerce Blocks cart updates.
     *
     * @param \WC_Cart          $cart     Cart being calculated.
     * @param \WC_Customer|null $customer Customer when provided.
     */
    public function update_tax_exemption_blocks_cart($cart, $customer = null): void
    {
        if (!$customer) {
            $customer = WC()->customer;
        }
        // Only apply this in REST API context (Store API) to avoid side effects in classic checkout
        if (!defined('REST_REQUEST') || !\REST_REQUEST) {
            return;
        }
        $data = WC()->session->get(InvoicesCheckout::SESSION_KEY_BLOCKS_DATA);
        $has_vat_confirmation = \false;
        if (!is_array($data)) {
            // Fallback to customer metadata if session is empty.
            $customer_id = get_current_user_id();
            $billing_vat = (string) get_user_meta($customer_id, $this->config->get_billing_tax_id_user_key(), \true);
            $shipping_vat = (string) get_user_meta($customer_id, $this->config->get_shipping_tax_id_user_key(), \true);
        } else {
            $checkout_data = SanitizedCheckoutData::from_array($data, $this->config);
            $wants_invoice = $checkout_data->wants_invoice();
            $billing_vat = $wants_invoice ? $checkout_data->get_billing_tax_id() : '';
            $shipping_vat = $wants_invoice ? $checkout_data->get_shipping_tax_id() : '';
            $has_vat_confirmation = $checkout_data->has_vat_confirmation();
        }
        $billing_country = (string) $customer->get_billing_country();
        $shipping_country = (string) $customer->get_shipping_country();
        $location = $this->location_for_exemption($billing_country, $billing_vat, $shipping_country, $shipping_vat, $this->ships_to_different_address($billing_country, $shipping_country, $billing_vat, $shipping_vat));
        $this->apply_tax_exemption($location['country'], $location['vat'], $customer, $has_vat_confirmation);
    }
    /**
     * Handles live tax calculation in WooCommerce Blocks when the customer changes their country.
     *
     * @param \WC_Customer $customer Customer being updated.
     */
    public function update_tax_exemption_blocks_customer_update($customer): void
    {
        $cart = isset(WC()->cart) ? WC()->cart : null;
        // @phpstan-ignore isset.property
        $this->update_tax_exemption_blocks_cart($cart, $customer);
    }
    /**
     * Applies or removes VAT exemption based on country and VAT validity.
     *
     * @param string            $country_code         Country WooCommerce uses for tax.
     * @param string            $vat                  VAT number for that country.
     * @param \WC_Customer|null $customer             Customer to update.
     * @param bool              $has_vat_confirmation Whether the customer confirmed the VAT number.
     */
    public function apply_tax_exemption(string $country_code, string $vat, $customer = null, bool $has_vat_confirmation = \false): void
    {
        if (!$customer) {
            $customer = WC()->customer;
        }
        if (!$customer) {
            return;
        }
        if (!$this->config->should_apply_tax_exemption()) {
            $customer->set_is_vat_exempt(\false);
            return;
        }
        if ($this->config->should_gate_tax_exemption_on_oss_products() && !$this->woocommerce_helper->cart_has_oss_products()) {
            $customer->set_is_vat_exempt(\false);
            return;
        }
        if (empty($country_code)) {
            return;
        }
        $base_country = WC()->countries->get_base_country();
        $is_buyer_eu = $this->woocommerce_helper->is_eu_country($country_code);
        if ($country_code === $base_country) {
            $is_exempt = !empty($vat) && $this->config->should_remove_vat_from_base_b2b();
        } elseif (!$is_buyer_eu) {
            $is_exempt = $this->config->should_exempt_non_eu_customers();
        } elseif (empty($vat)) {
            $is_exempt = \false;
        } else {
            try {
                $country = new Country($country_code);
                $vat_number = new VatNumber($vat);
                $result = $this->validator->validate($vat_number, $country);
                $is_exempt = $this->vies_failure_policy->should_exempt_vat($result, $has_vat_confirmation);
            } catch (\InvalidArgumentException $e) {
                $is_exempt = \false;
            }
        }
        $customer->set_is_vat_exempt($is_exempt);
    }
}
