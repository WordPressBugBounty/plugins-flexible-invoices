<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields;

use WPDeskFIVendor\WPDesk\Forms\Field\Header;
/**
 * Field to close table.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Settings\Fields
 */
class SubEndField extends Header
{
    public function __construct()
    {
        parent::__construct();
        $this->set_default_value('');
        $this->set_attribute('type', 'text');
    }
    public function get_template_name(): string
    {
        return 'sub-end';
    }
}
