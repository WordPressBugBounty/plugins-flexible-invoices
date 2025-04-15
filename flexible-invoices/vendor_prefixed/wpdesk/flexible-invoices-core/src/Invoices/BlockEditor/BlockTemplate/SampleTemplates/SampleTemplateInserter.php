<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\BlockTemplate\SampleTemplates;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\PostType\TemplatesPostType;
use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDeskFIVendor\WPDesk\View\Renderer\Renderer;
class SampleTemplateInserter implements Hookable
{
    private const DEFAULT_POST_STATUS = 'draft';
    private const ALREADY_ADDED_OPTION_KEY = 'sample_fi_templates_posts_added';
    private Renderer $renderer;
    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }
    public function hooks()
    {
        add_action('init', function () {
            if (!get_option(self::ALREADY_ADDED_OPTION_KEY)) {
                $this->add_sample_posts();
                update_option(self::ALREADY_ADDED_OPTION_KEY, \true);
            }
        });
    }
    private function add_sample_posts()
    {
        $sample_posts = $this->get_sample_templates();
        $document_types = ['invoice', 'correction', 'proforma'];
        foreach ($sample_posts as $sample) {
            $post_id = wp_insert_post(['post_title' => $sample->get_title(), 'post_content' => $sample->get_content(), 'post_type' => TemplatesPostType::POST_TYPE_SLUG, 'post_status' => self::DEFAULT_POST_STATUS]);
            if ($post_id && !is_wp_error($post_id)) {
                //@phpstan-ignore-line
                foreach ($document_types as $document_type) {
                    update_post_meta($post_id, $document_type . '_' . TemplatesPostType::NEXT_INVOICE_NUMBER_META, $sample->get_next_invoice_number());
                    update_post_meta($post_id, $document_type . '_' . TemplatesPostType::NUMBER_PREFIX_META, $sample->get_prefix($document_type));
                    update_post_meta($post_id, $document_type . '_' . TemplatesPostType::NUMBER_SUFFIX_META, $sample->get_suffix());
                    update_post_meta($post_id, $document_type . '_' . TemplatesPostType::NUMBER_RESET_TYPE_META, $sample->get_reset_type());
                    update_post_meta($post_id, $document_type . '_' . TemplatesPostType::DEFAULT_DUE_META, $sample->get_default_due_time());
                }
            }
        }
    }
    private function get_sample_templates(): array
    {
        $universal_title = __('Sample template', 'flexible-invoices');
        $universal_invoice_prefix = __('Invoice', 'flexible-invoices');
        $universal_proforma_prefix = __('Proforma', 'flexible-invoices');
        $universal_correction_prefix = __('Correction', 'flexible-invoices');
        return [new SampleTemplate($universal_title . ' #1', $this->get_sample_content('sample-1'), $universal_invoice_prefix, $universal_proforma_prefix, $universal_correction_prefix), new SampleTemplate($universal_title . ' #2', $this->get_sample_content('sample-2'), $universal_invoice_prefix, $universal_proforma_prefix, $universal_correction_prefix), new SampleTemplate($universal_title . ' #3', $this->get_sample_content('sample-3'), $universal_invoice_prefix, $universal_proforma_prefix, $universal_correction_prefix), new SampleTemplate($universal_title . ' #4', $this->get_sample_content('sample-4'), $universal_invoice_prefix, $universal_proforma_prefix, $universal_correction_prefix), new SampleTemplate($universal_title . ' #5', $this->get_sample_content('sample-5'), $universal_invoice_prefix, $universal_proforma_prefix, $universal_correction_prefix)];
    }
    private function get_sample_content(string $template): string
    {
        return $this->renderer->render('block-editor/sample-templates/' . $template);
    }
}
