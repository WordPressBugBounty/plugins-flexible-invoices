<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce\Checkout;

use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Reads VAT user meta saved before invoices-checkout.
 *
 * The library stores one key per field and does not read the previous one.
 * Checkout used to write `vat_number` and `invoice_ask`. The `billing_` prefix
 * arrived with the library. This filter fills a read only when the new key has
 * never been saved. A saved empty value stays empty: clearing the VAT number
 * must not restore `vat_number`. Saving still writes the new keys only.
 */
class LegacyUserMeta implements Hookable
{
    /**
     * New user meta key => key written by checkout before invoices-checkout.
     */
    private const KEYS = [InvoicesCheckoutConfig::BILLING_TAX_ID_USER_KEY => 'vat_number', InvoicesCheckoutConfig::WANT_INVOICE_USER_KEY => 'invoice_ask'];
    /**
     * Stops the stored-value lookup from entering this filter again.
     *
     * Callers must use this same instance. The flag has to be visible to the
     * filter callback while `read()` asks WordPress for the raw row.
     */
    private bool $reading_stored_meta = \false;
    /**
     * Fires hooks.
     */
    public function hooks()
    {
        add_filter('get_user_metadata', [$this, 'read_legacy_user_meta'], 10, 4);
    }
    /**
     * Returns the pre-library value when the library key has never been saved.
     *
     * A row with an empty VAT number is a stored clearance, not a missing key.
     * `0` and `no` on the new invoice-ask key are a stored answer.
     *
     * @param mixed $value     Short-circuit value. Null lets WordPress read the database.
     * @param mixed $object_id User ID.
     * @param mixed $meta_key  Meta key being read.
     * @param mixed $single    Whether a single value was requested.
     *
     * @return mixed
     */
    public function read_legacy_user_meta($value, $object_id, $meta_key, $single)
    {
        if ($this->reading_stored_meta || null !== $value) {
            return $value;
        }
        if (!is_string($meta_key) || !isset(self::KEYS[$meta_key])) {
            return $value;
        }
        if (!is_numeric($object_id)) {
            return $value;
        }
        $user_id = (int) $object_id;
        if ($this->key_is_stored($user_id, $meta_key)) {
            return $value;
        }
        $legacy = $this->stored_user_meta($user_id, self::KEYS[$meta_key]);
        if (!is_string($legacy) || '' === $legacy) {
            return $value;
        }
        if (!$single) {
            return [$legacy];
        }
        return $legacy;
    }
    /**
     * Reads a library user meta key.
     *
     * The pre-library key is used only when the new key has no database row.
     * An empty saved value is returned as empty.
     */
    public function read(int $user_id, string $meta_key): string
    {
        $this->reading_stored_meta = \true;
        try {
            if (metadata_exists('user', $user_id, $meta_key)) {
                $stored = get_user_meta($user_id, $meta_key, \true);
            } elseif (isset(self::KEYS[$meta_key])) {
                $stored = get_user_meta($user_id, self::KEYS[$meta_key], \true);
            } else {
                $stored = '';
            }
        } finally {
            $this->reading_stored_meta = \false;
        }
        return is_string($stored) ? $stored : '';
    }
    /**
     * Whether the new key has a row, including a row whose value is empty.
     */
    private function key_is_stored(int $user_id, string $meta_key): bool
    {
        $this->reading_stored_meta = \true;
        try {
            return metadata_exists('user', $user_id, $meta_key);
        } finally {
            $this->reading_stored_meta = \false;
        }
    }
    private function stored_user_meta(int $object_id, string $meta_key)
    {
        $this->reading_stored_meta = \true;
        try {
            return get_user_meta($object_id, $meta_key, \true);
        } finally {
            $this->reading_stored_meta = \false;
        }
    }
}
