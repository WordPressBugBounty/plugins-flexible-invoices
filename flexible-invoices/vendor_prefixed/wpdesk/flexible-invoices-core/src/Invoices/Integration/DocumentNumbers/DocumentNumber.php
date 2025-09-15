<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Integration\DocumentNumbers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\Documents\Document;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\DuplicationChecker;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Settings;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\Translator;
/**
 * This class define document number for each document types.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Integration
 */
class DocumentNumber
{
    /**
     * @var Settings
     */
    protected $settings;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $prefix;
    /**
     * @var string
     */
    protected $suffix;
    /**
     * @var int
     */
    protected $document_number;
    /**
     * @var int
     */
    protected int $issue_date;
    /**
     * @var Document
     */
    protected $document;
    /**
     * @var int
     */
    protected $current_time;
    /**
     * @param Settings $settings
     * @param Document $document
     * @param string $name
     */
    public function __construct(Settings $settings, Document $document, string $name = 'Document')
    {
        $this->settings = $settings;
        $this->document = $document;
        $this->type = $document->get_type();
        $this->issue_date = $document->get_date_of_issue();
        $this->prefix = $settings->get($this->type . '_number_prefix', $name . ' ');
        $this->suffix = $settings->get($this->type . '_number_suffix', '/{MM}/{YYYY}');
        $this->current_time = strtotime(current_time('mysql'));
        $this->document_number = $this->get_document_number();
    }
    /**
     * @return int
     */
    protected function get_number_from_option(): int
    {
        global $wpdb;
        // phpcs:disable
        $number = (int) $wpdb->get_var($wpdb->prepare("SELECT `option_value` FROM {$wpdb->options} WHERE `option_name` = '%s' ", 'inspire_invoices_' . $this->type . '_start_number'));
        // phpcs:enable
        if (!$number) {
            return 1;
        }
        return $number;
    }
    /**
     * @param int $value
     */
    protected function update_number(int $value)
    {
        global $wpdb;
        // phpcs:disable
        $wpdb->update($wpdb->options, array('option_value' => $value), array('option_name' => 'inspire_invoices_' . $this->type . '_start_number'));
        // phpcs:enable
    }
    /**
     * @return int
     */
    protected function get_document_number(): int
    {
        $number_reset_type = $this->get_reset_type();
        $number_reset_time = $this->get_reset_time();
        $reset_number = \false;
        if ($number_reset_type === 'month' && date('m.Y', $this->issue_date) !== date('m.Y', $number_reset_time)) {
            $reset_number = \true;
        }
        if ($number_reset_type === 'year' && date('Y', $this->issue_date) !== date('Y', $number_reset_time)) {
            $reset_number = \true;
        }
        if ($reset_number) {
            return 1;
        }
        return $this->get_number_from_option();
    }
    protected function get_reset_type(): string
    {
        return $this->settings->get($this->type . '_number_reset_type', 'year');
    }
    protected function get_reset_time(): int
    {
        return (int) $this->settings->get($this->type . '_start_number_timestamp', $this->current_time);
    }
    protected function get_number_array_for_formatting(): array
    {
        return [Translator::translate_meta('inspire_invoices_' . $this->type . '_number_prefix', $this->prefix), $this->document_number, Translator::translate_meta('inspire_invoices_' . $this->type . '_number_suffix', $this->suffix)];
    }
    /**
     * @return string
     */
    public function get_formatted_number(): string
    {
        $number_array = $this->get_number_array_for_formatting();
        foreach ($number_array as &$value) {
            $value = str_replace(['{DD}', '{MM}', '{YYYY}', '{AAAA}'], [wp_date('d', $this->issue_date), wp_date('m', $this->issue_date), wp_date('Y', $this->issue_date), wp_date('Y', $this->issue_date)], $value);
        }
        /**
         * Filters the numbering for the document.
         *
         * @param array $number_array Array of data for numbering that will be imploded.
         * @param Document $document Document Object.
         *
         * @return array
         *
         * @since 3.0.0
         */
        $number_array = apply_filters('fi/core/numbering/formatted_number', $number_array, $this->document);
        $checker = new DuplicationChecker();
        $invoice_title = implode('', $number_array);
        if (apply_filters('fi/core/numbering/enable_duplicate_guard', \false)) {
            $max_attempts = apply_filters('fi/core/numbering/duplicate_guard_max_attemps', 5);
            for ($attempt = 1; $attempt < $max_attempts; $attempt++) {
                $invoice_title = implode('', $number_array);
                if ($checker->invoice_with_title_exists($invoice_title)) {
                    $this->document_number = $number_array[1];
                    $this->increase_number();
                    $number_array[1] = $this->get_number_from_option();
                } else {
                    break;
                }
            }
        }
        return $invoice_title;
    }
    /**
     * @return int
     */
    public function get_number(): int
    {
        return $this->document_number;
    }
    protected function update_start_number_timestamp(int $value): void
    {
        $this->settings->set($this->type . '_start_number_timestamp', $value);
    }
    /**
     * @return void
     */
    public function increase_number()
    {
        $number = $this->document_number;
        ++$number;
        $this->update_number($number);
        $this->update_start_number_timestamp($this->current_time);
    }
}
