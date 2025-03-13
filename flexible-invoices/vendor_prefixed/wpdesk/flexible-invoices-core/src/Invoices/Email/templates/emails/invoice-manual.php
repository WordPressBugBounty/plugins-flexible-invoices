<?php

namespace WPDeskFIVendor;

/**
 * Email z fakturą
 *
 * @var string $email_heading
 * @var string $email
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

<?php 
/**
 * Fires in footer section of email template.
 */
\do_action('woocommerce_email_footer');
