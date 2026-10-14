<?php

namespace WPDeskFIVendor;

/**
 * VIES / OSS details on the order edit screen.
 *
 * @var array $args
 */
if (!\defined('ABSPATH')) {
    exit;
}
$countries = $args['countries'] ?? [];
$billing_code = $args['billing_country'] ?? '';
$ip_code = $args['ip_country'] ?? '';
$billing_label = $countries[$billing_code] ?? $billing_code;
$ip_label = $countries[$ip_code] ?? $ip_code;
$vat_number = $args['vat_number'] ?? '';
$validated = !empty($args['validated']);
$valid = !empty($args['valid']);
$self_declared = !empty($args['vat_number_self_declared']);
$location_decl = !empty($args['customer_self_declared_country']);
$ip_address = $args['ip_address'] ?? '';
?>
<p>
	<strong><?php 
\esc_html_e('VAT Number', 'flexible-invoices');
?>:</strong><br />
	<?php 
echo $vat_number ? \esc_html($vat_number) : \esc_html__('None', 'flexible-invoices');
?>
</p>
<p>
	<strong><?php 
\esc_html_e('VIES', 'flexible-invoices');
?>:</strong><br />
	<?php 
if (!$vat_number) {
    \esc_html_e('Not checked', 'flexible-invoices');
} elseif (!$validated) {
    \esc_html_e('Not validated (registry unavailable)', 'flexible-invoices');
} elseif ($valid) {
    \esc_html_e('Valid', 'flexible-invoices');
} else {
    \esc_html_e('Invalid', 'flexible-invoices');
}
?>
</p>
<p>
	<strong><?php 
\esc_html_e('Self-declared VAT', 'flexible-invoices');
?>:</strong><br />
	<?php 
echo $self_declared ? \esc_html__('Yes', 'flexible-invoices') : \esc_html__('No', 'flexible-invoices');
?>
</p>
<p>
	<strong><?php 
\esc_html_e('Billing country', 'flexible-invoices');
?>:</strong><br />
	<?php 
echo \esc_html($billing_label);
?>
</p>
<p>
	<strong><?php 
\esc_html_e('IP country', 'flexible-invoices');
?>:</strong><br />
	<?php 
echo $ip_label ? \esc_html($ip_label) : \esc_html__('Unknown', 'flexible-invoices');
?>
	<?php 
if ($ip_address) {
    ?>
		<br /><small><?php 
    echo \esc_html($ip_address);
    ?></small>
	<?php 
}
?>
</p>
<p>
	<strong><?php 
\esc_html_e('Self-declared location', 'flexible-invoices');
?>:</strong><br />
	<?php 
echo $location_decl ? \esc_html__('Yes', 'flexible-invoices') : \esc_html__('No', 'flexible-invoices');
?>
</p>
<?php 
