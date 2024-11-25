<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Tabs;

use WPDeskFIVendor\WPDesk\Forms\Field;
use WPDeskFIVendor\WPDesk\Forms\Field\NoOnceField;
use WPDeskFIVendor\WPDesk\Forms\Field\SubmitField;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\WooCommerce;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\DocumentsFields;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\DocumentsFields\DocumentsFieldsInterface;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\SettingsForm;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\SettingsStrategy\SettingsStrategy;
/**
 * Document Settings Tab Page.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Settings\Tabs
 */
final class DocumentsSettings extends FieldSettingsTab
{
    /**
     * @var array
     */
    private $form_fields = [];
    /**
     * @var SettingsStrategy
     */
    private $strategy;
    /**
     * @param SettingsStrategy $strategy
     */
    public function __construct(SettingsStrategy $strategy)
    {
        $this->strategy = $strategy;
        $this->set_sub_tab_forms();
    }
    /**
     * Set document fields form.
     */
    private function set_sub_tab_forms()
    {
        /**
         * @var DocumentsFields\DocumentsFieldsInterface[] $settings
         */
        $documents_settings = ['invoice' => new DocumentsFields\InvoicesSettingsFields($this->strategy)];
        if (WooCommerce::is_active()) {
            $documents_settings['proforma'] = new DocumentsFields\ProformaSettingsFields($this->strategy);
            $documents_settings['correction'] = new DocumentsFields\CorrectionsSettingsFields();
        }
        /**
         * Definitions of settings for Documents tab.
         *
         * @param DocumentsFieldsInterface[] $documents_settings Documents settings tab.
         *
         * @return array
         *
         * @since 1.2.0
         */
        $settings = (array) apply_filters('fi/core/settings/tabs/documents', $documents_settings);
        foreach ($settings as $setting) {
            if ($setting instanceof DocumentsFields\DocumentsFieldsInterface) {
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
        return 'documents';
    }
    /**
     * @return string
     */
    public function get_tab_name(): string
    {
        return esc_html__('Documents', 'flexible-invoices');
    }
}
