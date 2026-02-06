<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields;

/**
 * Attribute tab open field.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Settings\Fields
 */
class AttributesSubStartField extends SubStartField
{
    public function get_template_name(): string
    {
        return 'attributes-sub-start';
    }
}
