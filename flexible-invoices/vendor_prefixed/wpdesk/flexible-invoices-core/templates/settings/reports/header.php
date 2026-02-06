<?php

namespace WPDeskFIVendor;

/**
 * @var \WPDesk\Forms\Field $field
 * @var string $name_prefix
 * @var string $value
 */
$header_size = $field->get_meta_value('header_size') ?: '2';
$classes = $field->has_classes() ? 'class="' . $field->get_classes() . '"' : '';
?>
<tr>
	<td class="header-column" colspan="2">
<?php 
if ($field->has_label()) {
    ?>
	<h<?php 
    echo \esc_attr($header_size);
    echo $classes;
    //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
    ?>>
	<?php 
    echo \esc_html($field->get_label());
    ?>
	</h<?php 
    echo \esc_attr($header_size);
    ?>>
	<?php 
}
?>

<?php 
if ($field->has_description()) {
    ?>
	<p <?php 
    echo $classes;
    //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
    ?> >
	<?php 
    echo \wp_kses_post($field->get_description());
    ?>
	</p>
	<?php 
}
?>
	</td>
</tr>
<?php 
