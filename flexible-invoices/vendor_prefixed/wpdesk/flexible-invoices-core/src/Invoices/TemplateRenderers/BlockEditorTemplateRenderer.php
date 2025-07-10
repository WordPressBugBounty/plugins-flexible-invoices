<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\TemplateRenderers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers\CompanyReplacer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers\CustomerReplacer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers\DatesReplacer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers\DocumentNumberReplacer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers\NotesReplacer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers\OrderNumberReplacer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers\PaymentLinkReplacer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers\PaymentMethodReplacer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers\PaymentStatusReplacer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers\PriceSummaryReplacer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers\ProductTableReplacer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers\RecipientReplacer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers\SignatureReplacer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers\SummaryTableReplacer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Decorators\TemplateDocumentDecorator;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\BlockTemplateEditor;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\BlockTemplate\BlockTemplate;
class BlockEditorTemplateRenderer implements TemplateRendererInterface
{
    private string $assets_url;
    private BlockTemplate $block_template;
    private array $block_replacers;
    public function __construct(string $assets_url, BlockTemplate $block_template)
    {
        $this->assets_url = $assets_url;
        $this->block_template = $block_template;
        $this->block_replacers = apply_filters('fi/core/blocks/replacers', ['customer' => new CustomerReplacer(), 'payment-link' => new PaymentLinkReplacer(), 'recipient' => (new RecipientReplacer())->set_block_template($block_template), 'company' => (new CompanyReplacer())->set_block_template($block_template), 'dates' => new DatesReplacer(), 'number' => new DocumentNumberReplacer(), 'notes' => new NotesReplacer(), 'order-number' => new OrderNumberReplacer(), 'price-summary' => new PriceSummaryReplacer(), 'payment' => new PaymentMethodReplacer(), 'payment-status' => new PaymentStatusReplacer(), 'signature' => new SignatureReplacer(), 'table-products' => (new ProductTableReplacer())->set_block_template($block_template), 'table-summary' => new SummaryTableReplacer()]);
    }
    private function get_font_family_styles(BlockTemplate $template): string
    {
        $font_family = $template->get_font_family();
        return 'body{font-family: ' . $font_family . ';}';
    }
    private function get_table_paddings(): string
    {
        return '.fi-row table th, .fi-row table td{padding:6px 5px;}';
    }
    private function get_inbuilt_styles(): string
    {
        return file_get_contents($this->assets_url . 'css/blocks/block-pdf.css');
    }
    private function get_document_styles(BlockTemplate $template): string
    {
        $styles = $this->get_inbuilt_styles();
        $styles .= $this->get_font_family_styles($template);
        $styles .= $this->get_table_paddings();
        return '<style>' . $styles . '</style>';
    }
    public function render(array $data): string
    {
        $invoice_atts = $data['invoice_atts'];
        /**
         * @var TemplateDocumentDecorator $invoice
         */
        $invoice = $invoice_atts['invoice'];
        $template_html = $this->block_template->get_template_content();
        $template_html .= $this->get_document_styles($this->block_template);
        $final_html = $this->prepare_invoice_html($template_html, $invoice);
        $final_html = preg_replace('/rgba\((\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*)\)/i', 'rgba($1, 1)', $final_html);
        return $final_html;
    }
    private function prepare_invoice_html(string $template_html, TemplateDocumentDecorator $invoice_atts): string
    {
        return $this->replace_shortcodes($template_html, $invoice_atts);
    }
    private function replace_shortcodes(string $template_html, TemplateDocumentDecorator $invoice_atts): string
    {
        $invoice_html = $template_html;
        foreach ($this->block_replacers as $replacer) {
            $invoice_html = $replacer->modify_content($invoice_atts, $invoice_html, $this->block_template);
        }
        return $invoice_html;
    }
}
