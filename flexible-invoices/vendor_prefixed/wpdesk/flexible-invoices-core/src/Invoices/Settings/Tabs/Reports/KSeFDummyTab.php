<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Tabs\Reports;

use WPDeskFIVendor\WPDesk\Forms\Field\Header;
use WPDeskFIVendor\WPDesk\Forms\Field\InputTextField;
use WPDeskFIVendor\WPDesk\Forms\Field\SelectField;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\InvoicesIntegration;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Tabs\FieldSettingsTab;
class KSeFDummyTab extends FieldSettingsTab
{
    private const KSEF_DUMMY_FIELD_NAME = 'dummy-field';
    private const KSEF_REPO_URL = 'plugin-install.php?tab=plugin-information&plugin=ksef-for-flexible-invoices&TB_iframe=true&width=600&height=550';
    private const KSEF_PRO_URL_EN = 'https://wpdesk.link/ksef-fi-pro-en';
    private const KSEF_PRO_URL_PL = 'https://wpdesk.link/ksef-to-fi-pro';
    protected function get_fields()
    {
        $ksef_tab_description = $this->get_tab_description();
        $fields = [(new Header())->set_label(esc_html__('KSeF', 'flexible-invoices'))->set_description($ksef_tab_description), (new SelectField())->set_name(self::KSEF_DUMMY_FIELD_NAME)->set_label(esc_html__('Environtment', 'flexible-invoices'))->set_options(['test' => esc_html__('Test', 'flexible-invoices'), 'demo' => esc_html__('Pre-prod', 'flexible-invoices'), 'prod' => esc_html__('Prod', 'flexible-invoices')])->set_disabled()->set_default_value(''), (new InputTextField())->set_name(self::KSEF_DUMMY_FIELD_NAME)->set_label(esc_html__('Token', 'flexible-invoices'))->set_description(__('Paste the token allowing integration with KSEF. See how to do it <a href="">here</a>. Connection status: ', 'flexible-invoices'))->set_disabled()->add_class('hs-beacon-search'), (new SelectField())->set_name(self::KSEF_DUMMY_FIELD_NAME)->set_label(__('VAT exempt rate', 'flexible-invoices'))->set_description(__('If you are exempt from VAT, select from the list (otherwise, the system may send, for example, the VAT-exempt rate).', 'flexible-invoices'))->set_options([])->set_disabled()->add_class(self::KSEF_DUMMY_FIELD_NAME), (new SelectField())->set_name(self::KSEF_DUMMY_FIELD_NAME)->set_label(__('0% rate', 'flexible-invoices'))->set_description(__('If you are using a 0% rate, select it from the list (otherwise, the system may send, for example, the VAT-exempt rate).', 'flexible-invoices'))->set_options([])->set_disabled()];
        return $fields;
    }
    public static function get_tab_slug()
    {
        return 'ksef';
    }
    public function get_tab_name()
    {
        return esc_html__('KSeF', 'flexible-invoices');
    }
    private function get_tab_description(): string
    {
        if (InvoicesIntegration::is_super()) {
            $description = sprintf(
                // translators: 1. Open a tag with href. Ready for url. 4. Finish a tag with class and target, break line
                esc_html__('Integrate your WooCommerce with KSeF in just a few moments using the %1$sKSeF for WooCommerce Invoices%2$s plugin to work with the Flexible Invoices PRO.%3$s', 'flexible-invoices'),
                '<a href="' . esc_url(admin_url(self::KSEF_REPO_URL)) . '">',
                '</a>',
                '<br/><hr/>'
            );
        } else {
            $pro_version = get_locale() === 'pl_PL' ? self::KSEF_PRO_URL_PL : self::KSEF_PRO_URL_EN;
            $description = sprintf(
                // translators: 1. Open a tag with href. Ready for url. 4. Finish a tag with class and target, break line
                esc_html__('Integrate your WooCommerce with KSeF in just a few moments using the %1$sKSeF for WooCommerce Invoices%2$s plugin to work with the free version of Flexible Invoices. KSeF Addon also works with %3$sFlexible Invoices PRO%2$s - automate your invoicing!%4$s', 'flexible-invoices'),
                '<a href="' . esc_url(admin_url(self::KSEF_REPO_URL)) . '">',
                '</a>',
                '<a href="' . esc_url($pro_version) . '" target=_blank>',
                '<br/><hr/>'
            );
        }
        return $description;
    }
}
