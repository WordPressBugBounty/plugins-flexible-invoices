<?php

namespace WPDeskFIVendor\Mpdf\Http;

use WPDeskFIVendor\Psr\Http\Message\RequestInterface;
interface ClientInterface
{
    public function sendRequest(RequestInterface $request);
}
