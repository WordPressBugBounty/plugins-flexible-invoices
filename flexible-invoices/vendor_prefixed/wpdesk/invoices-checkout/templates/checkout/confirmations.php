<?php

namespace WPDeskFIVendor;

/**
 * Checkout confirmations template.
 *
 * @var array $args
 */
if (!\defined('ABSPATH')) {
    exit;
}
$vat_style = $args['show_vat'] ? '' : 'display: none;';
$location_style = $args['show_location'] ? '' : 'display: none;';
$location_key = $args['location_key'];
$vat_key = $args['vat_key'];
?>
<div class="wpd-invoices-confirmations-location" style="margin-bottom: 20px; <?php 
echo \esc_attr($location_style);
?>">
	<?php 
\woocommerce_form_field($location_key, ['type' => 'checkbox', 'class' => ['form-row-wide'], 'label' => \__('I declare that despite my IP address, I am located in the selected billing country.', 'flexible-invoices'), 'required' => \false]);
?>
</div>

<div class="wpd-invoices-confirmations-vat" style="margin-bottom: 20px; <?php 
echo \esc_attr($vat_style);
?>">
	<?php 
\woocommerce_form_field($vat_key, ['type' => 'checkbox', 'class' => ['form-row-wide'], 'label' => \__('I confirm that my VAT number is valid.', 'flexible-invoices'), 'required' => \false]);
?>
</div>
<?php 
