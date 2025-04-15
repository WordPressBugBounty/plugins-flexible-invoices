<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\ValueObjects\DocumentCustomer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Decorators\TemplateDocumentDecorator;
class CustomerReplacer extends AbstractBasicReplacer
{
    protected function get_wrapper_tags(): array
    {
        return ['start' => 'CustomerBlockBegin', 'end' => 'CustomerBlockEnd'];
    }
    protected function get_shortcodes_to_replace(TemplateDocumentDecorator $invoice): array
    {
        $client = $invoice->get_customer();
        return ['{CustomerName}' => $client->get_name(), '{CustomerStreet}' => $client->get_street(), '{CustomerCity}' => $client->get_city(), '{CustomerPostalCode}' => $client->get_postcode(), '{CustomerCountry}' => $client->get_country(), '{CustomerVAT}' => $client->get_vat_number(), '{CustomerEmail}' => $client->get_email(), '{CustomerPhone}' => $client->get_phone()];
    }
    public function modify_content(TemplateDocumentDecorator $invoice, string $content): string
    {
        if ($this->should_remove_element_from_document($invoice)) {
            return $this->remove_whole_element($content);
        }
        $shortcodes = $this->get_shortcodes_to_replace($invoice);
        return $this->replace_shortcodes($shortcodes, $content);
    }
    private function should_remove_element_from_document(TemplateDocumentDecorator $invoice): bool
    {
        /**
         * @var DocumentCustomer $customer
         */
        $customer = $invoice->get_customer();
        return $this->are_all_fields_empty($customer);
    }
    private function are_all_fields_empty(DocumentCustomer $customer): bool
    {
        if (empty($customer->get_name()) && empty($customer->get_street()) && empty($customer->get_street2()) && empty($customer->get_city()) && empty($customer->get_postcode()) && empty($customer->get_country()) && empty($customer->get_email()) && empty($customer->get_phone()) && empty($customer->get_vat_number())) {
            return \true;
        }
        return \false;
    }
}
