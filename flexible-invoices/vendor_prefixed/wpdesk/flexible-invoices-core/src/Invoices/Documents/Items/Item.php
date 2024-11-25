<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Documents\Items;

interface Item
{
    /**
     * @param string $type
     *
     * @return $this
     */
    public function set_name(string $type): Item;
    /**
     * @param string $type
     *
     * @return $this
     */
    public function set_type(string $type): Item;
    /**
     * @return string
     */
    public function get_type(): string;
    /**
     * @param string $unit
     *
     * @return $this
     */
    public function set_unit(string $unit): Item;
    /**
     * @param float $quantity
     *
     * @return $this
     */
    public function set_qty(float $quantity): Item;
    /**
     * @param float $net_price
     *
     * @return $this
     */
    public function set_net_price(float $net_price): Item;
    /**
     * @param float $total_price
     *
     * @return $this
     */
    public function set_gross_price(float $total_price): Item;
    /**
     * @param float $vat_price
     *
     * @return $this
     */
    public function set_vat_sum(float $vat_price): Item;
    /**
     * @param float $discount
     *
     * @return $this
     */
    public function set_discount(float $discount): Item;
    /**
     * @param float $net_price_sum
     *
     * @return $this
     */
    public function set_net_price_sum(float $net_price_sum): Item;
    /**
     * @param float $vat_rate
     *
     * @return $this
     */
    public function set_vat_rate(float $vat_rate): Item;
    /**
     * @param float $vat_rate_name
     *
     * @return $this
     */
    public function set_vat_rate_name(string $vat_rate_name): Item;
    /**
     * @param int $vat_type_index
     *
     * @return $this
     */
    public function set_vat_type_index(int $vat_type_index): Item;
    /**
     * @param string $sku
     *
     * @return $this
     */
    public function set_sku(string $sku): Item;
    /**
     * @param array $product_attributes
     *
     * @return $this
     */
    public function set_product_attributes(array $product_attributes): Item;
    /**
     * @param array $item_meta
     *
     * @return $this
     */
    public function set_meta(array $item_meta): Item;
    /**
     * @return array
     */
    public function get(): array;
}
