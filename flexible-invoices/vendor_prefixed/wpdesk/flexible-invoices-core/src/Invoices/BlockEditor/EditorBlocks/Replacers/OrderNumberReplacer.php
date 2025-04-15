<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\ValueObjects\DocumentCustomer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\ValueObjects\DocumentSeller;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Decorators\TemplateDocumentDecorator;
class OrderNumberReplacer extends AbstractBasicReplacer
{
    protected function get_wrapper_tags(): array
    {
        return ['start' => 'OrderNumberBlockBegin', 'end' => 'OrderNumberBlockEnd'];
    }
    protected function get_shortcodes_to_replace(TemplateDocumentDecorator $invoice): array
    {
        return ['{OrderNumber}' => $invoice->get_order_id()];
    }
    public function modify_content(TemplateDocumentDecorator $invoice, string $content): string
    {
        $shortcodes = $this->get_shortcodes_to_replace($invoice);
        return $this->replace_shortcodes($shortcodes, $content);
    }
}
