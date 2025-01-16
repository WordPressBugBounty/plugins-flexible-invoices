<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Creators;

use Exception;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Documents\Invoice;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Email\DocumentEmail;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Email\EmailInvoice;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Infrastructure\Request;
/**
 * Invoice creator.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Creators
 */
class InvoiceCreator extends AbstractDocumentCreator
{
    /**
     * @return string
     */
    public function get_type(): string
    {
        return Invoice::DOCUMENT_TYPE;
    }
    /**
     * @param int    $document_id
     * @param string $source_type
     *
     * @throws Exception
     */
    public function create_document_from_source($document_id, $source_type)
    {
        $request = new Request();
        if ($request->param_exists('get.related_proforma_id')) {
            $document_id = $request->param('get.related_proforma_id')->get();
            $this->assign_data_from_proforma(new Invoice(), $document_id, $source_type);
        } else {
            $this->assign_data_from_source(new Invoice(), $document_id, $source_type);
        }
    }
    /**
     * @throws Exception
     */
    private function assign_data_from_proforma($document, $document_id, $source_type)
    {
        $data = $this->source_factory->get_source($document_id, $source_type, $this->get_type());
        $document->set_date_of_pay(strtotime(current_time('mysql')));
        $document->set_date_of_paid(strtotime(current_time('mysql')));
        $document->set_date_of_issue(strtotime(current_time('mysql')));
        $document->set_date_of_sale(strtotime(current_time('mysql')));
        $document->set_customer($data->get_customer());
        $document->set_recipient($data->get_recipient());
        $document->set_customer_filter_field($data->get_customer_filter_field());
        $document->set_seller($data->get_seller());
        $document->set_currency($data->get_currency());
        $document->set_discount($data->get_discount());
        $document->set_items($data->get_items());
        $document->set_payment_method($data->get_payment_method());
        $document->set_payment_method_name($data->get_payment_method_name());
        $document->set_payment_status($data->get_payment_status());
        $document->set_notes($data->get_notes());
        $document->set_tax($data->get_tax());
        $document->set_total_gross(0);
        $document->set_total_net(0);
        $document->set_total_paid($data->get_total_paid());
        $document->set_total_tax($data->get_total_tax());
        $document->set_user_lang($data->get_user_lang());
        $document->set_show_order_number($data->get_show_order_number());
        $document->set_order_id($data->get_order_id());
        $this->document = $document;
    }
    /**
     * @return DocumentEmail
     */
    public function get_email_class(): DocumentEmail
    {
        return new EmailInvoice();
    }
}
