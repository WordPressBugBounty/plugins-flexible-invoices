<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\Checkout;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
use WPDeskFIVendor\WPDesk\InvoicesCheckout\View\SimpleTemplateRenderer;
/**
 * Handles rendering confirmation checkboxes (IP mismatch, VAT confirmation) in classic checkout.
 */
class CheckoutConfirmations
{
    private ConfigInterface $config;
    private SimpleTemplateRenderer $renderer;
    private bool $show_vat = \false;
    private bool $show_location = \false;
    public function __construct(ConfigInterface $config, SimpleTemplateRenderer $renderer)
    {
        $this->config = $config;
        $this->renderer = $renderer;
    }
    public function set_show_vat(bool $show): void
    {
        $this->show_vat = $show;
    }
    public function set_show_location(bool $show): void
    {
        $this->show_location = $show;
    }
    public function hooks(): void
    {
        add_action('woocommerce_review_order_before_submit', [$this, 'render_confirmations'], 10);
    }
    public function render_confirmations(): void
    {
        $this->renderer->render_checkout_confirmations($this->show_vat, $this->show_location, $this->config->get_location_confirmation_key(), $this->config->get_vat_confirmation_key());
    }
}
