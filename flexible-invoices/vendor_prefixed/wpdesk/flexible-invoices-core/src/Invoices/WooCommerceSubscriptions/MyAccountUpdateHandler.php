<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerceSubscriptions;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce\Checkout\InvoicesCheckoutConfig;
use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class MyAccountUpdateHandler implements Hookable
{
    public function hooks()
    {
        add_action('woocommerce_customer_save_address', [$this, 'update_nip_meta'], 10, 2);
    }
    public function update_nip_meta($user_id, $load_address)
    {
        if ('billing' !== $load_address) {
            return;
        }
        if (empty($_POST[InvoicesCheckoutConfig::BILLING_TAX_ID_USER_KEY])) {
            //phpcs:ignore WordPress.Security.NonceVerification.Missing
            return;
        }
        $new_vat = sanitize_text_field(wp_unslash($_POST[InvoicesCheckoutConfig::BILLING_TAX_ID_USER_KEY]));
        //phpcs:ignore WordPress.Security.NonceVerification.Missing
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
            $subscription->update_meta_data(InvoicesCheckoutConfig::BILLING_TAX_ID_KEY, $new_vat);
            $subscription->save();
        }
    }
}
