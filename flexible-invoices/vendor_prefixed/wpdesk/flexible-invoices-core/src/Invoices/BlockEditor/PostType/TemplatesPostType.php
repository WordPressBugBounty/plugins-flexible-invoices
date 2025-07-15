<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\PostType;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\BlockTemplateEditor;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\LibraryInfo;
use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class TemplatesPostType implements Hookable
{
    public const POST_TYPE_SLUG = 'fi_template';
    public const ENABLED_TEMPLATE_OPTION_KEY = 'template_enabled';
    public const PREVIEW_TEMPLATE_ARG = 'preview_fi_template';
    private const TOGGLE_TEMPLATE_NONCE = 'fi_toggle_template';
    public const INVOICE_PREFIX = 'invoice';
    public const PROFORMA_PREFIX = 'proforma';
    public const CORRECTION_PREFIX = 'correction';
    public const NEXT_INVOICE_NUMBER_META = 'next_number';
    public const NUMBER_RESET_TYPE_META = 'number_reset_type';
    public const NUMBER_PREFIX_META = 'number_prefix';
    public const NUMBER_SUFFIX_META = 'number_suffix';
    public const DEFAULT_DUE_META = 'default_due_time';
    public const NOTES_META = 'notes';
    public const NUMBER_RESET_TIME_META = 'number_reset_time';
    public const HIDE_TAX_NUMBER_META = 'hide_tax_number';
    public const DOCUMENT_FONT_META = 'document_font';
    public const HIDE_TAX_CELLS_META = 'hide_tax_cells';
    private const WELCOME_DISMISS_META = 'welcome_dismissed';
    public const PAGE_NUMBERING_META = 'enable_page_numbering';
    public const WOOCOMMERCE_SHIPPING = 'woocommerce_shipping_address';
    public const FLUSH_REWRITE_RULES_OPTION = 'fi_templatept_rewrite_rules_flushed';
    private string $lib_url;
    private string $plugin_path;
    private string $lib_version;
    public function __construct(LibraryInfo $library_info)
    {
        $this->lib_url = $library_info->get_library_url();
        $this->plugin_path = $library_info->get_plugin_dir();
        $this->lib_version = $library_info->get_plugin_version();
    }
    public function hooks()
    {
        add_action('init', [$this, 'register_post_type']);
        add_action('init', [$this, 'register_meta_fields']);
        add_action('init', [$this, 'register_editor_assets']);
        add_action('init', [$this, 'maybe_flush_rewrite_rules'], 20);
        add_action('admin_init', [$this, 'redirect_user_from_legacy_post_type_page']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets']);
        add_filter('preview_post_link', [$this, 'edit_view_post_type_link'], 10, 2);
        add_filter('post_type_link', [$this, 'edit_view_post_type_link'], 10, 2);
        add_action('wp_ajax_fi_toggle_template_enabled', [$this, 'toggle_template_enabled']);
    }
    /**
     * Flush rewrite rules to prevent "404" after deactivating plugin.
     */
    public function maybe_flush_rewrite_rules()
    {
        if (!get_option(self::FLUSH_REWRITE_RULES_OPTION)) {
            flush_rewrite_rules();
            update_option(self::FLUSH_REWRITE_RULES_OPTION, \true);
        }
    }
    /**
     * Don't allow user to get into the legacy post type page, redirect him to fi settings with custom invoice table instead
     */
    public function redirect_user_from_legacy_post_type_page()
    {
        global $pagenow;
        if ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'fi_template') {
            //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            wp_safe_redirect(admin_url('edit.php?post_type=inspire_invoice&page=invoices_settings&tab=invoice-template'));
            exit;
        }
    }
    /**
     * @param string $preview_link
     * @param \WP_Post $post
     *
     * @return string
     */
    public function edit_view_post_type_link($preview_link, $post)
    {
        if ($post->post_type === self::POST_TYPE_SLUG) {
            return add_query_arg(self::PREVIEW_TEMPLATE_ARG, $post->ID, $preview_link);
        }
        return $preview_link;
    }
    public function enqueue_admin_scripts()
    {
        wp_enqueue_script('fi-template-delete-handler', $this->lib_url . '/assets/js/admin/template-delete-handler.js', ['jquery'], $this->lib_version, \true);
        wp_enqueue_script('fi-template-editor-toggler', $this->lib_url . '/assets/js/admin/template-toggle-manager.js', ['jquery'], $this->lib_version, \true);
        wp_localize_script('fi-template-editor-toggler', 'fiInvoicesCore', ['ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce(self::TOGGLE_TEMPLATE_NONCE)]);
        wp_enqueue_style('fi-template-editor-toggler-styles', $this->lib_url . '/assets/css/admin/template-toggle.css', [], $this->lib_version);
    }
    public function register_editor_assets()
    {
        wp_register_script('fi-template-editor-metabox', $this->lib_url . 'assets/js/blocks/post-meta/template-settings/index.js', ['wp-plugins', 'wp-edit-post', 'wp-components', 'wp-data', 'wp-element', 'wp-api-fetch', 'wp-i18n'], $this->lib_version, \true);
        wp_set_script_translations('fi-template-editor-metabox', 'flexible-invoices', $this->plugin_path . 'lang/');
    }
    public function enqueue_editor_assets()
    {
        global $post;
        if (isset($post) && $post->post_type === self::POST_TYPE_SLUG) {
            wp_enqueue_script('fi-template-editor-metabox');
            wp_enqueue_style('fi-template-editor-metabox-style', $this->lib_url . 'assets/js/blocks/post-meta/template-settings/index.css', [], $this->lib_version);
            wp_enqueue_style('fitb-editor-styles', $this->lib_url . 'assets/css/blocks/editor-style.css', [], $this->lib_version);
            wp_enqueue_style('fitb-editor-fonts', $this->lib_url . 'assets/css/blocks/editor-fonts.css', [], $this->lib_version);
        }
    }
    public function register_meta_fields()
    {
        $meta_datas = [['type' => 'integer', 'name' => self::NEXT_INVOICE_NUMBER_META, 'default' => 1], ['type' => 'string', 'name' => self::NUMBER_RESET_TYPE_META, 'default' => 'month'], ['type' => 'string', 'name' => self::NUMBER_SUFFIX_META, 'default' => '/{MM}/{YYYY}'], ['type' => 'integer', 'name' => self::DEFAULT_DUE_META, 'default' => 7], ['type' => 'string', 'name' => self::NOTES_META, 'default' => '']];
        $prefixes = [self::INVOICE_PREFIX, self::PROFORMA_PREFIX, self::CORRECTION_PREFIX];
        // Register all meta fields that are common to documents (notes, suffix, reset type etc.)
        $this->register_common_meta($prefixes, $meta_datas);
        register_post_meta(self::POST_TYPE_SLUG, $prefixes[0] . '_' . self::NUMBER_PREFIX_META, ['type' => 'string', 'single' => \true, 'show_in_rest' => \true, 'default' => __('Invoice', 'flexible-invoices')]);
        register_post_meta(self::POST_TYPE_SLUG, $prefixes[1] . '_' . self::NUMBER_PREFIX_META, ['type' => 'string', 'single' => \true, 'show_in_rest' => \true, 'default' => __('Proforma', 'flexible-invoices')]);
        register_post_meta(self::POST_TYPE_SLUG, $prefixes[2] . '_' . self::NUMBER_PREFIX_META, ['type' => 'string', 'single' => \true, 'show_in_rest' => \true, 'default' => __('Correction', 'flexible-invoices')]);
        register_post_meta(self::POST_TYPE_SLUG, self::PAGE_NUMBERING_META, ['type' => 'boolean', 'single' => \true, 'show_in_rest' => \true, 'default' => \false]);
        register_post_meta(self::POST_TYPE_SLUG, self::WOOCOMMERCE_SHIPPING, ['type' => 'boolean', 'single' => \true, 'show_in_rest' => \true, 'default' => \false]);
        register_post_meta(self::POST_TYPE_SLUG, self::WELCOME_DISMISS_META, ['type' => 'boolean', 'single' => \true, 'show_in_rest' => \true, 'default' => \false]);
        register_post_meta(self::POST_TYPE_SLUG, self::HIDE_TAX_NUMBER_META, ['type' => 'boolean', 'single' => \true, 'show_in_rest' => \true, 'default' => \false]);
        register_post_meta(self::POST_TYPE_SLUG, self::HIDE_TAX_CELLS_META, ['type' => 'boolean', 'single' => \true, 'show_in_rest' => \true, 'default' => \false]);
        register_post_meta(self::POST_TYPE_SLUG, self::DOCUMENT_FONT_META, ['type' => 'string', 'single' => \true, 'show_in_rest' => \true, 'default' => 'dejavusans']);
    }
    private function register_common_meta(array $prefixes, array $meta_datas): void
    {
        foreach ($prefixes as $prefix) {
            foreach ($meta_datas as $meta_data) {
                register_post_meta(self::POST_TYPE_SLUG, $prefix . '_' . $meta_data['name'], ['type' => $meta_data['type'], 'single' => \true, 'show_in_rest' => \true, 'default' => $meta_data['default']]);
            }
        }
    }
    public function register_post_type()
    {
        $labels = ['name' => _x('Templates', 'Post type general name', 'flexible-invoices'), 'singular_name' => _x('Templates', 'Post type singular name', 'flexible-invoices'), 'menu_name' => _x('Templates', 'Admin Menu text', 'flexible-invoices'), 'name_admin_bar' => _x('Templates', 'Add New on Toolbar', 'flexible-invoices'), 'add_new' => __('Add New', 'flexible-invoices'), 'add_new_item' => __('Add New', 'flexible-invoices'), 'new_item' => __('New Template', 'flexible-invoices'), 'edit_item' => __('Edit Template', 'flexible-invoices'), 'view_item' => __('View Template', 'flexible-invoices'), 'all_items' => __('Templates', 'flexible-invoices'), 'search_items' => __('Search Templates', 'flexible-invoices')];
        $args = ['labels' => $labels, 'public' => \true, 'show_in_rest' => \true, 'publicly_queryable' => \true, 'show_ui' => \true, 'show_in_menu' => \false, 'query_var' => \true, 'capability_type' => 'post', 'has_archive' => \true, 'hierarchical' => \false, 'supports' => ['title', 'editor', 'custom-fields']];
        register_post_type(self::POST_TYPE_SLUG, $args);
    }
    public function toggle_template_enabled()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have permission to perform this action.', 'flexible-invoices'));
        }
        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'] ?? '')), self::TOGGLE_TEMPLATE_NONCE)) {
            wp_send_json_error(__('Invalid nonce.', 'flexible-invoices'));
        }
        if (!isset($_POST['post_id'])) {
            wp_send_json_error(__('Missing post id.', 'flexible-invoices'));
        }
        $post_id = (int) sanitize_key($_POST['post_id']);
        BlockTemplateEditor::activate_template($post_id);
        $query = new \WP_Query(['post_type' => self::POST_TYPE_SLUG, 'post__not_in' => [$post_id], 'posts_per_page' => -1, 'fields' => 'ids']);
        foreach ($query->posts as $post_id) {
            //@phpstan-ignore-line
            update_post_meta($post_id, self::ENABLED_TEMPLATE_OPTION_KEY, \false);
        }
        wp_reset_postdata();
        wp_send_json_success();
    }
}
