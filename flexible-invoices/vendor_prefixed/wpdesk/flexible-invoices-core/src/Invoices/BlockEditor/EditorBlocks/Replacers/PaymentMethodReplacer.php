<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Decorators\TemplateDocumentDecorator;
class PaymentMethodReplacer extends AbstractBasicReplacer
{
    protected function get_wrapper_tags(): array
    {
        return ['start' => 'PaymentBlockBegin', 'end' => 'PaymentBlockEnd'];
    }
    protected function get_shortcodes_to_replace(TemplateDocumentDecorator $invoice): array
    {
        return ['{PaymentMethod}' => $invoice->get_payment_method_name()];
    }
    public function modify_content(TemplateDocumentDecorator $invoice, string $content): string
    {
        $shortcodes = $this->get_shortcodes_to_replace($invoice);
        return $this->replace_shortcodes($shortcodes, $content);
    }
}
