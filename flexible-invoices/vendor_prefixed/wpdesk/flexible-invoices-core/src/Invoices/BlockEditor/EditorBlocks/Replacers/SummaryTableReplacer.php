<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Decorators\TemplateDocumentDecorator;
class SummaryTableReplacer extends AbstractBasicReplacer
{
    public function modify_content(TemplateDocumentDecorator $invoice, string $content): string
    {
        $shortcodes = $this->get_shortcodes_to_replace($invoice, $content);
        return $this->replace_shortcodes($shortcodes, $content);
    }
    protected function get_wrapper_tags(): array
    {
        return ['start' => 'SummaryTableBegin', 'end' => 'SummaryTableEnd'];
    }
    protected function get_shortcodes_to_replace(TemplateDocumentDecorator $invoice, string $content): array
    {
        $totals = $invoice->array_to_string_as_money($invoice->get_totals());
        $taxes = $invoice->array_to_string_as_money($invoice->get_totals_by_taxes());
        return ['{SummaryTableBody}' => $this->replace_table_body($taxes, $content), '{SummaryTotalNet}' => $totals['total_net_sum'], '{SummaryTotalTax}' => $totals['total_tax_sum'], '{SummaryTotalGross}' => $totals['total_gross_sum']];
    }
    private function replace_table_body(array $taxes, string $template_html): string
    {
        $table_row_template = $this->get_body_template($template_html);
        $table_body = '';
        foreach ($taxes as $tax_name => $tax) {
            $product_table_hints = ['{SummaryTaxNet}' => esc_html($tax['total_net_sum']), '{SummaryTaxName}' => esc_html($tax_name), '{SummaryTax}' => esc_html($tax['total_vat_sum']), '{SummaryTaxGross}' => esc_html($tax['total_gross_sum'])];
            $shortcodes = array_keys($product_table_hints);
            $replacements = array_values($product_table_hints);
            $row = str_replace($shortcodes, $replacements, $table_row_template);
            $table_body .= $row;
        }
        return $table_body;
    }
    private function get_body_template($content): string
    {
        return $this->get_content_between_tags($content, 'SummaryBodyTemplateBegin', 'SummaryBodyTemplateEnd');
    }
    private function get_content_between_tags(string $text, string $starting_tag, string $ending_tag): string
    {
        $pattern = '/<!--\s*\{' . $starting_tag . '\}(.*?)\{' . $ending_tag . '\}\s*-->/s';
        if (preg_match($pattern, $text, $matches)) {
            return $matches[1];
        }
        return '';
    }
}
