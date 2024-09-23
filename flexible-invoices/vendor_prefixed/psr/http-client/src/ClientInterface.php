<?php

namespace WPDeskFIVendor\Psr\Http\Client;

use WPDeskFIVendor\Psr\Http\Message\RequestInterface;
use WPDeskFIVendor\Psr\Http\Message\ResponseInterface;
interface ClientInterface
{
    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest(\WPDeskFIVendor\Psr\Http\Message\RequestInterface $request) : \WPDeskFIVendor\Psr\Http\Message\ResponseInterface;
}
