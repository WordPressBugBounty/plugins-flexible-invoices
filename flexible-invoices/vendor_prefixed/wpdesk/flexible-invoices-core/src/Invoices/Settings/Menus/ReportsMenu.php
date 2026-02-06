<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Menus;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Tabs\Reports\KSeFDummyTab;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Tabs\Reports\ReportTab;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\RegisterPostType;
class ReportsMenu extends GeneralSettingsMenu
{
    public const SETTINGS_SLUG = 'flexible-invoices-reports-settings';
    public const NONCE_ACTION = 'download_report';
    public const NONCE_NAME = 'report_download';
    protected function get_settings_slug(): string
    {
        return self::SETTINGS_SLUG;
    }
    public function hooks()
    {
        add_action('admin_menu', function () {
            add_submenu_page(RegisterPostType::POST_TYPE_MENU_URL, esc_html__('Reports', 'flexible-invoices'), esc_html__('Reports', 'flexible-invoices'), 'manage_options', self::SETTINGS_SLUG, [$this, 'render_page_action'], 10);
        });
        add_action('admin_init', [$this, 'save_settings_action'], 5);
        add_action('admin_notices', [$this, 'show_settings_saved_notice']);
    }
    protected function get_settings_tabs(): array
    {
        static $tabs = [];
        $tabs[ReportTab::get_tab_slug()] = new ReportTab();
        return apply_filters('fi/core/reports/tabs', $tabs);
    }
}
