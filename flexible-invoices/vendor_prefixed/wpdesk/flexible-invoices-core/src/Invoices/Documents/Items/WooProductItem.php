<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Documents\Items;

/**
 * Define product item for WooCommerce.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Items
 */
class WooProductItem extends ProductItem
{
    const TYPE = 'product';
    public function __construct()
    {
        parent::__construct();
        $this->data = array_merge($this->data, ['wc_item_type' => 'line_item', 'wc_order_item_id' => 0, 'wc_product_id' => 0, 'wc_variation_id' => 0]);
    }
    /**
     * @param string $wc_item_type
     *
     * @return $this
     */
    public function set_wc_item_type(string $wc_item_type = 'line_item'): Item
    {
        $this->data['wc_item_type'] = $wc_item_type;
        return $this;
    }
    /**
     * @param int $wc_order_item_id
     *
     * @return $this
     */
    public function set_wc_order_item_id(int $wc_order_item_id): Item
    {
        $this->data['wc_order_item_id'] = $wc_order_item_id;
        return $this;
    }
    /**
     * @param int $wc_product_id
     *
     * @return $this
     */
    public function set_wc_product_id(int $wc_product_id): Item
    {
        $this->data['wc_product_id'] = $wc_product_id;
        return $this;
    }
    /**
     * @param int $wc_variation_id
     *
     * @return $this
     */
    public function set_wc_variation_id(int $wc_variation_id): Item
    {
        $this->data['wc_variation_id'] = $wc_variation_id;
        return $this;
    }
}
