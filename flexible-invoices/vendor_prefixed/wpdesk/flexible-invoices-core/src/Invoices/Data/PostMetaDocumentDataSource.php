<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Data;

use WP_Post;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\PostType\TemplatesPostType;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\BlockTemplateEditor;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\CalculateTotals;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\PriceFormatter;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Integration\MetaPostContainer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Settings;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\DocumentData\Seller;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\ValueObjects\DocumentSeller;
use WPDeskFIVendor\WPDesk\Persistence\PersistentContainer;
/**
 * Get document data form post meta.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Data
 */
class PostMetaDocumentDataSource extends AbstractDataSource
{
    /**
     * @var PersistentContainer
     */
    public $meta;
    /**
     * @var WP_Post
     */
    public $post;
    public function __construct(int $post_id, Settings $options_container, string $document_type)
    {
        parent::__construct($options_container, $document_type);
        $this->post_id = $post_id;
        $this->meta = new MetaPostContainer($post_id);
        $this->post = get_post($this->post_id);
        $this->template_id = (int) $this->meta->get_fallback('_template_id', BlockTemplateEditor::get_active_template_id());
        $this->customer = $this->meta->get_fallback('_client', []);
        $this->recipient = $this->meta->get_fallback('_recipient', []);
        $this->seller = $this->meta->get_fallback('_owner', []);
    }
    public function get_id(): int
    {
        return $this->post_id;
    }
    public function get_number(): int
    {
        return (int) $this->meta->get_fallback('_number', 1);
    }
    public function get_formatted_number(): string
    {
        return empty($this->post->post_title) ? $this->meta->get_fallback('_formatted_number', '') : $this->post->post_title;
    }
    public function get_date_of_sale(): int
    {
        return (int) $this->meta->get_fallback('_date_sale', strtotime(current_time('mysql')));
    }
    public function get_date_of_pay(): int
    {
        if (BlockTemplateEditor::is_block_template_editor_active()) {
            $default_due_time = get_post_meta(BlockTemplateEditor::get_active_template_id(), $this->get_document_type() . '_' . TemplatesPostType::DEFAULT_DUE_META, \true);
        } else {
            $default_due_time = $this->settings->get($this->get_document_type() . '_default_due_time');
        }
        return (int) $this->meta->get_fallback('_date_pay', $this->get_date_of_issue() + 60 * 60 * 24 * (int) $default_due_time);
    }
    public function get_date_of_issue(): int
    {
        return (int) $this->meta->get_fallback('_date_issue', strtotime(current_time('mysql')));
    }
    public function get_seller(): Seller
    {
        if (empty($this->seller)) {
            //@phpstan-ignore-line
            return parent::get_seller();
        }
        $name = $this->seller['name'] ?? '';
        $address = $this->seller['address'] ?? '';
        $nip = $this->seller['nip'] ?? '';
        $bank_name = $this->seller['bank'] ?? '';
        $bank_account = $this->seller['account'] ?? '';
        $logo = $this->seller['logo'] ?? '';
        $signature_user = $this->seller['signature_user'] ?? '';
        return new DocumentSeller(0, $logo, $name, $address, $nip, $bank_name, $bank_account, $signature_user);
    }
    public function get_customer_filter_field(): string
    {
        return $this->get_customer()->get_name();
    }
    public function get_currency(): string
    {
        return $this->meta->get('_currency');
    }
    public function get_discount(): float
    {
        return PriceFormatter::string_to_float($this->meta->get('_discount'));
    }
    public function get_order_id(): int
    {
        return (int) $this->meta->get_fallback('_wc_order_id', 0);
    }
    public function get_items(): array
    {
        $products = $this->meta->get_fallback('_products', []);
        $shipping = $this->meta->get_fallback('_shipping', []);
        if (is_array($products) && is_array($shipping)) {
            return array_merge($products, $shipping);
        }
        return [];
    }
    public function get_payment_method(): string
    {
        return $this->meta->get('_payment_method');
    }
    public function get_payment_status(): string
    {
        return $this->meta->get_fallback('_payment_status', 'due');
    }
    public function get_date_of_paid(): int
    {
        return (int) $this->meta->get_fallback('_date_paid', 0);
    }
    public function get_payment_method_name(): string
    {
        return $this->meta->get('_payment_method_name');
    }
    public function get_notes(): string
    {
        return $this->meta->get('_notes');
    }
    public function get_total_gross(): float
    {
        return PriceFormatter::string_to_float($this->meta->get_fallback('_total_price', 0.0));
    }
    public function get_total_net(): float
    {
        return PriceFormatter::string_to_float($this->meta->get_fallback('_total_net', CalculateTotals::calculate_total_net($this->get_items())));
    }
    public function get_total_paid(): float
    {
        return PriceFormatter::string_to_float($this->meta->get_fallback('_total_paid', 0.0));
    }
    public function get_total_tax(): float
    {
        return PriceFormatter::string_to_float($this->meta->get_fallback('_total_tax', CalculateTotals::calculate_total_vat($this->get_items())));
    }
    public function get_user_lang(): string
    {
        $user_lang = $this->meta->get('wpml_user_lang');
        if (empty($user_lang)) {
            return strtolower($this->get_customer()->get_country());
        }
        return $user_lang;
    }
    public function get_corrected_id(): int
    {
        return (int) $this->meta->get_fallback('_corrected_invoice_id', 0);
    }
    public function get_is_correction(): int
    {
        return (int) $this->meta->get_fallback('_correction', 0);
    }
    public function get_show_order_number(): int
    {
        return (int) $this->meta->get_fallback('_add_order_id', 0);
    }
    public function get_template_id(): int
    {
        return (int) $this->meta->get_fallback('_template_id', 0);
    }
}
