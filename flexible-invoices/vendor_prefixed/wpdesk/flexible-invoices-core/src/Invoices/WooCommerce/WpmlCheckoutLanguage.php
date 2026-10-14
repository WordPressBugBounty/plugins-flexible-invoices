<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WooCommerce;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\Translator;
use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Stores the checkout language for WPML.
 *
 * Invoice and VAT fields are registered by invoices-checkout.
 */
class WpmlCheckoutLanguage implements Hookable
{
    private string $template_dir;
    public function __construct(string $template_dir)
    {
        $this->template_dir = $template_dir;
    }
    public function hooks()
    {
        add_action('woocommerce_after_order_notes', [$this, 'add_wpml_user_session_lang']);
        add_action('woocommerce_checkout_update_order_meta', [$this, 'save_wpml_user_session_lang']);
    }
    /**
     * Prints the current WPML language into classic checkout.
     *
     * @param mixed $checkout Checkout object. Unused.
     *
     * @internal You should not use this directly from another application
     */
    public function add_wpml_user_session_lang($checkout)
    {
        // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
        if (!Translator::is_wpml_active()) {
            return;
        }
        global $sitepress;
        $language = $sitepress->get_current_language();
        $template = $this->template_dir . 'woocommerce/wpml-user-lang.php';
        if (!is_string($language) || !is_readable($template)) {
            return;
        }
        include $template;
    }
    /**
     * Saves the customer language on the order.
     *
     * @param mixed $order_id Order ID.
     *
     * @internal You should not use this directly from another application
     */
    public function save_wpml_user_session_lang($order_id)
    {
        $wpml_user_lang = trim(sanitize_text_field(wp_unslash(apply_filters('wpml_current_language', 'en'))));
        if (!$wpml_user_lang) {
            return;
        }
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }
        $order->update_meta_data('wpml_user_lang', $wpml_user_lang);
        $order->save();
    }
}
