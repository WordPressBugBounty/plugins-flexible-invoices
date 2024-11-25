<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Documents\Items;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Documents\Items\DiscountItem;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Documents\Items\DocumentItem;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Documents\Items\FeeItem;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Documents\Items\ProductItem;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Documents\Items\ShippingItem;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Documents\Items\WooProductItem;
/**
 * Document item factory for WooCommerce order.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\WooCommerce
 */
class ItemFactory
{
    /**
     * @var string
     */
    private $type;
    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }
    /**
     * @return DocumentItem
     */
    public function get_item(): DocumentItem
    {
        switch ($this->type) {
            case 'shipping':
                return new ShippingItem();
            case 'fee':
                return new FeeItem();
            case 'discount':
                return new DiscountItem();
            case 'line_item':
                return new WooProductItem();
            default:
                return new ProductItem();
        }
    }
}
