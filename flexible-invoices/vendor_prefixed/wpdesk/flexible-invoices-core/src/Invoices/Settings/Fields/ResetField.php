<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields;

use WPDeskFIVendor\WPDesk\Forms\Field\SubmitField;
/**
 * Reset settings field.
 *
 * @package WPDesk\FIT\Settings\Fields
 */
class ResetField extends SubmitField
{
    /**
     * @return string
     */
    //@phpstan-ignore-next-line
    public function get_type()
    {
        return 'button';
    }
}
