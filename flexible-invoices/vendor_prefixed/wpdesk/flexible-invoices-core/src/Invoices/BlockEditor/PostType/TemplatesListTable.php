<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\PostType;

class TemplatesListTable extends \WP_List_Table
{
    private array $table_data;
    private const BULK_ACTION_DELETE = 'bulk_delete';
    public function prepare_items()
    {
        $this->handle_duplicate();
        $this->handle_bulk_actions();
        $this->table_data = $this->get_table_data();
        $data = [];
        foreach ($this->table_data as $post) {
            $data[] = ['id' => $post->ID, 'name' => (empty($post->post_title) ? $this->get_edit_link($post->ID, __('(no title)', 'flexible-invoices')) : $this->get_edit_link($post->ID, $post->post_title)) . ' ' . $this->get_user_friendly_status($post->ID), 'status' => $this->get_toggle_button($post->ID, get_post_meta($post->ID, 'template_enabled', \true))];
        }
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = [];
        $this->_column_headers = [$columns, $hidden, $sortable];
        $this->items = $data;
    }
    private function get_user_friendly_status(int $template_id): string
    {
        $post_status = get_post_status($template_id);
        switch ($post_status) {
            case 'draft':
                return __('(draft)', 'flexible-invoices');
            case 'private':
                return __('(private)', 'flexible-invoices');
            default:
                return '';
        }
    }
    public function extra_tablenav($which)
    {
        if ('top' === $which) {
            ?>
			<div class="alignleft actions">
				<a href="<?php 
            echo esc_url(admin_url('post-new.php?post_type=' . TemplatesPostType::POST_TYPE_SLUG));
            ?>" class="button button-primary"><?php 
            esc_html_e('Add New', 'flexible-invoices');
            ?></a>
			</div>
			<?php 
            wp_nonce_field('fi_bulk_delete_templates_action', 'fi_security_nonce');
        }
    }
    public function column_name($item)
    {
        $actions = ['edit' => $this->get_edit_link($item['id'], __('Edit', 'flexible-invoices')), 'delete' => $this->get_delete_link($item['id'], __('Delete', 'flexible-invoices')), 'view' => $this->get_preview_link($item['id'], __('Preview', 'flexible-invoices')), 'duplicate' => $this->get_duplicate_link($item['id'], __('Duplicate', 'flexible-invoices'))];
        if (count($this->table_data) <= 1) {
            unset($actions['delete']);
        }
        return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions, \true));
    }
    public function get_columns()
    {
        $columns = ['cb' => '<input type="checkbox" />', 'name' => __('Name', 'flexible-invoices'), 'status' => __('Enabled', 'flexible-invoices')];
        return $columns;
    }
    public function column_default($item, $column_name)
    {
        return $item[$column_name];
    }
    public function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="fi_template[]" value="%s" />', $item['id']);
    }
    public function get_bulk_actions()
    {
        return [self::BULK_ACTION_DELETE => __('Delete', 'flexible-invoices')];
    }
    public function handle_bulk_actions()
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have permission to perform this action.', 'flexible-invoices'));
        }
        if (!isset($_REQUEST['action'])) {
            return;
        }
        $action = sanitize_text_field(wp_unslash($_REQUEST['action']));
        if (self::BULK_ACTION_DELETE !== $action) {
            return;
        }
        check_admin_referer('fi_bulk_delete_templates_action', 'fi_security_nonce');
        $post_ids = isset($_REQUEST['fi_template']) ? wp_unslash($_REQUEST['fi_template']) : [];
        //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        if (!is_array($post_ids) || empty($post_ids)) {
            return;
        }
        foreach ($post_ids as $post_id) {
            $is_enabled = filter_var(get_post_meta($post_id, TemplatesPostType::ENABLED_TEMPLATE_OPTION_KEY, \true), \FILTER_VALIDATE_BOOLEAN);
            $post_template = (string) get_post_type($post_id);
            if ($is_enabled || $post_template !== TemplatesPostType::POST_TYPE_SLUG) {
                continue;
            }
            wp_delete_post($post_id, \true);
        }
    }
    public function handle_duplicate()
    {
        if (!isset($_GET['action']) || $_GET['action'] !== 'duplicate') {
            return;
        }
        if (!isset($_GET['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['nonce'])), 'duplicate_template') || !current_user_can('manage_options')) {
            return;
        }
        if (!isset($_GET['post_id'])) {
            return;
        }
        $post_id = (int) $_GET['post_id'];
        $post = (array) get_post($post_id);
        unset($post['ID']);
        // translators: %s - post title
        $post['post_title'] = sprintf(__('Copy of %s', 'flexible-invoices'), $post['post_title']);
        wp_insert_post($post);
        wp_safe_redirect(remove_query_arg(['action', 'post_id', 'nonce']));
        exit;
    }
    /**
     * @return int[]|\WP_Post[]
     */
    private function get_table_data()
    {
        return get_posts(['post_type' => TemplatesPostType::POST_TYPE_SLUG, 'posts_per_page' => -1, 'post_status' => ['publish', 'draft', 'private']]);
    }
    /**
     * Buttons rendering
     */
    private function get_edit_link(int $post_id, string $label): string
    {
        return sprintf('<a href="%s">%s</a>', get_edit_post_link($post_id), $label);
    }
    private function get_preview_link(int $post_id, $label): string
    {
        return sprintf('<a href="%s">%s</a>', get_permalink($post_id), $label);
    }
    private function get_delete_link(int $post_id, $label): string
    {
        return sprintf('<a href="%s">%s</a>', add_query_arg(['nonce' => wp_create_nonce('delete_template')], get_delete_post_link($post_id)), $label);
    }
    private function get_duplicate_link(int $post_id, $label): string
    {
        $duplicate_url = add_query_arg(['post_id' => $post_id, 'action' => 'duplicate', 'nonce' => wp_create_nonce('duplicate_template')], admin_url('/edit.php?post_type=inspire_invoice&page=invoices_settings&tab=invoice-template'));
        return sprintf('<a href="%s">%s</a>', $duplicate_url, $label);
    }
    private function get_toggle_button(int $post_id, bool $enabled): string
    {
        return '<input type="checkbox" class="fi-block-template-toggle fi-toggle-button" data-post-id="' . esc_attr((string) $post_id) . '" ' . checked($enabled, \true, \false) . ' />';
    }
}
