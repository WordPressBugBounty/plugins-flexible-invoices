<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\ValueObjects\DocumentCustomer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\ValueObjects\DocumentSeller;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Decorators\TemplateDocumentDecorator;
class NotesReplacer extends AbstractBasicReplacer
{
    protected function get_wrapper_tags(): array
    {
        return ['start' => 'NotesBlockBegin', 'end' => 'NotesBlockEnd'];
    }
    protected function get_shortcodes_to_replace(TemplateDocumentDecorator $invoice): array
    {
        return ['{Notes}' => $invoice->get_notes()];
    }
    public function modify_content(TemplateDocumentDecorator $invoice, string $content): string
    {
        $shortcodes = $this->get_shortcodes_to_replace($invoice);
        return $this->replace_shortcodes($shortcodes, $content);
    }
}
