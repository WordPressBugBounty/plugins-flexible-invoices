<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Data;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\CalculateTotals;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers\PriceFormatter;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Infrastructure\Request;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Settings;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\DocumentData\Seller;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\ValueObjects\DocumentSeller;
/**
 * Get document data from POST.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Data
 */
class PostDocumentDataSource extends AbstractDataSource
{
    /**
     * @var Request
     */
    public $source;
    /**
     * @var array
     */
    protected $products;
    /**
     * @var PostMetaDocumentDataSource
     */
    private $post_meta_data;
    /**
     * @param int $post_id
     * @param Settings $options_container
     * @param string $document_type
     */
    public function __construct(int $post_id, Settings $options_container, string $document_type)
    {
        parent::__construct($options_container, $document_type);
        $this->source = new Request();
        $this->post_id = $post_id;
        $this->customer = $this->source->param('post.client')->get_as_array();
        $this->recipient = $this->source->param('post.recipient')->get_as_array();
        $this->seller = $this->source->param('post.owner')->get_as_array();
        $this->products = $this->source->param('post.product')->get_as_array();
        $this->post_meta_data = new PostMetaDocumentDataSource($post_id, $options_container, $document_type);
    }
    public function get_id(): int
    {
        return $this->post_id;
    }
    /**
     * @return int
     */
    public function get_date_of_sale(): int
    {
        return (int) strtotime($this->source->param('post.date_sale')->get());
    }
    /**
     * @return int
     */
    public function get_date_of_pay(): int
    {
        return (int) strtotime($this->source->param('post.date_pay')->get());
    }
    /**
     * @return int
     */
    public function get_date_of_paid(): int
    {
        $paid_time = $this->source->param('post.date_paid')->get() ?? date('Y-m-d H:i:s', time());
        return (int) strtotime($paid_time);
    }
    /**
     * @return int
     */
    public function get_date_of_issue(): int
    {
        return (int) strtotime($this->source->param('post.date_issue')->get());
    }
    /**
     * @return string
     */
    public function get_customer_filter_field(): string
    {
        return $this->get_customer()->get_name();
    }
    /**
     * @return string
     */
    public function get_currency(): string
    {
        return $this->source->param('post.currency')->get_as_string();
    }
    /**
     * @return float
     */
    public function get_discount(): float
    {
        return PriceFormatter::string_to_float($this->source->param('post.discount')->get());
    }
    /**
     * @return int
     */
    public function get_order_id(): int
    {
        if (empty($this->source->param('post.wc_order_id')->get())) {
            return $this->post_meta_data->get_order_id();
        }
        return $this->source->param('post.wc_order_id')->get();
    }
    /**
     * @return array
     */
    public function get_items(): array
    {
        $products = [];
        if (count($this->products) > 0) {
            foreach ($this->products['name'] as $index => $name) {
                $vat_type = explode('|', $this->products['vat_type'][$index]);
                $qty = $this->products['quantity'][$index] ?? 1;
                if (empty($qty) && $this->get_document_type() !== 'correction') {
                    $qty = 1;
                }
                $products[] = ['name' => $name, 'sku' => $this->products['sku'][$index], 'unit' => $this->products['unit'][$index], 'quantity' => $qty, 'net_price' => PriceFormatter::string_to_float($this->products['net_price'][$index]), 'discount' => $this->products['discount'][$index] ?? '', 'net_price_sum' => PriceFormatter::string_to_float($this->products['net_price_sum'][$index]), 'vat_type' => $vat_type[1] ?? '0', 'vat_type_index' => $vat_type[0] ?? '0', 'vat_type_name' => $vat_type[2] ?? '0', 'vat_rate' => $this->calculate_vat_rate($qty, $index), 'vat_sum' => PriceFormatter::string_to_float($this->products['vat_sum'][$index]), 'total_price' => PriceFormatter::string_to_float($this->products['total_price'][$index]), 'wc_item_type' => $this->products['wc_item_type'][$index] ?? '', 'wc_order_item_id' => $this->products['wc_order_item_id'][$index] ?? '', 'wc_product_id' => $this->products['wc_product_id'][$index] ?? '', 'wc_variation_id' => $this->products['wc_variation_id'][$index] ?? ''];
            }
        }
        // Backward compatibility.
        $correction_products = [];
        if ($this->get_document_type() === 'correction' && !empty($products)) {
            foreach ($products as $index2 => $product) {
                if (isset($this->products['before_correction'][$index2])) {
                    $product['before_correction'] = 1;
                    $product['quantity'] = '-' . $product['quantity'];
                    $product['net_price_sum'] = '-' . $product['net_price_sum'];
                    $product['vat_rate'] = '-' . $product['vat_rate'];
                    $product['vat_sum'] = '-' . $product['vat_sum'];
                    $product['total_price'] = '-' . $product['total_price'];
                    $correction_products[] = $product;
                } else {
                    $correction_products[] = $product;
                }
            }
            return $correction_products;
        }
        return $products;
    }
    private function calculate_vat_rate(int $product_quantity, int $index): float
    {
        if ($product_quantity > 0) {
            return PriceFormatter::string_to_float($this->products['vat_sum'][$index]) / PriceFormatter::string_to_float($product_quantity);
        }
        return 0;
    }
    /**
     * @return string
     */
    public function get_payment_method(): string
    {
        return $this->source->param('post.payment_method')->get_as_string();
    }
    /**
     * @return string
     */
    public function get_payment_method_name(): string
    {
        return $this->source->param('post.payment_method_name')->get_as_string();
    }
    /**
     * @return string
     */
    public function get_notes(): string
    {
        $notes = $this->source->param('post.notes')->get_as_string();
        if (!empty($notes)) {
            return $this->source->param('post.notes')->get_as_string();
        }
        return $this->settings->get($this->get_document_type() . '_notes', '');
    }
    /**
     * @return float
     */
    public function get_total_gross(): float
    {
        return PriceFormatter::string_to_float($this->source->param('post.total_price')->get());
    }
    /**
     * @return float
     */
    public function get_total_net(): float
    {
        return $this->calculate_total_net();
    }
    /**
     * @return float
     */
    public function get_total_paid(): float
    {
        return PriceFormatter::string_to_float($this->source->param('post.total_paid')->get());
    }
    /**
     * @return float
     */
    public function get_total_tax(): float
    {
        return $this->calculate_total_tax();
    }
    /**
     * @return string
     */
    public function get_user_lang(): string
    {
        if (empty($this->source->param('post.wpml_user_lang')->get_as_string())) {
            return $this->post_meta_data->get_user_lang();
        }
        return $this->source->param('post.wpml_user_lang')->get_as_string();
    }
    /**
     * @return string
     */
    public function get_payment_status(): string
    {
        return $this->source->param('post.payment_status')->get_as_string();
    }
    /**
     * @return Seller
     */
    public function get_seller(): Seller
    {
        $name = $this->seller['name'] ?? '';
        $address = $this->seller['address'] ?? '';
        $nip = $this->seller['nip'] ?? '';
        $bank_name = $this->seller['bank'] ?? '';
        $bank_account = $this->seller['account'] ?? '';
        $logo = $this->seller['logo'] ?? '';
        $signature_user = $this->seller['signature_user'] ?? '';
        return new DocumentSeller(0, $logo, $name, $address, $nip, $bank_name, $bank_account, $signature_user);
    }
    /**
     * @return float
     */
    private function calculate_total_tax(): float
    {
        $items_vats = $this->source->param('post.product.vat_sum')->get_as_array();
        $vat_items = [];
        foreach ($items_vats as $vat) {
            $vat_items[]['vat_sum'] = $vat;
        }
        $total = CalculateTotals::calculate_total_vat($vat_items);
        if ($total > 0) {
            return $total;
        }
        return $this->total_tax;
    }
    /**
     * @return float
     */
    private function calculate_total_net(): float
    {
        $items_nets = $this->source->param('post.product.net_price_sum')->get_as_array();
        $items_net = [];
        foreach ($items_nets as $vat) {
            $items_net[]['net_price_sum'] = $vat;
        }
        $total = CalculateTotals::calculate_total_net($items_net);
        if ($total > 0) {
            return $total;
        }
        return $this->total_tax;
    }
    /**
     * @return int
     */
    public function get_show_order_number(): int
    {
        return (int) $this->source->param('post.add_order_id')->get();
    }
    /**
     * @return int
     */
    public function get_corrected_id(): int
    {
        return (int) $this->source->param('post.corrected_invoice')->get();
    }
}
