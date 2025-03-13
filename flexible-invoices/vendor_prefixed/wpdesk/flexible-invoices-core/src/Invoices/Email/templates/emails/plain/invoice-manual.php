<?php

namespace WPDeskFIVendor;

/**
 * Email z fakturą
 *
 * @var string $email
 * @var string $email_heading
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
    \printf(\esc_html__('Download Invoice: <a href="%1$s"><b>%2$s</b></a>', 'flexible-invoices'), \esc_url($download_url), \esc_html($document_name));
    echo '<br/><br/>';
}
?>

<?php 
/**
 * Fires in footer section of email template.
 */
\do_action('woocommerce_email_footer');
