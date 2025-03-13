<?php

namespace WPDeskFIVendor;

/**
 * Email z fakturą
 *
 * @var \WC_Order $order
 * @var string $email_heading
 * @var string $email
 * @var bool $sent_to_admin
 * @var string $plain_text
 */
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly 
?>

<?php 
\WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\Hooks::woocommerce_email_header_hook($email_heading, $email);
?>

<?php 
if (isset($download_url) && isset($document_name)) {
    // translators: %1$s - download url, %2$s - document name
    \printf(\wp_kses_post(\__('Download Invoice: <a href="%s"><b>%s</b></a>', 'flexible-invoices')), \esc_url($download_url), \esc_html($document_name));
    //phpcs:ignore WordPress.WP.I18n.UnorderedPlaceholdersText
    echo '<br/><br/>';
}
?>
<h2><?php 
\esc_html_e('Order', 'woocommerce') . ': ' . $order->get_order_number();
?> (<?php 
\printf('<time datetime="%s">%s</time>', \esc_html(\date_i18n('c', \strtotime($order->get_date_created()))), \esc_html(\date_i18n(\wc_date_format(), \strtotime($order->get_date_created()))));
?>)</h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php 
\esc_html_e('Product', 'woocommerce');
?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php 
\esc_html_e('Quantity', 'woocommerce');
?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php 
\esc_html_e('Price', 'woocommerce');
?></th>
		</tr>
	</thead>
	<tbody>
	<?php 
echo WPDesk\Library\FlexibleInvoicesCore\Email\BaseEmail::get_email_order_items($order);
// phpcs:ignore WordPress.Security.EscapeOutput
?>
	</tbody>
	<tfoot>
		<?php 
$totals = $order->get_order_item_totals();
if ($totals) {
    $i = 0;
    foreach ($totals as $total) {
        ++$i;
        // do not escape HTML for total items!
        ?>
					<tr>
						<th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee;
						<?php 
        if ($i === 1) {
            echo 'border-top-width: 4px;';
        }
        ?>
						"><?php 
        echo $total['label'];
        // phpcs:ignore 
        ?></th>
						<td style="text-align:left; border: 1px solid #eee;
						<?php 
        if ($i === 1) {
            echo 'border-top-width: 4px;';
        }
        ?>
						"><?php 
        echo $total['value'];
        // phpcs:ignore 
        ?></td>
					</tr>
					<?php 
    }
}
?>
	</tfoot>
</table>

<?php 
\WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\Hooks::woocommerce_email_after_order_table_hook($order, $sent_to_admin, $plain_text, $email);
\WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\Hooks::woocommerce_email_order_meta_hook($order, $sent_to_admin, $plain_text, $email);
?>
<br/><br/>
<h2><?php 
\esc_html_e('Customer details', 'woocommerce');
?></h2>

<?php 
if ($order->get_billing_email()) {
    ?>
	<p><strong><?php 
    \esc_html_e('Email', 'woocommerce');
    ?>: </strong> <?php 
    echo \esc_html($order->get_billing_email());
    ?></p>
<?php 
}
if ($order->get_billing_phone()) {
    ?>
	<p><strong><?php 
    \esc_html_e('Phone', 'woocommerce');
    ?>: </strong> <?php 
    echo \esc_html($order->get_billing_phone());
    ?></p>
<?php 
}
?>

<?php 
\wc_get_template('emails/email-addresses.php', ['order' => $order]);
?>

<?php 
/**
 * Fires in footer section of email template.
 */
\do_action('woocommerce_email_footer');
