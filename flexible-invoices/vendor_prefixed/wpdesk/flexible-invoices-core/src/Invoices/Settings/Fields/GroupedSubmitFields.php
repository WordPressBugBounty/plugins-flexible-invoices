<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields;

use WPDeskFIVendor\WPDesk\Forms\Field;
use WPDeskFIVendor\WPDesk\Forms\Field\BasicField;
class GroupedSubmitFields extends BasicField
{
    /**
     * @var Field[]
     */
    private array $grouped_fields;
    public function __construct()
    {
        $this->set_default_value('');
        $this->set_attribute('type', 'text');
    }
    /**
     * @param array $fields
     *
     * @return GroupedSubmitFields
     */
    public function set_grouped_fields(array $fields): self
    {
        $this->grouped_fields = $fields;
        return $this;
    }
    /**
     * @return Field[]
     */
    public function get_grouped_fields(): array
    {
        return $this->grouped_fields;
    }
    public function get_template_name(): string
    {
        return 'reports/grouped-submit-fields';
    }
}
