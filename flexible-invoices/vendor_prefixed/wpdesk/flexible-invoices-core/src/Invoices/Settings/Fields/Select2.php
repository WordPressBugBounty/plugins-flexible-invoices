<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields;

use WPDeskFIVendor\WPDesk\Forms\Field\SelectField;
class Select2 extends SelectField
{
    public function __construct()
    {
        $this->add_class('wc-enhanced-select');
    }
    /**
     * @return string
     */
    public function get_template_name(): string
    {
        return 'reports/select2';
    }
}
