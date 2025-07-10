<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\BlockTemplate\SampleTemplates\SampleTemplateInserter;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\PostType\TemplatesPostType;
class BlockTemplateEditor
{
    public static function get_active_template_id(): int
    {
        $args = ['post_type' => TemplatesPostType::POST_TYPE_SLUG, 'post_status' => 'any', 'meta_query' => [
            //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
            ['key' => 'template_enabled', 'value' => '1'],
        ], 'posts_per_page' => 1, 'fields' => 'ids'];
        $template_query = new \WP_Query($args);
        $template_ids = $template_query->posts;
        return !empty($template_ids) ? (int) $template_ids[0] : 0;
    }
    public static function is_block_template_editor_active(): bool
    {
        return filter_var(get_option('inspire_invoices_gutenberg_editor', \false), \FILTER_VALIDATE_BOOLEAN);
    }
    public static function activate_first_template()
    {
        $args = ['post_type' => TemplatesPostType::POST_TYPE_SLUG, 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids'];
        $template_query = new \WP_Query($args);
        if (!$template_query->have_posts()) {
            delete_option(SampleTemplateInserter::ALREADY_ADDED_OPTION_KEY);
            return;
        }
        $template_ids = $template_query->posts;
        self::activate_template($template_ids[0]);
    }
    public static function activate_template(int $template_id): void
    {
        update_post_meta($template_id, TemplatesPostType::ENABLED_TEMPLATE_OPTION_KEY, \true);
    }
    public static function is_template(int $template_id): bool
    {
        return get_post_type($template_id) === TemplatesPostType::POST_TYPE_SLUG && get_post_status($template_id) === 'publish';
    }
}
