<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\View;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\LibraryInfo;
/**
 * Handles rendering of template files.
 */
class SimpleTemplateRenderer
{
    private LibraryInfo $library_info;
    public function __construct(LibraryInfo $library_info)
    {
        $this->library_info = $library_info;
    }
    /**
     * Renders checkout confirmations template.
     */
    public function render_checkout_confirmations(bool $show_vat, bool $show_location, string $location_key, string $vat_key): void
    {
        $this->render('checkout/confirmations.php', ['show_vat' => $show_vat, 'show_location' => $show_location, 'location_key' => $location_key, 'vat_key' => $vat_key]);
    }
    /**
     * Renders the EU VAT metabox template on the order edit screen.
     */
    public function render_vies_order_metabox(string $vat_number, bool $vat_number_self_declared, bool $valid, bool $validated, string $billing_country, string $ip_address, string $ip_country, bool $customer_self_declared_country, array $countries): void
    {
        $this->render('admin/vies-order-metabox.php', ['vat_number' => $vat_number, 'vat_number_self_declared' => $vat_number_self_declared, 'valid' => $valid, 'validated' => $validated, 'billing_country' => $billing_country, 'ip_address' => $ip_address, 'ip_country' => $ip_country, 'customer_self_declared_country' => $customer_self_declared_country, 'countries' => $countries]);
    }
    /**
     * Renders a template relative to the library templates directory.
     */
    public function render(string $relative_template, array $args): void
    {
        // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
        include $this->library_info->get_templates_dir() . $relative_template;
    }
}
