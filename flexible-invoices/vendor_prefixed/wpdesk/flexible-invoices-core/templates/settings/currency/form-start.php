<?php

namespace WPDeskFIVendor;

/**
 * @var \WPDesk\Forms\Form\FormWithFields $form
 */
?>
<form class="wrap woocommerce" method="<?php 
echo \esc_attr($form->get_method());
?>" action="<?php 
echo \esc_attr($form->get_action());
?>">
	<h2 style="display:none;"></h2>
<?php 
$docs_link = 'https://docs.flexibleinvoices.com/category/785-currencies?utm_source=flexible-invoices-settings&utm_medium=link&utm_campaign=flexible-invoices-docs-link&utm_content=currency-rates';
if (\get_locale() === 'pl_PL') {
    $docs_link = 'https://www.wpdesk.pl/docs/faktury-woocommerce-docs/?utm_source=flexible-invoices-settings&utm_medium=link&utm_campaign=flexible-invoices-docs-link&utm_content=currency-rates#konfiguracja-walut';
}
$link = \sprintf('%2$s%1$s%3$s', \sprintf(\esc_html__('Read more in the %1$splugin documentation &rarr;%2$s', 'flexible-invoices'), '<a href="' . $docs_link . '" target="_blank" style="color: #4BB04E; font-weight: 700;">', '</a>'), '<strong>', '</strong>');
?>
	<div class="support-url-wrapper"><?php 
echo $link;
?></div>
	<table id="flexible_invoices_tax_table" class="form-table flexible_invoices_tax">
	<thead>
	<tr>
		<th class="sort"></th>
		<th class="name">
			<?php 
\esc_html_e('Currency', 'flexible-invoices');
?>
		</th>
		<th class="rate">
			<?php 
\esc_html_e('Currency position', 'flexible-invoices');
?>
		</th>
		<th class="rate">
			<?php 
\esc_html_e('Thousand separator', 'flexible-invoices');
?>
		</th>
		<th class="rate">
			<?php 
\esc_html_e('Decimal separator', 'flexible-invoices');
?>
		</th>
		<th class="delete"></th>
	</tr>
	</thead>

	<tbody class="ui-sortable">
<?php 
