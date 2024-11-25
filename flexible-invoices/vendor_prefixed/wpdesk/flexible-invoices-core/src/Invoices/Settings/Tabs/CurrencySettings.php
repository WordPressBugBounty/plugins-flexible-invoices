<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Tabs;

use WPDeskFIVendor\WPDesk\Forms\Field\Header;
use WPDeskFIVendor\WPDesk\Forms\Field\NoOnceField;
use WPDeskFIVendor\WPDesk\Forms\Field\Paragraph;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Configs\Currency;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\WooCommerce;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields\TableGroupedFields;
use WPDeskFIVendor\WPDesk\Forms\Field\InputTextField;
use WPDeskFIVendor\WPDesk\Forms\Field\SelectField;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\SettingsForm;
final class CurrencySettings extends FieldSettingsTab
{
    const CURRENCY_GROUP = 'currency';
    const CURRENCY_FIELD = 'currency';
    const CURRENCY_POSITION_FIELD = 'currency_position';
    const THOUSAND_SEPARATOR_FIELD = 'thousand_separator';
    const FIELD_DECIMAL_SEP_FIELD = 'decimal_separator';
    /**
     * @return array|\WPDesk\Forms\Field[]
     */
    protected function get_fields(): array
    {
        $invoice_beacon = 'Currencies for invoicing in WordPress';
        if (WooCommerce::is_active()) {
            $invoice_beacon = 'Currencies for invoicing in WooCommerce';
        }
        return [(new TableGroupedFields())->set_name(self::CURRENCY_GROUP)->set_items([(new SelectField())->set_name(self::CURRENCY_FIELD)->add_class('currency hs-beacon-search ')->set_options(Currency::get_currencies_options())->set_attribute('data-beacon_search', $invoice_beacon), (new SelectField())->set_name(self::CURRENCY_POSITION_FIELD)->add_class('currency-position hs-beacon-search ')->set_options(Currency::get_currency_position_options())->set_attribute('data-beacon_search', $invoice_beacon), (new InputTextField())->set_name(self::THOUSAND_SEPARATOR_FIELD)->add_class('thousand-separator hs-beacon-search')->set_attribute('minlength', 0)->set_attribute('maxlength', 1)->set_default_value(',')->set_attribute('data-beacon_search', $invoice_beacon), (new InputTextField())->set_name(self::FIELD_DECIMAL_SEP_FIELD)->add_class('decimal-separator hs-beacon-search')->set_attribute('minlength', 0)->set_attribute('maxlength', 1)->set_default_value('.')->set_attribute('data-beacon_search', $invoice_beacon)]), (new NoOnceField(SettingsForm::NONCE_ACTION))->set_name(SettingsForm::NONCE_NAME)];
    }
    /**
     * @return string
     */
    public static function get_tab_slug(): string
    {
        return 'currency';
    }
    /**
     * @return string
     */
    public function get_tab_name(): string
    {
        return esc_html__('Currencies', 'flexible-invoices');
    }
}
