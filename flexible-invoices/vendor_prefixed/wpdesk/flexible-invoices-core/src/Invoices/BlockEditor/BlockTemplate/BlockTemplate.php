<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\BlockTemplate;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\PostType\TemplatesPostType;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\BlockTemplateEditor;
class BlockTemplate
{
    private int $template_id;
    public function __construct(int $template_id = 0)
    {
        if ($template_id === 0) {
            $template_id = BlockTemplateEditor::get_active_template_id();
        }
        $this->template_id = $template_id;
    }
    public function get_next_invoice_number(string $document_type): int
    {
        $meta = $this->get_meta($document_type . '_' . TemplatesPostType::NEXT_INVOICE_NUMBER_META);
        return !empty($meta) ? $meta : 1;
    }
    public function set_next_invoice_number(int $number, string $document_type): void
    {
        update_post_meta($this->template_id, $document_type . '_' . TemplatesPostType::NEXT_INVOICE_NUMBER_META, $number);
    }
    public function get_template_content(): string
    {
        return apply_filters('the_content', get_post_field('post_content', $this->template_id));
    }
    public function get_reset_type(string $document_type): string
    {
        $reset_type = $this->get_meta($document_type . '_' . TemplatesPostType::NUMBER_RESET_TYPE_META);
        return empty($reset_type) ? 'year' : $reset_type;
    }
    public function get_reset_time(string $document_type): int
    {
        $meta = $this->get_meta($document_type . '_' . TemplatesPostType::NUMBER_RESET_TIME_META);
        return !empty($meta) ? $meta : strtotime(current_time('mysql'));
    }
    public function set_reset_time(int $time, string $document_type)
    {
        update_post_meta($this->template_id, $document_type . '_' . TemplatesPostType::NUMBER_RESET_TIME_META, $time);
    }
    public function get_number_prefix(string $document_type): string
    {
        return $this->get_meta($document_type . '_' . TemplatesPostType::NUMBER_PREFIX_META);
    }
    public function get_number_suffix(string $document_type): string
    {
        return $this->get_meta($document_type . '_' . TemplatesPostType::NUMBER_SUFFIX_META);
    }
    public function is_page_numbering_enabled(): bool
    {
        return $this->get_meta(TemplatesPostType::PAGE_NUMBERING_META);
    }
    public function should_remove_recipient_block(): bool
    {
        return $this->get_meta(TemplatesPostType::WOOCOMMERCE_SHIPPING);
    }
    public function get_font_family(): string
    {
        $font_family = $this->get_meta(TemplatesPostType::DOCUMENT_FONT_META);
        return empty($font_family) ? 'dejavusanscondensed' : $font_family;
    }
    public function is_hide_seller_vat_enabled(): bool
    {
        return $this->get_meta(TemplatesPostType::HIDE_TAX_NUMBER_META);
    }
    public function is_hide_tax_cells_enabled(): bool
    {
        return $this->get_meta(TemplatesPostType::HIDE_TAX_CELLS_META);
    }
    private function get_meta(string $key, bool $single = \true)
    {
        return get_post_meta($this->template_id, $key, $single);
    }
}
