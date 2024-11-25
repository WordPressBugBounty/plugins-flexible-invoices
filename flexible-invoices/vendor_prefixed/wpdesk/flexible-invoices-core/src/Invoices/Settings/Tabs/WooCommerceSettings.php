<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Tabs;

use WPDeskFIVendor\WPDesk\Forms\Field;
use WPDeskFIVendor\WPDesk\Forms\Field\NoOnceField;
use WPDeskFIVendor\WPDesk\Forms\Field\SubmitField;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\SettingsForm;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\WooCommerceFields;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\WooCommerceFields\SubTabInterface;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\SettingsStrategy\SettingsStrategy;
/**
 * General Settings Tab Page.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Settings\Tabs
 */
final class WooCommerceSettings extends FieldSettingsTab
{
    /**
     * @var array
     */
    private $form_fields = [];
    public function __construct()
    {
        $this->set_sub_tab_forms();
    }
    /**
     * Set document fields form.
     */
    private function set_sub_tab_forms()
    {
        /**
         * @var WooCommerceFields\SubTabInterface[] $settings
         */
        $woocommerce_tabs = ['general' => new WooCommerceFields\GeneralSettingsFields(), 'checkout' => new WooCommerceFields\CheckoutSettingsFields(), 'moss' => new WooCommerceFields\MossSettingsFields()];
        /**
         * Definitions of settings for WooCommerce tab.
         *
         * @param SubTabInterface[] $woocommerce_tabs WooCommerce's settings tab.
         *
         * @return array
         *
         * @since 1.2.0
         */
        $settings = (array) apply_filters('fi/core/settings/tabs/woocommerce', $woocommerce_tabs);
        foreach ($settings as $setting) {
            if ($setting instanceof WooCommerceFields\SubTabInterface) {
                $this->form_fields[$setting::get_tab_slug()] = $setting->get_fields();
            }
        }
        $fields = [(new NoOnceField(SettingsForm::NONCE_ACTION))->set_name(SettingsForm::NONCE_NAME), (new SubmitField())->set_name('save')->set_label(esc_html__('Save changes', 'flexible-invoices'))->add_class('button-primary')];
        $this->form_fields[] = $fields;
    }
    /**
     * @return array|Field[]
     */
    public function get_fields(): array
    {
        $fields = [];
        foreach ($this->form_fields as $form) {
            foreach ($form as $field) {
                $fields[] = $field;
            }
        }
        return $fields;
    }
    /**
     * @return string
     */
    public static function get_tab_slug(): string
    {
        return 'woocommerce';
    }
    /**
     * @return string
     */
    public function get_tab_name(): string
    {
        return esc_html__('WooCommerce', 'flexible-invoices');
    }
}
