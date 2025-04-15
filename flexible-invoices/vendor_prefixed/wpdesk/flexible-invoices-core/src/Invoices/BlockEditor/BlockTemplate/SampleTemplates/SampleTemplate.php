<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\BlockTemplate\SampleTemplates;

class SampleTemplate
{
    private string $title;
    private int $next_invoice_number;
    private string $reset_type;
    private string $content;
    private string $invoice_prefix;
    private string $proforma_prefix;
    private string $correction_prefix;
    private string $suffix;
    private int $default_due_time;
    public function __construct(string $title, string $content, string $invoice_prefix, string $proforma_prefix, string $correction_prefix, string $suffix = '/{MM}/{YYYY}', int $next_invoice_number = 1, string $reset_type = 'month', int $default_due_time = 7)
    {
        $this->title = $title;
        $this->next_invoice_number = $next_invoice_number;
        $this->content = $content;
        $this->reset_type = $reset_type;
        $this->invoice_prefix = $invoice_prefix;
        $this->proforma_prefix = $proforma_prefix;
        $this->correction_prefix = $correction_prefix;
        $this->suffix = $suffix;
        $this->default_due_time = $default_due_time;
    }
    public function get_title(): string
    {
        return $this->title;
    }
    public function get_next_invoice_number(): int
    {
        return $this->next_invoice_number;
    }
    public function get_content(): string
    {
        return $this->content;
    }
    public function get_reset_type(): string
    {
        return $this->reset_type;
    }
    public function get_prefix(string $document_type): string
    {
        switch ($document_type) {
            case 'proforma':
                return $this->proforma_prefix;
            case 'correction':
                return $this->correction_prefix;
            case 'invoice':
            default:
                return $this->invoice_prefix;
        }
    }
    public function get_suffix(): string
    {
        return $this->suffix;
    }
    public function get_default_due_time(): int
    {
        return $this->default_due_time;
    }
}
