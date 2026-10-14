<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Validation;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
/**
 * Decides checkout and tax behaviour when VIES rejects a number or is unreachable.
 */
class ViesApiFailurePolicy
{
    private ConfigInterface $config;
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }
    /**
     * Whether checkout should be blocked for this validation result.
     */
    public function should_block_checkout(ValidationResult $result, bool $has_vat_confirmation): bool
    {
        if ($result->is_valid()) {
            return \false;
        }
        if (!$this->is_vies_policy_error($result)) {
            return \true;
        }
        if ($this->accepts_order_on_vies_failure()) {
            return \false;
        }
        return !$has_vat_confirmation;
    }
    /**
     * Whether the self-declare VAT confirmation checkbox should be shown.
     */
    public function should_show_vat_confirmation(ValidationResult $result): bool
    {
        return $this->is_vies_policy_error($result) && !$this->accepts_order_on_vies_failure();
    }
    /**
     * Whether a VIES failure should strip VAT (reverse charge fallback).
     */
    public function should_exempt_vat(ValidationResult $result, bool $has_vat_confirmation): bool
    {
        if ($result->is_valid()) {
            return \true;
        }
        if (!$this->is_vies_policy_error($result)) {
            return \false;
        }
        if (ConfigInterface::VIES_FAILURE_ACCEPT_WITHOUT_VAT === $this->config->get_vies_failure_handling()) {
            return \true;
        }
        return ConfigInterface::VIES_FAILURE_REJECT === $this->config->get_vies_failure_handling() && $has_vat_confirmation;
    }
    private function is_vies_policy_error(ValidationResult $result): bool
    {
        return in_array($result->get_error_code(), [ValidationResult::ERROR_VIES_API, ValidationResult::ERROR_INVALID_VAT], \true);
    }
    private function accepts_order_on_vies_failure(): bool
    {
        return in_array($this->config->get_vies_failure_handling(), [ConfigInterface::VIES_FAILURE_ACCEPT_WITH_VAT, ConfigInterface::VIES_FAILURE_ACCEPT_WITHOUT_VAT], \true);
    }
}
