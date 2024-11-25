<?php

namespace WPDeskFIVendor\WPDesk\Composer\Codeception;

use WPDeskFIVendor\Composer\Composer;
use WPDeskFIVendor\Composer\IO\IOInterface;
use WPDeskFIVendor\Composer\Plugin\Capable;
use WPDeskFIVendor\Composer\Plugin\PluginInterface;
/**
 * Composer plugin.
 *
 * @package WPDesk\Composer\Codeception
 */
class Plugin implements PluginInterface, Capable
{
    /**
     * @var Composer
     */
    private $composer;
    /**
     * @var IOInterface
     */
    private $io;
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
    /**
     * @inheritDoc
     */
    public function deactivate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
    /**
     * @inheritDoc
     */
    public function uninstall(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
    public function getCapabilities()
    {
        return [\WPDeskFIVendor\Composer\Plugin\Capability\CommandProvider::class => CommandProvider::class];
    }
}
