<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\TemplateRenderers;

use WPDeskFIVendor\WPDesk\View\Renderer\Renderer;
class PHPTemplateRenderer implements TemplateRendererInterface
{
    /**
     * @var Renderer
     */
    private $renderer;
    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }
    public function render(array $data): string
    {
        $document_name = $data['document_name'];
        $invoice_atts = $data['invoice_atts'];
        return $this->renderer->render('documents/' . $document_name, $invoice_atts);
    }
}
