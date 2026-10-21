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
        $status = $this->status_from_http($response);
        // A busy-gateway answer comes back in under a second. One more try reaches the real verdict.
        if (self::CACHE_STATUS_API_ERROR === $status && !$this->is_timeout($response)) {
            sleep(2);
            $response = $this->remote_get($url);
            $status = $this->status_from_http($response);
        }
        return $this->remember($memory_key, $transient_key, $status);
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
     * The Europa gateway also answers a non-browser user agent with HTTP 200
     * and userError MS_MAX_CONCURRENT_REQ, for a number a browser call reports
     * as valid. WordPress sends "WordPress/x.y; {site}".
     *
     * @return array|\WP_Error
     */
    private function remote_get(string $url)
    {
        add_action('http_api_curl', [$this, 'configure_vies_curl'], 10, 3);
        try {
            return wp_remote_get($url, ['timeout' => 20, 'httpversion' => '1.1', 'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36']);
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
     * @param array|\WP_Error $response HTTP API response.
     */
    private function status_from_http($response): string
    {
        if (is_wp_error($response) || 200 !== (int) wp_remote_retrieve_response_code($response)) {
            return self::CACHE_STATUS_API_ERROR;
        }
        $data = json_decode(wp_remote_retrieve_body($response), \true);
        if (!is_array($data) || !isset($data['isValid'])) {
            return self::CACHE_STATUS_API_ERROR;
        }
        return $this->status_from_response($data);
    }
    /**
     * A timed-out call already waited the full limit, so it is not retried.
     *
     * @param array|\WP_Error $response HTTP API response.
     */
    private function is_timeout($response): bool
    {
        if (!is_wp_error($response)) {
            return \false;
        }
        $message = $response->get_error_message();
        return \false !== stripos($message, 'timed out') || \false !== stripos($message, 'cURL error 28');
    }
    /**
     * VIES answers HTTP 200 with isValid false when a member state is busy.
     * userError then names the outage. Only INVALID means the number was rejected.
     *
     * @param array $data Decoded VIES JSON.
     */
    private function status_from_response(array $data): string
    {
        $user_error = strtoupper(trim((string) ($data['userError'] ?? '')));
        if (\true === $data['isValid'] || 'VALID' === $user_error) {
            return self::CACHE_STATUS_VALID;
        }
        if ('' === $user_error || 'INVALID' === $user_error || 'INVALID_INPUT' === $user_error) {
            return self::CACHE_STATUS_INVALID;
        }
        return self::CACHE_STATUS_API_ERROR;
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
