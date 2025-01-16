<?php

namespace WPDeskFIVendor\Mpdf\File;

interface LocalContentLoaderInterface
{
    /**
     * @return string|null
     */
    public function load($path);
}
