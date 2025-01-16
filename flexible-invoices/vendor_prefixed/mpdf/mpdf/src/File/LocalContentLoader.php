<?php

namespace WPDeskFIVendor\Mpdf\File;

class LocalContentLoader implements \WPDeskFIVendor\Mpdf\File\LocalContentLoaderInterface
{
    public function load($path)
    {
        return file_get_contents($path);
    }
}
