<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout\SanitizedCheckoutData;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\InvoicesCheckout;
/**
 * Data Transfer Object encapsulating invoice request details.
 */
class InvoiceData
{
    private ?bool $want_invoice;
    private ?string $billing_tax_id;
    private ?string $shipping_tax_id;
    private ?string $billing_company;
    private ?string $shipping_company;
    private ?bool $location_confirmation;
    public function __construct(?bool $want_invoice = null, ?string $billing_tax_id = null, ?string $shipping_tax_id = null, ?string $billing_company = null, ?string $shipping_company = null, ?bool $location_confirmation = null)
    {
        $this->want_invoice = $want_invoice;
        $this->billing_tax_id = $billing_tax_id;
        $this->shipping_tax_id = $shipping_tax_id;
        $this->billing_company = $billing_company;
        $this->shipping_company = $shipping_company;
        $this->location_confirmation = $location_confirmation;
    }
    /**
     * Creates InvoiceData from a SanitizedCheckoutData DTO.
     */
    public static function from_sanitized_checkout_data(SanitizedCheckoutData $data): self
    {
        $want_invoice_bool = $data->wants_invoice();
        $billing_tax_id = $data->get_billing_tax_id();
        $shipping_tax_id = $data->get_shipping_tax_id();
        if (\false === $want_invoice_bool) {
            return new self(\false, '', '', '' !== $data->get_billing_company() ? $data->get_billing_company() : null, '' !== $data->get_shipping_company() ? $data->get_shipping_company() : null, $data->has_location_confirmation());
        }
        return new self($want_invoice_bool, '' !== $billing_tax_id ? $billing_tax_id : null, '' !== $shipping_tax_id ? $shipping_tax_id : null, '' !== $data->get_billing_company() ? $data->get_billing_company() : null, '' !== $data->get_shipping_company() ? $data->get_shipping_company() : null, $data->has_location_confirmation());
    }
    /**
     * Returns a new instance with the specified wants_invoice flag.
     * If false, it also unsets tax IDs to maintain consistency.
     */
    public function with_wants_invoice(bool $wants_invoice): self
    {
        $clone = clone $this;
        $clone->want_invoice = $wants_invoice;
        if (!$wants_invoice) {
            $clone->billing_tax_id = '';
            $clone->shipping_tax_id = '';
        }
        return $clone;
    }
    /**
     * Creates InvoiceData from raw input array (POST, Store API, or order meta) using ConfigInterface.
     */
    public static function from_array(array $data, ConfigInterface $config): self
    {
        $want_val = self::find_first_key_value($data, [$config->get_want_invoice_key(), $config->get_want_invoice_user_key(), InvoicesCheckout::KEY_WANT_INVOICE]);
        $billing_tax_id = self::find_first_key_value($data, [$config->get_billing_tax_id_key(), $config->get_billing_tax_id_user_key(), InvoicesCheckout::KEY_BILLING_TAX_ID]);
        $shipping_tax_id = self::find_first_key_value($data, [$config->get_shipping_tax_id_key(), $config->get_shipping_tax_id_user_key(), InvoicesCheckout::KEY_SHIPPING_TAX_ID]);
        $billing_company = self::find_first_key_value($data, [$config->get_billing_company_key(), $config->get_billing_company_user_key(), InvoicesCheckout::KEY_BILLING_COMPANY]);
        $shipping_company = self::find_first_key_value($data, [$config->get_shipping_company_key(), $config->get_shipping_company_user_key(), InvoicesCheckout::KEY_SHIPPING_COMPANY]);
        $want_invoice_bool = null !== $want_val ? filter_var($want_val, \FILTER_VALIDATE_BOOLEAN) : null;
        // If the user explicitly does not want an invoice, ensure VAT fields are wiped out globally
        if (\false === $want_invoice_bool) {
            $billing_tax_id = '';
            $shipping_tax_id = '';
        }
        $location_confirmation = self::find_first_key_value($data, [$config->get_location_confirmation_key()]);
        $location_confirmation_bool = null !== $location_confirmation ? filter_var($location_confirmation, \FILTER_VALIDATE_BOOLEAN) : null;
        return new self($want_invoice_bool, null !== $billing_tax_id ? (string) $billing_tax_id : null, null !== $shipping_tax_id ? (string) $shipping_tax_id : null, null !== $billing_company ? (string) $billing_company : null, null !== $shipping_company ? (string) $shipping_company : null, $location_confirmation_bool);
    }
    /**
     * Finds the first present key value in the target array.
     *
     * @return mixed|null
     */
    private static function find_first_key_value(array $data, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                return $data[$key];
            }
        }
        return null;
    }
    public function get_want_invoice(): ?bool
    {
        return $this->want_invoice;
    }
    public function get_billing_tax_id(): ?string
    {
        return $this->billing_tax_id;
    }
    public function get_shipping_tax_id(): ?string
    {
        return $this->shipping_tax_id;
    }
    public function get_billing_company(): ?string
    {
        return $this->billing_company;
    }
    public function get_shipping_company(): ?string
    {
        return $this->shipping_company;
    }
    public function has_want_invoice(): bool
    {
        return null !== $this->want_invoice;
    }
    public function has_billing_tax_id(): bool
    {
        return null !== $this->billing_tax_id;
    }
    public function has_shipping_tax_id(): bool
    {
        return null !== $this->shipping_tax_id;
    }
    public function has_billing_company(): bool
    {
        return null !== $this->billing_company;
    }
    public function has_shipping_company(): bool
    {
        return null !== $this->shipping_company;
    }
    public function get_location_confirmation(): ?bool
    {
        return $this->location_confirmation;
    }
    public function has_location_confirmation(): bool
    {
        return null !== $this->location_confirmation;
    }
}
