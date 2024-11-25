<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields;

use WPDeskFIVendor\WPDesk\Forms\Field;
use WPDeskFIVendor\WPDesk\Forms\Field\BasicField;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\Translator;
/**
 * This decorator adds a disabled property for a field if WPML is active.
 * Value for this field can be translated in WPML String Translation.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Settings\Fields
 */
class WPMLFieldDecorator
{
    /**
     * @var BasicField
     */
    private $field;
    public function __construct(BasicField $field)
    {
        $this->field = $field;
    }
    /**
     * @param string $textdomain
     *
     * @return Field
     */
    public function get_field()
    {
        if (Translator::is_default_language()) {
            return $this->field;
        } else {
            $this->field->set_disabled();
            return $this->field;
        }
    }
}
