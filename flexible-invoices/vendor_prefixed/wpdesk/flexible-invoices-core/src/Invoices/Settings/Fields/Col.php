<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields;

use WPDeskFIVendor\WPDesk\Forms\Field\BasicField;
/**
 * Template col.
 *
 * @package WPDesk\FIT\Settings\Fields
 */
class Col extends BasicField
{
    public function get_template_name(): string
    {
        return 'col';
    }
}
