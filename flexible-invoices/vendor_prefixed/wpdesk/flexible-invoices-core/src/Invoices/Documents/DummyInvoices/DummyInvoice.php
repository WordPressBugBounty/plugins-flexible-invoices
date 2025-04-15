<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Documents\DummyInvoices;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\ValueObjects\DocumentCustomer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\ValueObjects\DocumentRecipient;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\ValueObjects\DocumentSeller;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Documents\AbstractDocument;
class DummyInvoice extends AbstractDocument
{
    public function __construct()
    {
        $this->set_number(15);
        $this->set_formatted_number('Invoice 15/01/2025');
        $this->set_currency('USD');
        $this->set_payment_method_name(__('Direct bank transfer', 'flexible-invoices'));
        $this->set_payment_method('bacs');
        $this->set_notes(__('This dummy invoice serves as an example template for preview purposes.', 'flexible-invoices'));
        $this->set_user_lang('EN');
        $this->set_total_paid('0');
        $this->set_payment_status('topay');
        $this->set_items($this->create_dummy_items());
        $this->set_date_of_issue(1737463274);
        $this->set_date_of_pay(1737463274);
        $this->set_date_of_sale(1737463260);
        $this->set_total_tax(21.743138);
        $this->set_total_net(162.844562);
        $this->set_total_gross(184.5877);
        $this->set_customer($this->create_dummy_customer());
        $this->set_recipient($this->create_dummy_recipient());
        $this->set_seller($this->create_dummy_seller());
    }
    private function create_dummy_customer(): DocumentCustomer
    {
        return new DocumentCustomer(0, 'Good Tech Solutions Ltd.', 'Digital Lane', '00-345', 'Night City', 'EU123456789', 'US', '+48123456789', 'not-customer@flexibleinvoices.com', 'individual', '42', 'Ohio');
    }
    private function create_dummy_recipient(): DocumentRecipient
    {
        return new DocumentRecipient('Good Tech Solutions Ltd.', 'Digital Lane', '00-345', 'Night City', 'EU123456780', 'US', '+48123456780', 'not-recipient@flexibleinvoices.com', '43', 'Ohio');
    }
    private function create_dummy_items(): array
    {
        return [['type' => 'product', 'name' => 'Flexible Invoices PRO', 'unit' => 'item', 'quantity' => 1, 'net_price' => 66.39, 'discount' => 0, 'net_price_sum' => 66.39, 'vat_rate' => 19, 'vat_sum' => 12.61, 'vat_type' => 19, 'vat_type_name' => 'VAT', 'vat_type_index' => 1, 'total_price' => 79.0, 'sku' => '', 'product_attributes' => [], 'item_meta' => [], 'item' => 'item'], ['type' => 'product', 'name' => 'Flexible Shipping PRO', 'unit' => 'item', 'quantity' => 1, 'net_price' => 41.18, 'discount' => 0, 'net_price_sum' => 41.18, 'vat_rate' => 19, 'vat_sum' => 7.82, 'vat_type' => 19, 'vat_type_name' => 'VAT', 'vat_type_index' => 1, 'total_price' => 49.0, 'sku' => '', 'product_attributes' => [], 'item_meta' => [], 'item' => 'item'], ['type' => 'product', 'name' => 'Flexible PDF Coupons', 'unit' => 'item', 'quantity' => 2, 'net_price' => 32.77, 'discount' => 0, 'net_price_sum' => 65.54000000000001, 'vat_rate' => 19, 'vat_sum' => 12.46, 'vat_type' => 19, 'vat_type_name' => 'VAT', 'vat_type_index' => 1, 'total_price' => 78.0, 'sku' => '', 'product_attributes' => [], 'item_meta' => [], 'item' => 'item'], ['type' => 'product', 'name' => 'Flexible Product Fields PRO', 'unit' => 'item', 'quantity' => 1, 'net_price' => 24.37, 'discount' => 0, 'net_price_sum' => 24.37, 'vat_rate' => 19, 'vat_sum' => 4.63, 'vat_type' => 19, 'vat_type_name' => 'VAT', 'vat_type_index' => 1, 'total_price' => 29.0, 'sku' => '', 'product_attributes' => [], 'item_meta' => [], 'item' => 'item'], ['type' => 'shipping', 'name' => 'Shipping', 'unit' => 'item', 'quantity' => 1, 'net_price' => 19.99, 'discount' => 0, 'net_price_sum' => 19.99, 'vat_rate' => 23, 'vat_sum' => 4.5977, 'vat_type' => 23, 'vat_type_name' => 'VAT', 'vat_type_index' => 2, 'total_price' => 24.5877, 'sku' => '', 'product_attributes' => [], 'item_meta' => [], 'item' => 'item']];
    }
    private function create_dummy_seller(): DocumentSeller
    {
        return new DocumentSeller(0, '', 'Flexible Invoices Ltd', '99 E-Commerce Road, Berlin, Germany', 'DE987654321', 'FI Bank', 'FI00123456543210123456543210', 'John Doe');
    }
    public function get_type()
    {
        return 'dummy_invoice';
    }
}
