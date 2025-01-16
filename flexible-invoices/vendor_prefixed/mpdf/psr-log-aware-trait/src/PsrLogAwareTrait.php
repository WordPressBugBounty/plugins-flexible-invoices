<?php

namespace WPDeskFIVendor\Mpdf\PsrLogAwareTrait;

use WPDeskFIVendor\Psr\Log\LoggerInterface;
trait PsrLogAwareTrait
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
