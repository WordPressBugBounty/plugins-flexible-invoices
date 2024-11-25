<?php

namespace WPDeskFIVendor;

if (!\function_exists('WPDeskFIVendor\dd')) {
    function dd(...$args)
    {
        if (\function_exists('WPDeskFIVendor\dump')) {
            dump(...$args);
        } else {
            \var_dump(...$args);
        }
        die;
    }
}
