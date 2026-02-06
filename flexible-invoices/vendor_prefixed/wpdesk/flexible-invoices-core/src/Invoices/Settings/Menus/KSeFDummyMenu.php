<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Menus;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Tabs\Reports\KSeFDummyTab;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\RegisterPostType;
class KSeFDummyMenu extends GeneralSettingsMenu
{
    public const SETTINGS_SLUG = 'ksef-settings';
    protected function get_settings_slug(): string
    {
        return self::SETTINGS_SLUG;
    }
    public function hooks()
    {
        if (!$this->should_display_ksef_menu()) {
            return;
        }
        add_action('admin_menu', function () {
            add_submenu_page(RegisterPostType::POST_TYPE_MENU_URL, esc_html__('KSeF', 'flexible-invoices'), esc_html__('KSeF', 'flexible-invoices'), 'manage_options', self::SETTINGS_SLUG, [$this, 'render_page_action'], 10);
        });
        add_action('admin_init', [$this, 'save_settings_action'], 5);
        add_action('admin_notices', [$this, 'show_settings_saved_notice']);
    }
    public function render_page_action()
    {
        $tab = $this->get_active_tab();
        $renderer = $this->get_renderer();
        $tab->output_render($renderer);
        $renderer->output_render('footer');
    }
    private function should_display_ksef_menu(): bool
    {
        $is_pl_locale = get_locale() === 'pl_PL';
        $is_pl_store = \false;
        if (function_exists('WC') && WC()->countries instanceof \WC_Countries) {
            $is_pl_store = WC()->countries->get_base_country() === 'PL';
        }
        $should_display = $is_pl_locale || $is_pl_store;
        return apply_filters('fi/core/ksef/should_display_menu', $should_display);
    }
    protected function get_settings_tabs(): array
    {
        static $tabs = [];
        $tabs[KSeFDummyTab::get_tab_slug()] = new KSeFDummyTab();
        return apply_filters('fi/core/ksef/tabs', $tabs);
    }
}
