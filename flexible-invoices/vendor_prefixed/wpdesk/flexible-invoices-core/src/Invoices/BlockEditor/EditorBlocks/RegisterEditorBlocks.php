<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\InvoicesIntegration;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\LibraryInfo;
use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class RegisterEditorBlocks implements Hookable
{
    public const POST_TYPE_SLUG = 'fi_template';
    public const BLOCKS_NAMESPACE = 'flexible-invoices';
    private string $library_dir;
    private string $plugin_dir;
    private array $blocks;
    public function __construct(LibraryInfo $library_info)
    {
        $this->library_dir = $library_info->get_library_dir();
        $this->plugin_dir = $library_info->get_plugin_dir();
        $this->blocks = apply_filters('fi/core/blocks/block_list', ['company', 'customer', 'dates', 'logo', 'notes', 'number', 'order-number', 'payment', 'payment-link', 'payment-status', 'price-summary', 'recipient', 'row', 'signature', 'table-products', 'table-summary', 'text', 'wrapper']);
        if (InvoicesIntegration::is_super()) {
            $this->blocks[] = 'table-of-conversion';
        }
    }
    public function hooks()
    {
        add_filter('allowed_block_types_all', [$this, 'filter_gutenberg_blocks'], 10, 2);
        add_filter('block_categories_all', [$this, 'register_block_category']);
        add_action('init', [$this, 'register_blocks']);
    }
    public function register_block_category($categories)
    {
        $categories[] = ['slug' => 'flexible-invoices', 'title' => 'Flexible Invoices'];
        return $categories;
    }
    public function filter_gutenberg_blocks($allowed_block_types, $editor_context)
    {
        $post_type = null;
        if (isset($editor_context->post) && is_object($editor_context->post)) {
            $post_type = $editor_context->post->post_type;
        }
        if ($post_type === self::POST_TYPE_SLUG) {
            $fi_blocks = array_map(function ($block) {
                return self::BLOCKS_NAMESPACE . '/' . $block;
            }, $this->blocks);
            return apply_filters('fi/core/blocks/allowed_block_types', $fi_blocks);
        }
        return $allowed_block_types;
    }
    public function register_blocks()
    {
        foreach ($this->blocks as $id) {
            $block = register_block_type($this->library_dir . 'assets/js/blocks/' . $id);
            $block_script_handles = array_merge($block->editor_script_handles, $block->script_handles, $block->view_script_handles);
            foreach ($block_script_handles as $script_handle) {
                wp_set_script_translations($script_handle, 'flexible-invoices', $this->plugin_dir . 'lang/');
            }
        }
    }
}
