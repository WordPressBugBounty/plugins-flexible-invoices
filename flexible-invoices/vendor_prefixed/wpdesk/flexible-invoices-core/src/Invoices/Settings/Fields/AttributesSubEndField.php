<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields;

/**
 * Attribute tab close field.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Settings\Fields
 */
class AttributesSubEndField extends SubEndField
{
    public function get_template_name(): string
    {
        return 'attributes-sub-end';
    }
}
