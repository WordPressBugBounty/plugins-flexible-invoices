<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields;

use WPDeskFIVendor\WPDesk\Forms\Field\BasicField;
/**
 * Template row.
 *
 * @package WPDesk\FIT\Settings\Fields
 */
class Row extends BasicField
{
    /**
     * @var string
     */
    private $row_type;
    public function __construct($is_open = \true)
    {
        if ($is_open) {
            $row_type = 'open';
        } else {
            $row_type = 'close';
        }
        $this->row_type = $row_type;
    }
    public function get_template_name(): string
    {
        return 'row-' . $this->row_type;
    }
}
