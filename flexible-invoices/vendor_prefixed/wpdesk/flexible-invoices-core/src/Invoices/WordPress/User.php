<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress;

use WP_User;
use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDeskFIVendor\WPDesk\View\Renderer\Renderer;
/**
 * Add Vat field in user account.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\WordPress
 */
class User implements Hookable
{
    private Renderer $renderer;
    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }
    public function hooks()
    {
        add_action('show_user_profile', [$this, 'add_vat_user_field']);
        add_action('edit_user_profile', [$this, 'add_vat_user_field']);
        add_action('personal_options_update', [$this, 'save_vat_user_field']);
        add_action('edit_user_profile_update', [$this, 'save_vat_user_field']);
    }
    /**
     * @internal You should not use this directly from another application
     */
    public function add_vat_user_field(WP_User $user)
    {
        $vat_number = get_user_meta($user->ID, 'billing_vat_number', \true);
        if (!is_string($vat_number) || '' === $vat_number) {
            $vat_number = get_user_meta($user->ID, 'vat_number', \true);
        }
        $invoice_ask_meta = get_user_meta($user->ID, 'billing_invoice_ask', \true);
        if ('' === $invoice_ask_meta) {
            $invoice_ask_meta = get_user_meta($user->ID, 'invoice_ask', \true);
        }
        $invoice_ask = filter_var($invoice_ask_meta, \FILTER_VALIDATE_BOOLEAN);
        $this->renderer->output_render('wordpress/user-profile-fields', ['vat_number' => $vat_number, 'invoice_ask' => $invoice_ask]);
    }
    /**
     * @param int $user_id
     *
     * @internal You should not use this directly from another application
     */
    public function save_vat_user_field($user_id)
    {
        check_admin_referer('update-user_' . $user_id);
        if (current_user_can('edit_user', $user_id)) {
            if (isset($_POST['billing_vat_number'])) {
                $vat_number = sanitize_text_field(wp_unslash($_POST['billing_vat_number']));
                update_user_meta($user_id, 'billing_vat_number', $vat_number);
                update_user_meta($user_id, 'vat_number', $vat_number);
            } elseif (isset($_POST['vat_number'])) {
                $vat_number = sanitize_text_field(wp_unslash($_POST['vat_number']));
                update_user_meta($user_id, 'vat_number', $vat_number);
                update_user_meta($user_id, 'billing_vat_number', $vat_number);
            }
            $invoice_ask = isset($_POST['invoice_ask']) ? '1' : '0';
            update_user_meta($user_id, 'billing_invoice_ask', $invoice_ask);
            update_user_meta($user_id, 'invoice_ask', $invoice_ask);
        }
    }
}
