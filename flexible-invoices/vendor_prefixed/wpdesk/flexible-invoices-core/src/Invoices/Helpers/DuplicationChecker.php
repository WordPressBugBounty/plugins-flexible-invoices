<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\RegisterPostType;
class DuplicationChecker
{
    private const TRANSIENT_NAME = 'flexible_invoices_duplicates';
    public function has_duplicates(): bool
    {
        return filter_var(get_transient(self::TRANSIENT_NAME), \FILTER_VALIDATE_BOOLEAN);
    }
    public function check_for_duplicates($post_id, $post)
    {
        if ($post->post_type === RegisterPostType::POST_TYPE_NAME) {
            $has_duplicates = $this->find_duplicates();
            if ($has_duplicates) {
                set_transient(self::TRANSIENT_NAME, 'yes', \MONTH_IN_SECONDS);
            } else {
                delete_transient(self::TRANSIENT_NAME);
            }
        }
    }
    public function invoice_with_title_exists(string $invoice_title): bool
    {
        global $wpdb;
        if ($invoice_title === '') {
            return \false;
        }
        $sql = "\n        SELECT 1\n        FROM {$wpdb->posts}\n        WHERE post_type  = %s\n          AND post_status = 'publish'\n          AND post_title  = %s\n    ";
        $params = [RegisterPostType::POST_TYPE_NAME, $invoice_title];
        $sql .= ' LIMIT 1';
        return (bool) $wpdb->get_var($wpdb->prepare($sql, ...$params));
        //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared
    }
    private function find_duplicates(): int
    {
        global $wpdb;
        //phpcs:ignore
        return (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(post_title) FROM {$wpdb->posts} WHERE `post_type` = %s AND `post_status` = 'publish' GROUP BY `post_title` HAVING COUNT(post_title) > 1", RegisterPostType::POST_TYPE_NAME));
        //phpcs:ignore
    }
}
