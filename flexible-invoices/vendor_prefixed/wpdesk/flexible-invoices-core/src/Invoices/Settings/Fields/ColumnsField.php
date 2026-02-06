<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields;

use WPDeskFIVendor\WPDesk\Forms\Field\BasicField;
class ColumnsField extends BasicField
{
    public function get_template_name(): string
    {
        return 'reports/columns';
    }
    public function set_options($options)
    {
        $this->meta['possible_values'] = $options;
        return $this;
    }
    public function set_active_columns(array $columns): self
    {
        $this->meta['active_columns'] = $columns;
        return $this;
    }
}
