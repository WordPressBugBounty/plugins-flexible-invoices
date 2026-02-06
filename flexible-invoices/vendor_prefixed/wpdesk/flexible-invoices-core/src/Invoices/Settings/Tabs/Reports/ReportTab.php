<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Tabs\Reports;

use WPDeskFIVendor\WPDesk\Forms\Field\DateField;
use WPDeskFIVendor\WPDesk\Forms\Field\NoOnceField;
use WPDeskFIVendor\WPDesk\Forms\Field\SelectField;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields\FixedSubmitField;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields\GroupedFields;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Tabs\FieldSettingsTab;
use WPDeskFIVendor\WPDesk\Notice\Notice;
use WPDeskFIVendor\WPDesk\View\Renderer\Renderer;
class ReportTab extends FieldSettingsTab
{
    const NONCE_ACTION = 'download_report';
    const NONCE_NAME = 'report_download';
    protected function get_fields()
    {
        return [(new GroupedFields())->set_name('grouped_field')->set_grouped_fields([(new DateField())->set_name('start_date')->set_label(esc_html__('From:', 'flexible-invoices'))->add_class('medium-text hs-beacon-search')->set_default_value(date('Y-m-d', strtotime('NOW - 1 months')))->set_attribute('data-beacon_search', 'Reports'), (new DateField())->set_name('end_date')->set_label(esc_html__('To:', 'flexible-invoices'))->add_class('medium-text hs-beacon-search')->set_default_value(date('Y-m-d'))->set_attribute('data-beacon_search', 'Reports')]), (new GroupedFields())->set_name('grouped_field')->set_grouped_fields([(new SelectField())->set_name('currency')->set_label(esc_html__('Currency:', 'flexible-invoices'))->set_options($this->get_currencies())]), (new FixedSubmitField())->set_name('download_report')->set_label(esc_html__('Generate', 'flexible-invoices'))->add_class('button-primary'), (new NoOnceField(self::NONCE_ACTION))->set_name(self::NONCE_NAME)];
    }
    public function render(Renderer $renderer)
    {
        new Notice($renderer->render('../wordpress/advanced-reports-ad', []), Notice::NOTICE_TYPE_SUCCESS, \true);
        // translators: %1$s - open tag, %2$s - close tag
        $docs_description = sprintf('%2$s%1$s%3$s', sprintf(esc_html__('Read more in the %1$splugin documentation &rarr;%2$s', 'flexible-invoices'), '<a href="' . $this->get_docs_url() . '" target="_blank" style="color: #4BB04E; font-weight: 700;">', '</a>'), '<strong>', '</strong>');
        $content = '<div class="wrap"><h1 class="wp-heading-inline">' . esc_html__('Reports', 'flexible-invoices') . '</h1>';
        $content .= '<div class="support-url-wrapper">' . $docs_description . '</div>';
        $content .= '<hr class="wp-header-end">';
        $content .= $renderer->render('form-start', ['form' => $this, 'method' => 'POST', 'action' => '']);
        $content .= $this->render_fields($renderer);
        $content .= $renderer->render('form-end');
        $content .= '</div>';
        return $content;
    }
    public function render_fields(Renderer $renderer): string
    {
        $content = '';
        foreach ($this->get_fields() as $field) {
            $content .= $renderer->render($field->should_override_form_template() ? $field->get_template_name() : 'form-field', ['field' => $field, 'renderer' => $renderer, 'name_prefix' => self::get_tab_slug(), 'value' => '', 'template_name' => $field->get_template_name()]);
        }
        return $content;
    }
    public function get_method(): string
    {
        return 'POST';
    }
    public function get_action(): string
    {
        return admin_url('admin-ajax.php?action=fiw_generate_report');
    }
    private function get_docs_url(): string
    {
        $url = 'https://flexibleinvoices.com/sk/flexible-invoices-report-docs-en';
        if (get_locale() === 'pl_PL') {
            $url = 'https://www.wpdesk.pl/sk/flexible-invoices-report-docs-pl';
        }
        return $url;
    }
    public function output_render(Renderer $renderer): void
    {
        echo $this->render($renderer);
        //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    private function get_currencies(): array
    {
        $currencies_options = [];
        $currencies = get_option('inspire_invoices_currency', []);
        foreach ($currencies as $currency) {
            $currencies_options[$currency['currency']] = $currency['currency'];
        }
        return $currencies_options;
    }
    public static function get_tab_slug()
    {
        return 'reports';
    }
    public function get_tab_name()
    {
        return esc_html__('Reports', 'flexible-invoices');
    }
}
