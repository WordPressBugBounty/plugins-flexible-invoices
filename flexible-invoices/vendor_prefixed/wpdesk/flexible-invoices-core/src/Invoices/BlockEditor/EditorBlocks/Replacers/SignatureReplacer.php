<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\ValueObjects\DocumentCustomer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\ValueObjects\DocumentSeller;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Decorators\TemplateDocumentDecorator;
class SignatureReplacer extends AbstractBasicReplacer
{
    protected function get_wrapper_tags(): array
    {
        return ['start' => 'SignatureBlockBegin', 'end' => 'SignatureBlockEnd'];
    }
    protected function get_shortcodes_to_replace(DocumentSeller $seller): array
    {
        return ['{Signature}' => $seller->get_signature_user()];
    }
    public function modify_content(TemplateDocumentDecorator $invoice, string $content): string
    {
        $shortcodes = $this->get_shortcodes_to_replace($invoice->get_seller());
        //@phpstan-ignore-line
        return $this->replace_shortcodes($shortcodes, $content);
    }
}
