<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Integration\DocumentNumbers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\Documents\Document;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\BlockTemplate\BlockTemplate;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Settings;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\Translator;
/**
 * This class define document number for each document types.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Integration
 */
class BlockTemplateDocumentNumber extends DocumentNumber
{
    private BlockTemplate $block_template;
    private string $document_type;
    public function __construct(Settings $settings, Document $document, BlockTemplate $block_template, string $name = 'Document')
    {
        $this->block_template = $block_template;
        $this->document_type = $document->get_type();
        parent::__construct($settings, $document, $name);
        $this->prefix = $block_template->get_number_prefix($this->document_type);
        $this->suffix = $block_template->get_number_suffix($this->document_type);
        $this->document_number = $this->get_document_number();
    }
    protected function get_number_from_option(): int
    {
        return $this->block_template->get_next_invoice_number($this->document_type);
    }
    protected function get_number_array_for_formatting(): array
    {
        return [Translator::translate($this->prefix), $this->document_number, Translator::translate($this->suffix)];
    }
    protected function update_number(int $value)
    {
        $this->block_template->set_next_invoice_number($value, $this->document_type);
    }
    protected function update_start_number_timestamp(int $value): void
    {
        $this->block_template->set_reset_time($value, $this->document_type);
    }
    protected function get_reset_type(): string
    {
        return $this->block_template->get_reset_type($this->document_type);
    }
    protected function get_reset_time(): int
    {
        return $this->block_template->get_reset_time($this->document_type);
    }
}
