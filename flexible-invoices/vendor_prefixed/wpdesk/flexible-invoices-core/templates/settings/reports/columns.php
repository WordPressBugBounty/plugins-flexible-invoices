<?php

namespace WPDeskFIVendor;

/**
 * @var \FSIReportsVendor\WPDesk\Forms\Field $field ;
 */
$value = $value ?? '';
$active_columns = [];
if (!empty($value)) {
    $active_columns = \explode(',', $value);
}
$values = $field->get_possible_values();
?>
<input type="hidden" value="<?php 
echo \esc_attr($value);
?>" id="<?php 
echo \esc_attr($field->get_id());
?>" name="<?php 
echo \esc_attr($name_prefix);
?>[<?php 
echo \esc_attr($field->get_name());
?>]"/>
<div class="columns-layout">
	<div class="available-columns-wrap column-wrap">
		<h3><?php 
\esc_html_e('Available columns', 'flexible-invoices-reports');
?></h3>
		<ul id="available_columns" class="available connectedSortable">
			<?php 
foreach ($field->get_possible_values() as $column_id => $column_name) {
    if (\in_array($column_id, $active_columns)) {
        //phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
        continue;
    }
    ?>
				<li title="<?php 
    \esc_attr_e('Use drag & drop', 'flexible-invoices-reports');
    ?>" data-value="<?php 
    echo \esc_attr($column_id);
    ?>"><?php 
    echo \esc_html($column_name);
    ?></li>
			<?php 
}
?>
		</ul>
	</div>
	<div class="selected-columns-wrap column-wrap">
		<h3><?php 
\esc_html_e('Active columns', 'flexible-invoices-reports');
?></h3>
		<ul id="selected_columns" class="selected connectedSortable">
			<?php 
foreach ($active_columns as $column_id) {
    $column_name = $values[$column_id] ?? $column_id;
    ?>
				<li title="<?php 
    \esc_attr_e('Use drag & drop', 'flexible-invoices-reports');
    ?>" data-value="<?php 
    echo \esc_attr($column_id);
    ?>"><?php 
    echo \esc_html($column_name);
    ?></li>
			<?php 
}
?>

		</ul>
	</div>
</div>
<?php 
