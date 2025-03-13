<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers;

/**
 * @package WPDesk\Library\FlexibleInvoicesCore\Helpers
 */
class Template
{
    /**
     * @param string $direction
     *
     * @return string
     */
    public static function rtl_align(string $direction): string
    {
        if ($direction === 'left' && is_rtl()) {
            return 'right';
        }
        if ($direction === 'right' && is_rtl()) {
            return 'left';
        }
        return $direction;
    }
}
