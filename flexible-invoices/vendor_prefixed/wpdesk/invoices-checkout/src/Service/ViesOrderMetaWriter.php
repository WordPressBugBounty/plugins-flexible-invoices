<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Service;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout\SanitizedCheckoutData;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\ValidationResult;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\Country;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\VatNumber;
use WC_Geolocation;
use WC_Order;
/**
 * Persists VIES and OSS geolocation results onto the order.
 */
class ViesOrderMetaWriter
{
    private ConfigInterface $config;
    private VatNumberValidator $validator;
    public function __construct(ConfigInterface $config, VatNumberValidator $validator)
    {
        $this->config = $config;
        $this->validator = $validator;
    }
    /**
     * Writes VIES validation and IP-country meta for a checkout submission.
     */
    public function write(WC_Order $order, SanitizedCheckoutData $checkout_data, string $billing_country = ''): void
    {
        $country_code = '' !== $checkout_data->get_billing_country() ? $checkout_data->get_billing_country() : $billing_country;
        $this->write_ip_country($order, $checkout_data);
        $this->write_vat_validation($order, $checkout_data, $country_code);
    }
    private function write_ip_country(WC_Order $order, SanitizedCheckoutData $checkout_data): void
    {
        $order->update_meta_data($this->config->get_ip_country_key(), $this->get_ip_country());
        $order->update_meta_data($this->config->get_location_confirmation_key(), $checkout_data->has_location_confirmation() ? 'yes' : 'no');
        $self_declared_country_key = $this->config->get_customer_self_declared_country_key();
        if ('' !== $self_declared_country_key) {
            $order->update_meta_data($self_declared_country_key, $checkout_data->has_location_confirmation() ? 'yes' : 'no');
        }
    }
    private function write_vat_validation(WC_Order $order, SanitizedCheckoutData $checkout_data, string $country_code): void
    {
        if (!$checkout_data->wants_invoice()) {
            return;
        }
        $vat = $checkout_data->get_billing_tax_id();
        $order->update_meta_data($this->config->get_vat_confirmation_key(), $checkout_data->has_vat_confirmation() ? 'yes' : 'no');
        $evidence_key = $this->config->get_vat_number_evidence_key();
        if ('' !== $evidence_key && '' !== $vat) {
            $order->update_meta_data($evidence_key, $vat);
        }
        $self_declared_key = $this->config->get_vat_number_self_declared_key();
        if ('' !== $self_declared_key) {
            $order->update_meta_data($self_declared_key, $checkout_data->has_vat_confirmation() ? 'yes' : 'no');
        }
        if ('' === $vat || '' === $country_code) {
            return;
        }
        try {
            $result = $this->validator->validate(new VatNumber($vat), new Country($country_code));
        } catch (\InvalidArgumentException $e) {
            return;
        }
        $validated = ValidationResult::ERROR_VIES_API !== $result->get_error_code();
        $order->update_meta_data($this->config->get_vat_number_is_validated_key(), $validated ? 'yes' : 'no');
        $order->update_meta_data($this->config->get_vat_number_is_valid_key(), $result->is_valid() ? 'yes' : 'no');
    }
    private function get_ip_country(): string
    {
        if (!class_exists(WC_Geolocation::class)) {
            return '';
        }
        $ip_address = WC_Geolocation::get_ip_address();
        $geo = WC_Geolocation::geolocate_ip($ip_address);
        return $geo['country'] ?? '';
    }
}
