<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Integration;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\Creator\DocumentCreator;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Creators\AbstractDocumentCreator;
/**
 * Register document creators.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Integration
 */
class CreatorContainer
{
    /**
     * @var AbstractDocumentCreator[];
     */
    private $creators = [];
    /**
     * @param AbstractDocumentCreator $creator
     */
    public function add_creator(AbstractDocumentCreator $creator)
    {
        $this->creators[$creator->get_type()] = $creator;
    }
    /**
     * @return AbstractDocumentCreator[]
     */
    public function get_creators(): array
    {
        return $this->creators;
    }
}
