<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields;

use WPDeskFIVendor\WPDesk\Forms\Field;
use WPDeskFIVendor\WPDesk\Forms\Field\BasicField;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\Plugin;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\InvoicesIntegration;
/**
 * Disable field adapter.
 *
 * @package WPDesk\FIT\Settings\Fields
 */
class DisableTemplateFieldAdapter
{
    /**
     * @var Field\BasicField
     */
    private $field;
    /**
     * @var string
     */
    private $name;
    /**
     * @var bool
     */
    private $show_link;
    /**
     * @param string $name
     * @param BasicField $field
     * @param bool $show_pro_link
     */
    public function __construct(string $name, Field\BasicField $field, bool $show_pro_link = \false)
    {
        $this->name = $name;
        $this->show_link = $show_pro_link;
        $this->field = $field;
    }
    public function get_field()
    {
        $field_description = '';
        if ($this->field->has_description()) {
            $field_description = $this->field->get_description();
        }
        if (Plugin::is_template_addon_is_disabled()) {
            $this->field->set_disabled();
            $upgrade_link = '';
            if ($this->show_link) {
                $upgrade_pro_url = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/sk/flexible-invoices-disabled-pro-pl' : 'https://www.flexibleinvoices.com/sk/flexible-invoices-disabled-pro-en';
                $upgrade_link = '<span class="pro-url"><a href="' . esc_url($upgrade_pro_url) . '" target="_blank">' . esc_html__('Upgrade to PRO &rarr;', 'flexible-invoices') . '</a></span>';
            }
            if ($field_description) {
                $this->field->set_description($field_description . '<br/>' . $upgrade_link);
            } else {
                $this->field->set_description($field_description . $upgrade_link);
            }
            return $this->field;
        }
        $this->field->set_name($this->name);
        if ($field_description) {
            $this->field->set_description($field_description);
        }
        return $this->field;
    }
}
