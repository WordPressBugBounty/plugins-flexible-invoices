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
$exchange_info = $attributes['footers'];
$styles = ['header' => ['color' => $attributes['headerTextColor'], 'background' => $attributes['headerBackground'], 'textAlign' => $attributes['headerTextAlign'], 'fontStyle' => $attributes['headerFontStyle'], 'fontSize' => $attributes['headerFontSize'], 'fontWeight' => $attributes['headerFontWeight'], 'borders' => ['rowWidthTop' => $attributes['headerBorderRowWidthTop'], 'rowWidthLeft' => $attributes['headerBorderRowWidthLeft'], 'rowWidthRight' => $attributes['headerBorderRowWidthRight'], 'rowWidthBottom' => $attributes['headerBorderRowWidthBottom'], 'rowColor' => $attributes['headerBorderRowColor'], 'cellWidthTop' => $attributes['headerBorderCellWidthTop'], 'cellWidthLeft' => $attributes['headerBorderCellWidthLeft'], 'cellWidthRight' => $attributes['headerBorderCellWidthRight'], 'cellWidthBottom' => $attributes['headerBorderCellWidthBottom'], 'cellColor' => $attributes['headerBorderCellColor']]], 'body' => ['color' => $attributes['bodyTextColor'], 'background' => $attributes['bodyBackground'], 'backgroundEven' => $attributes['multipleBodyBackground'] ? $attributes['bodyBackgroundEven'] : $attributes['bodyBackground'], 'colorEven' => $attributes['multipleBodyBackground'] ? $attributes['bodyTextColorEven'] : $attributes['bodyTextColor'], 'textAlign' => $attributes['bodyTextAlign'], 'fontStyle' => $attributes['bodyFontStyle'], 'fontSize' => $attributes['bodyFontSize'], 'fontWeight' => $attributes['bodyFontWeight'], 'borders' => ['rowWidthTop' => $attributes['bodyBorderRowWidthTop'], 'rowWidthLeft' => $attributes['bodyBorderRowWidthLeft'], 'rowWidthRight' => $attributes['bodyBorderRowWidthRight'], 'rowWidthBottom' => $attributes['bodyBorderRowWidthBottom'], 'rowColor' => $attributes['bodyBorderRowColor'], 'cellWidthTop' => $attributes['bodyBorderCellWidthTop'], 'cellWidthLeft' => $attributes['bodyBorderCellWidthLeft'], 'cellWidthRight' => $attributes['bodyBorderCellWidthRight'], 'cellWidthBottom' => $attributes['bodyBorderCellWidthBottom'], 'cellColor' => $attributes['bodyBorderCellColor']]], 'footer' => ['background' => $attributes['footerBackground'], 'color' => $attributes['footerTextColor'], 'textAlign' => $attributes['footerTextAlign'], 'fontStyle' => $attributes['footerFontStyle'], 'fontSize' => $attributes['footerFontSize'], 'fontWeight' => $attributes['footerFontWeight'], 'textDecoration' => $attributes['footerTextDecoration'], 'borders' => ['widthTop' => $attributes['footerBorderWidthTop'], 'widthLeft' => $attributes['footerBorderWidthLeft'], 'widthRight' => $attributes['footerBorderWidthRight'], 'widthBottom' => $attributes['footerBorderWidthBottom'], 'color' => $attributes['footerBorderColor']]]];
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
?> {
		width:100%;
		table-layout: fixed;
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
?> tfoot tr td {
		color: <?php 
echo \esc_html($styles['footer']['color']);
?>;
		text-align: <?php 
echo \esc_html($styles['footer']['textAlign']);
?>;
		font-style: <?php 
echo \esc_html($styles['footer']['fontStyle']);
?>;
		font-size: <?php 
echo \esc_html($styles['footer']['fontSize']);
?>;
		font-weight: <?php 
echo \esc_html($styles['footer']['fontWeight']);
?>;
	}

	table#<?php 
echo $table_id;
?> thead tr{
		border-style: solid;
		border-top-width: <?php 
echo \esc_html($styles['header']['borders']['rowWidthTop']);
?>;
		border-left-width: <?php 
echo \esc_html($styles['header']['borders']['rowWidthLeft']);
?>;
		border-right-width: <?php 
echo \esc_html($styles['header']['borders']['rowWidthRight']);
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
		border-top-width: <?php 
echo \esc_html($styles['header']['borders']['cellWidthTop']);
?>;
		border-left-width: <?php 
echo \esc_html($styles['header']['borders']['cellWidthLeft']);
?>;
		border-right-width: <?php 
echo \esc_html($styles['header']['borders']['cellWidthRight']);
?>;
		border-bottom-width: <?php 
echo \esc_html($styles['header']['borders']['cellWidthBottom']);
?>;
		border-color: <?php 
echo \esc_html($styles['header']['borders']['cellColor']);
?>;
	}
	table#<?php 
echo $table_id;
?> tbody tr{
		border-style: solid;
		border-top-width: <?php 
echo \esc_html($styles['body']['borders']['rowWidthTop']);
?>;
		border-left-width: <?php 
echo \esc_html($styles['body']['borders']['rowWidthLeft']);
?>;
		border-right-width: <?php 
echo \esc_html($styles['body']['borders']['rowWidthRight']);
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
		border-top-width: <?php 
echo \esc_html($styles['body']['borders']['cellWidthTop']);
?>;
		border-left-width: <?php 
echo \esc_html($styles['body']['borders']['cellWidthLeft']);
?>;
		border-right-width: <?php 
echo \esc_html($styles['body']['borders']['cellWidthRight']);
?>;
		border-bottom-width: <?php 
echo \esc_html($styles['body']['borders']['cellWidthBottom']);
?>;
		border-color: <?php 
echo \esc_html($styles['body']['borders']['cellColor']);
?>;
	}

	table#<?php 
echo $table_id;
?> tfoot tr{
		background: <?php 
echo \esc_html($styles['footer']['background']);
?>;
		border-style: solid;
		border-top-width: <?php 
echo \esc_html($styles['footer']['borders']['widthTop']);
?>;
		border-left-width: <?php 
echo \esc_html($styles['footer']['borders']['widthLeft']);
?>;
		border-right-width: <?php 
echo \esc_html($styles['footer']['borders']['widthRight']);
?>;
		border-bottom-width: <?php 
echo \esc_html($styles['footer']['borders']['widthBottom']);
?>;
		border-color: <?php 
echo \esc_html($styles['footer']['borders']['color']);
?>;
	}
	table#<?php 
echo $table_id;
?> tfoot tr:nth-child(odd) {
		border-top-width: 0;
	}
</style>
<!-- {ConversionTableBegin} -->
<table class="fitb-conversion-table" id="<?php 
echo $table_id;
?>" style="border-collapse: collapse;">
	<thead class="fitb-conversion-table-header">
	<tr class="fitb-conversion-table-header-row">
		<?php 
foreach ($headers as $index => $header) {
    if (!$states[$index]) {
        continue;
    }
    ?>
			<th class="fitb-conversion-table-header-cell"><?php 
    echo \esc_html($header);
    ?></th>
		<?php 
}
?>
	</tr>
	</thead>
	<tbody class="fitb-conversion-table-body">
	{ConversionTableBody}
	<!-- {ConversionBodyTemplateBegin} <tr class="fitb-conversion-table-row">
		<?php 
foreach ($rows as $index => $row) {
    if (!$states[$index]) {
        continue;
    }
    ?>
			<td class="fitb-conversion-table-cell"><?php 
    echo \esc_html($row);
    ?></td>
		<?php 
}
?>
	</tr> {ConversionBodyTemplateEnd} -->
	</tbody>
	<tfoot class="fitb-conversion-table-footer">
	<?php 
foreach ($exchange_info as $info) {
    ?>
	<tr>
		<td colspan="<?php 
    echo \count($headers);
    ?>"><?php 
    echo \esc_html($info);
    ?></td>
	</tr>
	<?php 
}
?>
	</tfoot>
</table>
<!-- {ConversionTableEnd} -->
<?php 
