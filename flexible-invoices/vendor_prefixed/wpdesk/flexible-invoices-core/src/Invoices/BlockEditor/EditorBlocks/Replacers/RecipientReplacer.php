<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\DocumentData\Customer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\DocumentData\Recipient;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Decorators\TemplateDocumentDecorator;
class RecipientReplacer extends AbstractBasicReplacer
{
    public function modify_content(TemplateDocumentDecorator $invoice, string $content): string
    {
        if ($this->should_remove_element_from_document($invoice) && $this->block_template->should_remove_recipient_block()) {
            return $this->remove_whole_element($content);
        }
        $shortcodes = $this->get_shortcodes_to_replace($invoice);
        return $this->replace_shortcodes($shortcodes, $content);
    }
    private function should_remove_element_from_document(TemplateDocumentDecorator $invoice): bool
    {
        $recipient = $invoice->get_recipient();
        if ($this->are_all_fields_empty($recipient)) {
            return \true;
        } elseif ($this->is_customer_and_recipient_the_same($recipient, $invoice->get_customer())) {
            return \true;
        }
        return \false;
    }
    protected function get_shortcodes_to_replace(TemplateDocumentDecorator $invoice): array
    {
        $recipient = $invoice->get_recipient();
        return ['{RecipientName}' => $recipient->get_name(), '{RecipientStreet}' => $recipient->get_street(), '{RecipientCity}' => $recipient->get_city(), '{RecipientPostalCode}' => $recipient->get_postcode(), '{RecipientCountry}' => $recipient->get_country(), '{RecipientVAT}' => $recipient->get_vat_number(), '{RecipientEmail}' => $recipient->get_email(), '{RecipientPhone}' => $recipient->get_phone()];
    }
    protected function get_wrapper_tags(): array
    {
        return ['start' => 'RecipientBlockBegin', 'end' => 'RecipientBlockEnd'];
    }
    private function are_all_fields_empty(Recipient $recipient): bool
    {
        if (empty($recipient->get_name()) && empty($recipient->get_street()) && empty($recipient->get_street2()) && empty($recipient->get_city()) && empty($recipient->get_postcode()) && empty($recipient->get_country()) && empty($recipient->get_email()) && empty($recipient->get_phone()) && empty($recipient->get_vat_number())) {
            return \true;
        }
        return \false;
    }
    private function is_customer_and_recipient_the_same(Recipient $recipient, Customer $customer): bool
    {
        return $recipient->get_name() === $customer->get_name() && $recipient->get_vat_number() === $customer->get_vat_number() && $recipient->get_street() === $customer->get_street() && $recipient->get_street2() === $customer->get_street2() && $recipient->get_city() === $customer->get_city() && $recipient->get_postcode() === $customer->get_postcode() && $recipient->get_country() === $customer->get_country();
    }
}
