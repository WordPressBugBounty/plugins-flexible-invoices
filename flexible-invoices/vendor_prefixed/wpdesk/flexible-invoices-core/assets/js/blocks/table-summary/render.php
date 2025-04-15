<?php

namespace WPDeskFIVendor;

/**
 *
 * @var array $attributes
 */
$table_id = \esc_attr(\uniqid('table'));
$headers = $attributes['headers'];
$rows = $attributes['rows'];
$states = $attributes['states'];
$styles = ['table' => ['borderColor' => $attributes['tableBorderColor'], 'borderWidth' => $attributes['tableBorderWidth']], 'header' => ['color' => $attributes['headerTextColor'], 'background' => $attributes['headerBackground'], 'textAlign' => $attributes['headerTextAlign'], 'fontStyle' => $attributes['headerFontStyle'], 'fontSize' => $attributes['headerFontSize'], 'fontWeight' => $attributes['headerFontWeight'], 'borders' => ['rowWidthTop' => $attributes['headerBorderRowWidthTop'], 'rowWidthBottom' => $attributes['headerBorderRowWidthBottom'], 'rowColor' => $attributes['headerBorderRowColor'], 'columnWidthLeft' => $attributes['headerBorderColumnWidthLeft'], 'columnWidthRight' => $attributes['headerBorderColumnWidthRight'], 'columnColor' => $attributes['headerBorderColumnColor']]], 'body' => ['color' => $attributes['bodyTextColor'], 'background' => $attributes['bodyBackground'], 'backgroundEven' => $attributes['multipleBodyBackground'] ? $attributes['bodyBackgroundEven'] : $attributes['bodyBackground'], 'colorEven' => $attributes['multipleBodyBackground'] ? $attributes['bodyTextColorEven'] : $attributes['bodyTextColor'], 'textAlign' => $attributes['bodyTextAlign'], 'fontStyle' => $attributes['bodyFontStyle'], 'fontSize' => $attributes['bodyFontSize'], 'fontWeight' => $attributes['bodyFontWeight'], 'borders' => ['rowWidthTop' => $attributes['bodyBorderRowWidthTop'], 'rowWidthBottom' => $attributes['bodyBorderRowWidthBottom'], 'rowColor' => $attributes['bodyBorderRowColor'], 'columnWidthLeft' => $attributes['bodyBorderColumnWidthLeft'], 'columnWidthRight' => $attributes['bodyBorderColumnWidthRight'], 'columnColor' => $attributes['bodyBorderColumnColor']]]];
?>

<style>
	table#<?php 
echo $table_id;
?> td, th {
		word-wrap: break-word;
		white-space: normal;
	}
	table#<?php 
echo $table_id;
?> td {
		word-break: break-all;
		overflow: hidden;
	}
	table#<?php 
echo $table_id;
?>{
		width:100%;
		table-layout: fixed;
		border-style: solid;
		border-width: <?php 
echo \esc_html($styles['table']['borderWidth']);
?>;
		border-color: <?php 
echo \esc_html($styles['table']['borderColor']);
?>;
	}
	table#<?php 
echo $table_id;
?> thead tr {
		background: <?php 
echo \esc_html($styles['header']['background']);
?>;
	}

	table#<?php 
echo $table_id;
?> tbody tr {
		background: <?php 
echo \esc_html($styles['body']['background']);
?>;
	}
	table#<?php 
echo $table_id;
?> tbody tr:nth-child(even) {
		background: <?php 
echo \esc_html($styles['body']['backgroundEven']);
?>;
	}

	table#<?php 
echo $table_id;
?> thead tr th {
		color: <?php 
echo \esc_html($styles['header']['color']);
?>;
		text-align: <?php 
echo \esc_html($styles['header']['textAlign']);
?>;
		font-style: <?php 
echo \esc_html($styles['header']['fontStyle']);
?>;
		font-size: <?php 
echo \esc_html($styles['header']['fontSize']);
?>;
		font-weight: <?php 
echo \esc_html($styles['header']['fontWeight']);
?>;
	}

	table#<?php 
echo $table_id;
?> tbody tr td {
		color: <?php 
echo \esc_html($styles['body']['color']);
?>;
		text-align: <?php 
echo \esc_html($styles['body']['textAlign']);
?>;
		font-style: <?php 
echo \esc_html($styles['body']['fontStyle']);
?>;
		font-size: <?php 
echo \esc_html($styles['body']['fontSize']);
?>;
		font-weight: <?php 
echo \esc_html($styles['body']['fontWeight']);
?>;
	}
	table#<?php 
echo $table_id;
?> tbody tr:nth-child(even) td {
		color: <?php 
echo \esc_html($styles['body']['colorEven']);
?>;
	}
	table#<?php 
echo $table_id;
?> thead tr{
		border-style: solid;
		border-top-width: <?php 
echo \esc_html($styles['header']['borders']['rowWidthTop']);
?>;
		border-bottom-width: <?php 
echo \esc_html($styles['header']['borders']['rowWidthBottom']);
?>;
		border-color: <?php 
echo \esc_html($styles['header']['borders']['rowColor']);
?>;
	}
	table#<?php 
echo $table_id;
?> thead tr th{
		border-style: solid;
		border-left-width: <?php 
echo \esc_html($styles['header']['borders']['columnWidthLeft']);
?>;
		border-right-width: <?php 
echo \esc_html($styles['header']['borders']['columnWidthRight']);
?>;
		border-color: <?php 
echo \esc_html($styles['header']['borders']['columnColor']);
?>;
	}
	table#<?php 
echo $table_id;
?> tbody tr{
		border-style: solid;
		border-top-width: <?php 
echo \esc_html($styles['body']['borders']['rowWidthTop']);
?>;
		border-bottom-width: <?php 
echo \esc_html($styles['body']['borders']['rowWidthBottom']);
?>;
		border-color: <?php 
echo \esc_html($styles['body']['borders']['rowColor']);
?>;
	}
	table#<?php 
echo $table_id;
?> tbody td{
		border-style: solid;
		border-left-width: <?php 
echo \esc_html($styles['body']['borders']['columnWidthLeft']);
?>;
		border-right-width: <?php 
echo \esc_html($styles['body']['borders']['columnWidthRight']);
?>;
		border-color: <?php 
echo \esc_html($styles['body']['borders']['columnColor']);
?>;
	}
</style>

<!-- {SummaryTableBegin} -->
<table id="<?php 
echo $table_id;
?>" class="fitb-summary-table" style="border-collapse: collapse;">
	<thead class="fitb-summary-table-header">
	<tr class="fitb-summary-table-header-row">
		<?php 
foreach ($headers as $index => $header) {
    if (!$states[$index]) {
        continue;
    }
    ?>
			<th class="fitb-summary-table-header-cell"><?php 
    echo \esc_html($header);
    ?></th>
		<?php 
}
?>
	</tr>
	</thead>
	<tbody class="fitb-summary-table-body">
	{SummaryTableBody}
	<!-- {SummaryBodyTemplateBegin} <tr class="fitb-summary-table-row">
		<?php 
foreach ($rows as $index => $row) {
    if (!$states[$index]) {
        continue;
    }
    ?>
			<td class="fitb-summary-table-cell"><?php 
    echo \esc_html($row);
    ?></td>
		<?php 
}
?>
	</tr> {SummaryBodyTemplateEnd} -->
	</tbody>
</table>
<!-- {SummaryTableEnd} -->
<?php 
