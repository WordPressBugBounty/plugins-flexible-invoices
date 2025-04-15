<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Decorators\TemplateDocumentDecorator;
class PriceSummaryReplacer extends AbstractBasicReplacer
{
    protected function get_wrapper_tags(): array
    {
        return ['start' => 'PriceSummaryBlockBegin', 'end' => 'PriceSummaryBlockEnd'];
    }
    protected function get_shortcodes_to_replace(TemplateDocumentDecorator $invoice): array
    {
        $totals = $invoice->array_to_string_as_money(['total_amount' => $invoice->get_total_gross(), 'paid_amount' => $invoice->get_total_paid(), 'due_amount' => $invoice->get_total_gross() - $invoice->get_total_paid()]);
        return ['{TotalAmount}' => $totals['total_amount'], '{PaidAmount}' => $totals['paid_amount'], '{DueAmount}' => $totals['due_amount']];
    }
    public function modify_content(TemplateDocumentDecorator $invoice, string $content): string
    {
        $shortcodes = $this->get_shortcodes_to_replace($invoice);
        return $this->replace_shortcodes($shortcodes, $content);
    }
}
