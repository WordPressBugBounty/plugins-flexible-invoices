<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Decorators\TemplateDocumentDecorator;
class PaymentStatusReplacer extends AbstractBasicReplacer
{
    protected function get_wrapper_tags(): array
    {
        return ['start' => 'PaymentStatusBlockBegin', 'end' => 'PaymentStatusBlockEnd'];
    }
    public function modify_content(TemplateDocumentDecorator $invoice, string $content): string
    {
        $shortcodes = $this->get_shortcodes_to_replace($invoice);
        return $this->replace_shortcodes($shortcodes, $content);
    }
    protected function get_shortcodes_to_replace(TemplateDocumentDecorator $invoice): array
    {
        return ['{PaymentStatus}' => $invoice->get_payment_status_name()];
    }
}
