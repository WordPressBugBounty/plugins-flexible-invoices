<?php

namespace WPDeskFIVendor;

/**
 * @var \WPDesk\Forms\Field $field
 * @var \WPDesk\View\Renderer\Renderer $renderer
 * @var string $name_prefix
 * @var string $value
 *
 * @var string $template_name Real field template.
 */
?>
<tr>
	<td></td>
	<td>
		<p class="submit">
			<?php 
//phpcs:disable
foreach ($field->get_grouped_fields() as $field) {
    $value = $field->get_default_value();
    echo $renderer->render($field->get_template_name(), ['field' => $field, 'renderer' => $renderer, 'name_prefix' => $name_prefix, 'value' => $value]);
}
//phpcs:enable
?>
		</p>
	</td>
</tr>

<?php 
