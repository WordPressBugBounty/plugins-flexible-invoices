<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Service\WooCommerceHelper;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Service\VatNumberValidator;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\Country;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\VatNumber;
use WC_Geolocation;
use WC_Order;
use WP_REST_Request;
/**
 * Handles OSS geolocation verification for B2C transactions.
 */
class OssGeolocationService
{
    private ConfigInterface $config;
    private WooCommerceHelper $woocommerce_helper;
    private VatNumberValidator $validator;
    private CheckoutConfirmations $checkout_confirmations;
    public function __construct(ConfigInterface $config, WooCommerceHelper $woocommerce_helper, VatNumberValidator $validator, CheckoutConfirmations $checkout_confirmations)
    {
        $this->config = $config;
        $this->woocommerce_helper = $woocommerce_helper;
        $this->validator = $validator;
        $this->checkout_confirmations = $checkout_confirmations;
    }
    public function hooks(): void
    {
        add_action('woocommerce_after_checkout_validation', [$this, 'validate_classic_checkout'], 10, 2);
        add_action('woocommerce_checkout_update_order_review', [$this, 'check_update_order_review'], 10, 1);
        add_action('woocommerce_store_api_checkout_update_order_from_request', [$this, 'validate_blocks_checkout'], 10, 2);
    }
    /**
     * @param array $data
     * @param \WP_Error $errors
     */
    public function validate_classic_checkout($data, $errors): void
    {
        if (!$this->config->should_validate_customer_ip()) {
            return;
        }
        if (!$this->woocommerce_helper->cart_has_oss_products()) {
            return;
        }
        $checkout_data = SanitizedCheckoutData::from_woocommerce_checkout($data, $this->config);
        $billing_country = $checkout_data->get_billing_country();
        if (!$this->validate_checkout_oss_location($checkout_data, $billing_country)) {
            $this->checkout_confirmations->set_show_location(\true);
            $errors->add('oss_location_mismatch', __('Your IP Address does not match your billing country. Please check the location confirmation box to proceed.', 'flexible-invoices'));
        }
    }
    public function check_update_order_review($posted_data): void
    {
        $checkout_data = SanitizedCheckoutData::from_url_encoded_string((string) $posted_data, $this->config);
        $this->validate_classic_checkout($checkout_data->get_data(), new \WP_Error());
    }
    /**
     * @param WC_Order $order
     * @param WP_REST_Request $request
     */
    public function validate_blocks_checkout(WC_Order $order, WP_REST_Request $request): void
    {
        if (!$this->config->should_validate_customer_ip()) {
            return;
        }
        if (!$this->woocommerce_helper->cart_has_oss_products()) {
            return;
        }
        $extensions = $request->get_param('extensions');
        $data = $extensions[$this->config->get_normalized_block_name()] ?? [];
        $checkout_data = SanitizedCheckoutData::from_array($data, $this->config);
        $billing_country = (string) $order->get_billing_country();
        if (!$this->validate_checkout_oss_location($checkout_data, $billing_country)) {
            throw new RouteException('oss_location_mismatch', __('Your IP Address does not match your billing country. Please check the location confirmation box to proceed.', 'flexible-invoices'), 400);
            // @phpstan-ignore class.notFound
        }
    }
    /**
     * Validates whether the user's IP matches their billing country for OSS rules.
     * Returns true if valid (or exempt), false if there is a mismatch requiring confirmation.
     */
    private function validate_checkout_oss_location(SanitizedCheckoutData $checkout_data, string $billing_country): bool
    {
        if ($this->is_b2b_transaction($checkout_data, $billing_country)) {
            return \true;
            // Skip if B2B
        }
        if (!$this->woocommerce_helper->is_eu_country($billing_country)) {
            return \true;
        }
        $location_confirmation = $checkout_data->has_location_confirmation();
        $ip_country = $this->get_ip_country();
        if (!empty($ip_country) && $ip_country !== $billing_country && !$location_confirmation) {
            return \false;
            // Mismatch and not confirmed
        }
        return \true;
    }
    private function get_ip_country(): string
    {
        $ip_address = WC_Geolocation::get_ip_address();
        $geo = WC_Geolocation::geolocate_ip($ip_address);
        return $geo['country'] ?? '';
    }
    private function is_b2b_transaction(SanitizedCheckoutData $checkout_data, string $country_code): bool
    {
        if (!$checkout_data->wants_invoice()) {
            return \false;
        }
        $billing_vat = $checkout_data->get_billing_tax_id();
        if (empty($billing_vat)) {
            return \false;
        }
        // Base country B2B doesn't require VIES validation for tax exemption
        $base_country = WC()->countries->get_base_country();
        if ($country_code === $base_country) {
            return \true;
        }
        try {
            $country = new Country($country_code);
            $vat_number = new VatNumber($billing_vat);
            $result = $this->validator->validate($vat_number, $country);
            return $result->is_valid();
        } catch (\InvalidArgumentException $e) {
            return \false;
        }
    }
}
