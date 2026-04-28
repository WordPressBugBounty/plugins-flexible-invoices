<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Decorators\TemplateDocumentDecorator;
class NotesReplacer extends AbstractBasicReplacer
{
    protected function get_wrapper_tags(): array
    {
        return ['start' => 'NotesBlockBegin', 'end' => 'NotesBlockEnd'];
    }
    protected function get_shortcodes_to_replace(TemplateDocumentDecorator $invoice): array
    {
        $notes = $invoice->get_notes();
        $filters = '';
        //We have to catch ob content as it is action and not a filter :(
        if ($invoice->get_type() === 'invoice') {
            ob_start();
            do_action('fi/core/template/invoice/after_notes', $invoice);
            $filters = ob_get_contents();
            ob_end_clean();
        } elseif ($invoice->get_type() === 'proforma') {
            ob_start();
            do_action('fi/core/template/proforma/after_notes', $invoice);
            $filters = ob_get_contents();
            ob_end_clean();
        }
        return ['{Notes}' => $notes . $filters];
    }
    public function modify_content(TemplateDocumentDecorator $invoice, string $content): string
    {
        $shortcodes = $this->get_shortcodes_to_replace($invoice);
        return $this->replace_shortcodes($shortcodes, $content);
    }
}
