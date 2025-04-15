<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Decorators\TemplateDocumentDecorator;
class DatesReplacer extends AbstractBasicReplacer
{
    protected function get_wrapper_tags(): array
    {
        return ['start' => 'DatesBlockBegin', 'end' => 'DatesBlockEnd'];
    }
    protected function get_shortcodes_to_replace(TemplateDocumentDecorator $invoice): array
    {
        return ['{IssueDate}' => $invoice->get_date_of_issue(), '{PayDate}' => $invoice->get_date_of_pay(), '{SaleDate}' => $invoice->get_date_of_sale()];
    }
    public function modify_content(TemplateDocumentDecorator $invoice, string $content): string
    {
        $shortcodes = $this->get_shortcodes_to_replace($invoice);
        return $this->replace_shortcodes($shortcodes, $content);
    }
}
