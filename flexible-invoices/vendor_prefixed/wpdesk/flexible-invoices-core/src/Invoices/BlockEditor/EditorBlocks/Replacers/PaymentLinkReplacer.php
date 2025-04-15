<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Decorators\TemplateDocumentDecorator;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\WooCommerce;
class PaymentLinkReplacer extends AbstractBasicReplacer
{
    protected function get_wrapper_tags(): array
    {
        return ['start' => 'PaymentLinkBlockBegin', 'end' => 'PaymentLinkBlockEnd'];
    }
    public function modify_content(TemplateDocumentDecorator $invoice, string $content): string
    {
        if ($this->should_remove_element_from_document($invoice)) {
            return $this->remove_whole_element($content);
        }
        return $content;
    }
    private function should_remove_element_from_document(TemplateDocumentDecorator $invoice): bool
    {
        if (!WooCommerce::is_active()) {
            return \true;
        }
        $order_id = $invoice->get_order_id();
        $order = wc_get_order($order_id);
        if (!$order) {
            return \true;
        }
        if (!$order->is_paid()) {
            return \false;
        }
        return \true;
    }
}
