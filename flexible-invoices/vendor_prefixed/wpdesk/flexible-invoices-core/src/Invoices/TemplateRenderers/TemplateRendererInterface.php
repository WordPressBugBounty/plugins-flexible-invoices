<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\TemplateRenderers;

interface TemplateRendererInterface
{
    /**
     * Render template
     *
     * @param array $data
     *
     * @return string
     */
    public function render(array $data): string;
}
