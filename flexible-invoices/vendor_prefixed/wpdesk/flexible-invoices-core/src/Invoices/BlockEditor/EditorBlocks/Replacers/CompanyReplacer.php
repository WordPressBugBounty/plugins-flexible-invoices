<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\ValueObjects\DocumentSeller;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Decorators\TemplateDocumentDecorator;
class CompanyReplacer extends AbstractBasicReplacer
{
    protected function get_wrapper_tags(): array
    {
        return ['start' => 'CompanyBlockBegin', 'end' => 'CompanyBlockEnd'];
    }
    protected function get_shortcodes_to_replace(TemplateDocumentDecorator $invoice): array
    {
        $seller = $invoice->get_seller();
        return ['{CompanyName}' => $seller->get_name(), '{CompanyAddress}' => $seller->get_address(), '{CompanyBankAccount}' => $seller->get_bank_account_number(), '{CompanyBankName}' => $seller->get_bank_name(), '{CompanyVAT}' => $seller->get_vat_number()];
    }
    public function modify_content(TemplateDocumentDecorator $invoice, string $content): string
    {
        if ($this->should_remove_element_from_document($invoice)) {
            return $this->remove_whole_element($content);
        }
        $shortcodes = $this->get_shortcodes_to_replace($invoice);
        if ($this->should_remove_company_tax($invoice)) {
            $shortcodes['{CompanyVAT}'] = '';
        }
        return $this->replace_shortcodes($shortcodes, $content);
    }
    private function should_remove_company_tax(TemplateDocumentDecorator $invoice): bool
    {
        $empty_tax = !$invoice->get_total_tax();
        return $empty_tax && $this->block_template->is_hide_seller_vat_enabled();
    }
    private function should_remove_element_from_document(TemplateDocumentDecorator $invoice): bool
    {
        /**
         * @var DocumentSeller $seller
         */
        $seller = $invoice->get_seller();
        return $this->are_all_fields_empty($seller);
    }
    private function are_all_fields_empty(DocumentSeller $seller): bool
    {
        if (empty($seller->get_name()) && empty($seller->get_vat_number()) && empty($seller->get_address()) && empty($seller->get_bank_name()) && empty($seller->get_bank_account_number())) {
            return \true;
        }
        return \false;
    }
}
