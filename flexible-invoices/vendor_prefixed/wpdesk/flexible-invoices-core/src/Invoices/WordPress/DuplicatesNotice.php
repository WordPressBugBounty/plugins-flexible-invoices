<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\DuplicationChecker;
use WPDeskFIVendor\WPDesk\Notice\Notice;
use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Show notice for administrator if the documents have the same number or title.
 */
class DuplicatesNotice implements Hookable
{
    private DuplicationChecker $duplication_checker;
    public function __construct(DuplicationChecker $duplication_checker)
    {
        $this->duplication_checker = $duplication_checker;
    }
    public function hooks()
    {
        add_action('save_post', [$this, 'check_for_duplicates'], 10, 2);
        add_action('admin_init', [$this, 'add_duplicates_notice_error']);
    }
    public function check_for_duplicates($post_id, $post)
    {
        $this->duplication_checker->check_for_duplicates($post_id, $post);
    }
    public function add_duplicates_notice_error()
    {
        if ($this->duplication_checker->has_duplicates()) {
            new Notice(wp_kses_post(sprintf(
                // Translators: %s url.
                __('<strong>Warning!</strong> There are documents with the same number in the invoice list. Check <a href="%s">here</a>.', 'flexible-invoices'),
                admin_url('edit.php?post_type=' . RegisterPostType::POST_TYPE_NAME . '&filter=show_duplicated')
            )), Notice::NOTICE_TYPE_ERROR, \true);
            do_action('fi/core/duplicates', \true);
        }
    }
}
