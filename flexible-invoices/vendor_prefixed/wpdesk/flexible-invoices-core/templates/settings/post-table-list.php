<?php

namespace WPDeskFIVendor;

/**
 * @var \WPDesk\Forms\Field $field
 * @var \WPDesk\View\Renderer\Renderer $renderer
 *
 */
?>
<tr>
	<td colspan="2">
		<div>
			<?php 
echo $field->get_attribute('table-content');
?>
		</div>
	</td>
</tr>

<?php 
