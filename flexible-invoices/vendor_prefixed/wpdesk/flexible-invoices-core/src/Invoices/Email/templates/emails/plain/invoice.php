<?php

namespace WPDeskFIVendor;

/**
 * Email z fakturą (plain text)
 *
 * @var \WC_Order $order
 * @var string $email_heading
 * @var bool $sent_to_admin
 * @var string $plain_text
 * @var string $email
 */
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\Hooks;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
echo $email_heading . "\n\n";
// phpcs:ignore
echo "****************************************************\n\n";
if (isset($download_url)) {
    // translators: %s: download url
    \printf(\esc_html__('Download Invoice: %s', 'flexible-invoices'), \esc_url($download_url)) . "\n\n";
}
// translators: %s: order number
echo \sprintf(\esc_html__('Order number: %s', 'woocommerce'), \esc_html($order->get_order_number())) . "\n";
// translators: %s: order date
echo \sprintf(\esc_html__('Order date: %s', 'woocommerce'), \esc_html(\date_i18n(\wc_date_format(), \strtotime($order->get_date_created())))) . "\n";
Hooks::woocommerce_email_order_meta_hook($order, $sent_to_admin, $plain_text, $email);
echo "\n";
echo WPDesk\Library\FlexibleInvoicesCore\Email\BaseEmail::get_email_order_items($order, \true);
// phpcs:ignore WordPress.Security.EscapeOutput
echo "----------\n\n";
$totals = $order->get_order_item_totals();
if ($totals) {
    foreach ($totals as $total) {
        // phpcs:ignore
        echo $total['label'] . "\t " . $total['value'] . "\n";
        // do not escape HTML!
    }
}
echo "\n****************************************************\n\n";
Hooks::woocommerce_email_after_order_table_hook($order, $sent_to_admin, $plain_text, $email);
\esc_html_e('Your details', 'woocommerce') . "\n\n";
if ($order->get_billing_email()) {
    \esc_html_e('Email', 'woocommerce');
}
?>:
	<?php 
echo $order->get_billing_email() . "\n";
// phpcs:ignore
if ($order->get_billing_phone()) {
    \esc_html_e('Phone', 'woocommerce');
}
?>
	:
	<?php 
echo $order->get_billing_phone() . "\n";
// phpcs:ignore
\wc_get_template('emails/plain/email-addresses.php', ['order' => $order, 'sent_to_admin' => $sent_to_admin]);
echo "\n****************************************************\n\n";
echo \apply_filters('woocommerce_email_footer_text', \get_option('woocommerce_email_footer_text'));
// phpcs:ignore
