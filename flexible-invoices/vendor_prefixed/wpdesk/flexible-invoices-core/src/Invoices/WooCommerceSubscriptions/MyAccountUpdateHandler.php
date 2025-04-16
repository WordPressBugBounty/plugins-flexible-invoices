<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerceSubscriptions;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Settings;
use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class MyAccountUpdateHandler implements Hookable
{
    private const BILLING_VAT_NUMBER_POST = 'billing_vat_number';
    private const BILLING_VAT_NUMBER_META = '_billing_vat_number';
    public function hooks()
    {
        add_action('woocommerce_customer_save_address', [$this, 'update_nip_meta'], 10, 2);
    }
    public function update_nip_meta($user_id, $load_address)
    {
        if ('billing' !== $load_address) {
            return;
        }
        if (empty($_POST[self::BILLING_VAT_NUMBER_POST])) {
            //phpcs:ignore WordPress.Security.NonceVerification.Missing
            return;
        }
        $new_vat = sanitize_text_field($_POST[self::BILLING_VAT_NUMBER_POST]);
        //phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        // Return if user didn't checked the option to update all subscriptions.
        if (!empty($_POST['update_all_subscriptions_addresses'])) {
            //phpcs:ignore WordPress.Security.NonceVerification.Missing
            $this->update_future_subscriptions($user_id, $new_vat);
        }
    }
    private function update_future_subscriptions(int $user_id, string $new_vat): void
    {
        $subscriptions = wcs_get_users_subscriptions($user_id);
        //@phpstan-ignore-line
        foreach ($subscriptions as $subscription) {
            // Statuses copied from WCS function WC_Subscription_Addresses::maybe_update_subscription_addresses
            if (!$subscription->has_status(['active', 'on-hold'])) {
                continue;
            }
            $subscription->update_meta_data(self::BILLING_VAT_NUMBER_META, $new_vat);
            $subscription->set_meta_data(self::BILLING_VAT_NUMBER_META, $new_vat);
            $subscription->save();
        }
    }
}
