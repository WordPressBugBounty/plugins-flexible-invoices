<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields;

use WPDeskFIVendor\WPDesk\Forms\Field\NoValueField;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\PostType\TemplatesListTable;
/**
 * Field to close table.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Settings\Fields
 */
class TemplateListField extends NoValueField
{
    public function __construct()
    {
        parent::__construct();
        $table = new TemplatesListTable();
        ob_start();
        $table->prepare_items();
        $table->display();
        $content = ob_get_clean();
        $this->set_attribute('table-content', $content);
    }
    public function get_template_name(): string
    {
        return 'post-table-list';
    }
    public function should_override_form_template(): bool
    {
        return \true;
    }
}
