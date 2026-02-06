<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields;

use WPDeskFIVendor\WPDesk\Forms\Field\BasicField;
class FixedSubmitField extends BasicField
{
    public function get_template_name(): string
    {
        return 'input-submit';
    }
    public function get_type(): string
    {
        return 'submit';
    }
    public function should_override_form_template(): bool
    {
        return \true;
    }
}
