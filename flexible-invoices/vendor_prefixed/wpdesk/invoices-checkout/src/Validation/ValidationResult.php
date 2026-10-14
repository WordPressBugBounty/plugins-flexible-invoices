<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation;

/**
 * Value Object encapsulating the result of a validation check.
 */
class ValidationResult
{
    public const ERROR_VIES_API = 'vies_api_error';
    public const ERROR_INVALID_LENGTH = 'invalid_length';
    public const ERROR_INVALID_CONTROL_SUM = 'invalid_control_sum';
    public const ERROR_INVALID_VAT = 'invalid_vat';
    public const ERROR_VAT_REQUIRED = 'vat_number_required';
    public const ERROR_COUNTRY_REQUIRED = 'country_required';
    private bool $is_valid;
    private ?string $error_code;
    private ?string $error_message;
    public function __construct(bool $is_valid = \true, ?string $error_code = null, ?string $error_message = null)
    {
        $this->is_valid = $is_valid;
        $this->error_code = $error_code;
        $this->error_message = $error_message;
    }
    /**
     * Creates a successful ValidationResult.
     */
    public static function success(): self
    {
        return new self(\true);
    }
    /**
     * Creates a failed ValidationResult with error code and message.
     */
    public static function failure(string $error_code, string $error_message): self
    {
        return new self(\false, $error_code, $error_message);
    }
    public function is_valid(): bool
    {
        return $this->is_valid;
    }
    public function get_error_code(): ?string
    {
        return $this->error_code;
    }
    public function get_error_message(): ?string
    {
        return $this->error_message;
    }
}
