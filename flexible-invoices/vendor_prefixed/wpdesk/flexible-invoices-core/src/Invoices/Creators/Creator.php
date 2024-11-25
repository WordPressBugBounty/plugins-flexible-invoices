<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Creators;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\Containers\MetaContainer;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\Creator\DocumentCreator;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\Creator\DocumentCreatorRestrictions;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesAbstracts\Documents\DocumentGetters;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\DocumentsMeta\CustomMeta;
/**
 * Document creator.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Creators
 */
interface Creator extends DocumentCreator, DocumentCreatorRestrictions
{
    /**
     * Returns class for saving additional custom meta for each document.
     *
     * @param DocumentGetters $document
     * @param MetaContainer   $meta
     *
     * @return CustomMeta
     */
    public function custom_meta(DocumentGetters $document, MetaContainer $meta);
}
