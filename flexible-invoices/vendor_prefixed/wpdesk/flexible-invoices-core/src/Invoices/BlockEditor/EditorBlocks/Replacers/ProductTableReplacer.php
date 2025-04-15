<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Decorators\TemplateDocumentDecorator;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\Currency;
class ProductTableReplacer extends AbstractBasicReplacer
{
    protected Currency $currency_helper;
    public function modify_content(TemplateDocumentDecorator $invoice, string $content): string
    {
        $this->currency_helper = new Currency($invoice->get_currency());
        $shortcodes = $this->get_shortcodes_to_replace($invoice, $content);
        $content = $this->remove_templates($content);
        return $this->replace_shortcodes($shortcodes, $content);
    }
    protected function get_wrapper_tags(): array
    {
        return ['start' => 'ProductTableBegin', 'end' => 'ProductTableEnd'];
    }
    protected function remove_templates(string $content): string
    {
        $content = $this->remove_content_between_tags($content, 'ProductBodyTemplateBegin', 'ProductBodyTemplateEnd');
        return $this->remove_content_between_tags($content, 'ProductHeaderTemplateBegin', 'ProductHeaderTemplateEnd');
    }
    protected function get_shortcodes_to_replace(TemplateDocumentDecorator $invoice, string $content): array
    {
        return ['{ProductTableBody}' => $this->replace_table_body($invoice, $content), '{ProductTableHeader}' => $this->modify_table_header($invoice, $content)];
    }
    protected function modify_table_header(TemplateDocumentDecorator $invoice, string $content): string
    {
        $header = $this->get_header_template($content);
        if (!$this->should_remove_tax_cells($invoice)) {
            return $header;
        }
        $body = $this->get_body_template($content);
        $shortcodes_to_hide = $this->get_shortcodes_with_vat_values();
        foreach ($shortcodes_to_hide as $shortcode) {
            $column_number = $this->find_column_by_shortcode($body, $shortcode);
            if (null !== $column_number) {
                $header = $this->remove_column($header, $column_number);
            }
        }
        return $header;
    }
    protected function get_shortcodes_with_vat_values(): array
    {
        return ['ProductTaxRate', 'ProductTaxAmount', 'ProductGrossAmount'];
    }
    protected function replace_table_body(TemplateDocumentDecorator $invoice, string $template_html): string
    {
        $row_template = $this->get_body_template($template_html);
        if ($this->should_remove_tax_cells($invoice)) {
            $shortcodes_to_hide = $this->get_shortcodes_with_vat_values();
            foreach ($shortcodes_to_hide as $shortcode) {
                $product_tax_rate_column = $this->find_column_by_shortcode($row_template, $shortcode);
                if (null !== $product_tax_rate_column) {
                    $row_template = $this->remove_column($row_template, $product_tax_rate_column);
                }
            }
        }
        $table_body = '';
        $items = $invoice->get_items();
        foreach ($items as $key => $item) {
            $product_table_hints = ['{Number}' => $key + 1, '{ProductName}' => $item['name'], '{ProductUnit}' => $item['unit'], '{ProductSKU}' => $item['sku'] ?? '', '{ProductDiscount}' => $item['discount'] ?? '', '{ProductQty}' => $item['quantity'], '{ProductNetPrice}' => $this->currency_helper->string_as_money($item['net_price']), '{ProductNetAmount}' => $this->currency_helper->string_as_money($item['net_price_sum']), '{ProductTaxRate}' => $item['vat_type_name'], '{ProductTaxAmount}' => $this->currency_helper->string_as_money($item['vat_sum']), '{ProductGrossAmount}' => $this->currency_helper->string_as_money($item['total_price'])];
            $shortcodes = array_keys($product_table_hints);
            $values = array_values($product_table_hints);
            $row = str_replace($shortcodes, $values, $row_template);
            $table_body .= $row;
        }
        return $table_body;
    }
    protected function should_remove_tax_cells(TemplateDocumentDecorator $invoice): bool
    {
        return !$invoice->get_total_tax() && $this->block_template->is_hide_tax_cells_enabled();
    }
    protected function get_body_template($content): string
    {
        return $this->get_content_between_tags($content, 'ProductBodyTemplateBegin', 'ProductBodyTemplateEnd');
    }
    protected function get_header_template($content): string
    {
        return $this->get_content_between_tags($content, 'ProductHeaderTemplateBegin', 'ProductHeaderTemplateEnd');
    }
    protected function remove_column(string $content, int $column_id): string
    {
        return $this->remove_content_between_tags($content, 'ColumnStart' . $column_id, 'ColumnEnd' . $column_id);
    }
    protected function get_content_between_tags(string $text, string $starting_tag, string $ending_tag): string
    {
        $pattern = '/<!--\s*\{' . preg_quote($starting_tag, '/') . '\}\s*-->(.*?)<!--\s*\{' . preg_quote($ending_tag, '/') . '\}\s*-->/s';
        if (preg_match($pattern, $text, $matches)) {
            return trim($matches[1]);
        }
        return '';
    }
    protected function remove_content_between_tags(string $text, string $starting_tag, string $ending_tag): string
    {
        $pattern = '/<!--\s*\{' . preg_quote($starting_tag, '/') . '\}\s*-->.*?<!--\s*\{' . preg_quote($ending_tag, '/') . '\}\s*-->/s';
        return preg_replace($pattern, '', $text);
    }
    protected function find_column_by_shortcode(string $html, string $shortcode): ?int
    {
        $pattern = '/<!--\s*\{ColumnStart(\d+)\}.*?\{([^}]+)\}.*?<!--\s*\{ColumnEnd\1\}\s*-->/s';
        if (preg_match_all($pattern, $html, $matches)) {
            foreach ($matches[2] as $index => $content) {
                if ($content === $shortcode) {
                    return (int) $matches[1][$index];
                }
            }
        }
        return null;
    }
}
