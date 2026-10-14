<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\Validator;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\Country;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\ValueObject\VatNumber;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation\ValidationResult;
/**
 * Validates VAT numbers using the VIES system.
 */
class ViesValidation implements VatValidator
{
    private const CACHE_STATUS_VALID = 'valid';
    private const CACHE_STATUS_INVALID = 'invalid';
    private const CACHE_STATUS_API_ERROR = 'api_error';
    /**
     * Validation results for the current PHP request, keyed by country prefix and VAT number.
     *
     * @var array<string, ValidationResult>
     */
    private static array $runtime_cache = [];
    /**
     * @inheritDoc
     */
    public function validate(VatNumber $vat_number, ?Country $country): ValidationResult
    {
        $normalized_vat = $vat_number->get_normalized();
        if ('' === $normalized_vat) {
            return ValidationResult::success();
        }
        if (!$country instanceof Country) {
            return ValidationResult::failure(ValidationResult::ERROR_COUNTRY_REQUIRED, __('Country is required for VAT validation.', 'flexible-invoices'));
        }
        $country_code = $country->get_code();
        if (!$this->is_eu_country($country_code)) {
            //VIES validation should be used only on EU countries.
            return ValidationResult::success();
        }
        if (preg_match('/^[A-Z]{2}/', $normalized_vat, $matches)) {
            $vat_prefix = $matches[0];
            $vat_to_check = substr($normalized_vat, 2);
        } else {
            $vat_prefix = $country_code;
            $vat_to_check = $normalized_vat;
        }
        if ('GR' === $vat_prefix) {
            // Greece uses 'EL' for VAT validation in VIES
            $vat_prefix = 'EL';
        }
        $memory_key = $vat_prefix . '_' . $vat_to_check;
        if (isset(self::$runtime_cache[$memory_key])) {
            return self::$runtime_cache[$memory_key];
        }
        $transient_key = 'wpd_vies_' . md5($memory_key);
        $cached_result = $this->result_from_stored_value(get_transient($transient_key));
        if ($cached_result instanceof ValidationResult) {
            self::$runtime_cache[$memory_key] = $cached_result;
            return $cached_result;
        }
        $url = sprintf('https://ec.europa.eu/taxation_customs/vies/rest-api/ms/%s/vat/%s', rawurlencode($vat_prefix), rawurlencode($vat_to_check));
        $response = $this->remote_get($url);
        if (is_wp_error($response)) {
            return $this->remember($memory_key, $transient_key, self::CACHE_STATUS_API_ERROR);
        }
        $response_code = wp_remote_retrieve_response_code($response);
        if (200 !== $response_code) {
            return $this->remember($memory_key, $transient_key, self::CACHE_STATUS_API_ERROR);
        }
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, \true);
        if (is_array($data) && isset($data['isValid'])) {
            $status = (bool) $data['isValid'] ? self::CACHE_STATUS_VALID : self::CACHE_STATUS_INVALID;
            return $this->remember($memory_key, $transient_key, $status);
        }
        return $this->remember($memory_key, $transient_key, self::CACHE_STATUS_API_ERROR);
    }
    /**
     * Loads a VIES response without asking for compression.
     *
     * WordPress tells cURL to advertise every encoding it supports. VIES then
     * answers with gzip, Connection: close and no Content-Length. OpenSSL 3.5
     * treats the following socket close as an unexpected EOF, and cURL drops
     * the HTTP 200. An identity body is chunked, so cURL finishes it before
     * that close.
     *
     * @return array|\WP_Error
     */
    private function remote_get(string $url)
    {
        add_action('http_api_curl', [$this, 'configure_vies_curl'], 10, 3);
        try {
            return wp_remote_get($url, ['timeout' => 10, 'httpversion' => '1.1']);
        } finally {
            remove_action('http_api_curl', [$this, 'configure_vies_curl'], 10);
        }
    }
    /**
     * Stops this VIES request from advertising gzip, brotli or zstd.
     *
     * @param resource|\CurlHandle $handle cURL handle.
     * @param mixed                $args   Request arguments passed by WordPress.
     * @param string               $url    Request URL.
     *
     * @return void
     */
    public function configure_vies_curl($handle, $args, $url): void
    {
        if (!is_string($url) || \false === strpos($url, 'ec.europa.eu/taxation_customs/vies')) {
            return;
        }
        curl_setopt($handle, \CURLOPT_ENCODING, 'identity');
    }
    /**
     * Stores a VIES outcome for this request and in a transient.
     *
     * @param string $memory_key    Runtime cache key.
     * @param string $transient_key Transient key.
     * @param string $status        Cache status.
     *
     * @return ValidationResult
     */
    private function remember(string $memory_key, string $transient_key, string $status): ValidationResult
    {
        $result = $this->result_for_status($status);
        self::$runtime_cache[$memory_key] = $result;
        set_transient($transient_key, $status, $this->transient_ttl($status));
        return $result;
    }
    private function transient_ttl(string $status): int
    {
        if (self::CACHE_STATUS_VALID === $status) {
            return \DAY_IN_SECONDS;
        }
        if (self::CACHE_STATUS_INVALID === $status) {
            return \HOUR_IN_SECONDS;
        }
        return 15;
    }
    /**
     * Maps a previously stored cache value to a validation result.
     *
     * Boolean false is a cache miss: WordPress returns false both when the transient
     * is missing and when a boolean false was stored.
     *
     * @param mixed $cached Stored transient value.
     *
     * @return ValidationResult|null
     */
    private function result_from_stored_value($cached): ?ValidationResult
    {
        if (\false === $cached || null === $cached || '' === $cached) {
            return null;
        }
        if (self::CACHE_STATUS_API_ERROR === $cached) {
            return $this->api_error_result();
        }
        if (self::CACHE_STATUS_INVALID === $cached || 0 === $cached || '0' === $cached) {
            return $this->invalid_vat_result();
        }
        if (self::CACHE_STATUS_VALID === $cached || \true === $cached || 1 === $cached || '1' === $cached) {
            return ValidationResult::success();
        }
        return null;
    }
    private function result_for_status(string $status): ValidationResult
    {
        if (self::CACHE_STATUS_VALID === $status) {
            return ValidationResult::success();
        }
        if (self::CACHE_STATUS_INVALID === $status) {
            return $this->invalid_vat_result();
        }
        return $this->api_error_result();
    }
    private function invalid_vat_result(): ValidationResult
    {
        return ValidationResult::failure(ValidationResult::ERROR_INVALID_VAT, __('This VAT number is not registered in the EU VIES system. Check that you typed it correctly.', 'flexible-invoices'));
    }
    private function api_error_result(): ValidationResult
    {
        return ValidationResult::failure(ValidationResult::ERROR_VIES_API, __('We could not check this VAT number in the EU registry right now. Confirm that it is correct to continue.', 'flexible-invoices'));
    }
    private function is_eu_country(string $country_code): bool
    {
        $eu_countries = WC()->countries->get_european_union_countries('eu_vat');
        return in_array($country_code, $eu_countries, \true);
    }
}
