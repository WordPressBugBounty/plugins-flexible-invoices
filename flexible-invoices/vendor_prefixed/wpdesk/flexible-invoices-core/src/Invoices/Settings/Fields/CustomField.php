<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields;

use WPDeskFIVendor\WPDesk\Forms\Field\BasicField;
class CustomField extends BasicField
{
    private string $content = '';
    public function get_template_name()
    {
        return 'custom-field';
    }
    public function get_content(): string
    {
        return $this->content;
    }
    public function set_content(string $content): self
    {
        $this->content = $content;
        return $this;
    }
}
