<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\PDF;

use Exception;
use WPDeskFIVendor\Mpdf\Mpdf;
use WPDeskFIVendor\Mpdf\MpdfException;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\Documents\Document;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\BlockTemplate\BlockTemplate;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\PostType\TemplatesPostType;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Decorators\TemplateDocumentDecorator;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Documents\DummyInvoices\DummyInvoice;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\TemplateRenderers\BlockEditorTemplateRenderer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\TemplateRenderers\TemplateRendererInterface;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\PDF;
class BlockPDFGenerator extends PDF
{
    private int $block_template_id = 0;
    public function hooks()
    {
        parent::hooks();
        add_action('template_redirect', [$this, 'maybe_render_template']);
    }
    protected function maybe_enable_page_numbering(Mpdf $mpdf): void
    {
        $block_template = new BlockTemplate($this->block_template_id);
        if ($block_template->is_page_numbering_enabled()) {
            $this->enable_page_numbering($mpdf);
        }
    }
    public function maybe_render_template()
    {
        if (isset($_GET[TemplatesPostType::PREVIEW_TEMPLATE_ARG]) && (current_user_can('manage_options') || current_user_can('manage_woocommerce'))) {
            //phpcs:ignore
            $template_id = sanitize_text_field(wp_unslash($_GET[TemplatesPostType::PREVIEW_TEMPLATE_ARG]));
            //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            /**
             * Add filter to overwrite all other filters trying to
             * dynamically change the template id as preview mustn't apply to those rules.
             */
            $template_id = add_filter('fi/core/blocks/invoice_template', static function () use ($template_id) {
                return $template_id;
            }, 999);
            $this->generate_template_preview((int) $template_id);
            //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        }
    }
    protected function get_template_renderer(Document $document): TemplateRendererInterface
    {
        $block_template = new BlockTemplate($document->get_template_id());
        return new BlockEditorTemplateRenderer($this->library_info->get_assets_url(), $block_template);
    }
    /**
     * @param int $template_id
     *
     * @return void
     * @throws MpdfException
     * @throws Exception
     */
    private function generate_template_preview(int $template_id)
    {
        $this->block_template_id = $template_id;
        try {
            $template = get_post($template_id);
            if (!$template) {
                throw new Exception(esc_html__('This template doesn\'t exist or was deleted.', 'flexible-invoices'));
            }
            $dummy_invoice = new DummyInvoice();
            $invoice = new TemplateDocumentDecorator($dummy_invoice, $this->strategy);
            $name = str_replace(['/', ' '], ['_', '_'], $invoice->get_formatted_number()) . '.pdf';
            header('Content-type: application/pdf');
            header('Content-Disposition: inline; filename="' . $name . '"');
            $pdf_data = $this->get_as_string($invoice);
            echo $pdf_data;
            //phpcs:ignore
            exit;
        } catch (Exception $e) {
            wp_die($e->getMessage());
            //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
}
