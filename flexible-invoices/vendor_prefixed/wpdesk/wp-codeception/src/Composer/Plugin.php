<?php

declare (strict_types=1);
namespace WPDeskFIVendor\WPDesk\Codeception\Composer;

use WPDeskFIVendor\Composer\Composer;
use WPDeskFIVendor\Composer\IO\IOInterface;
use WPDeskFIVendor\Composer\Plugin\Capable;
use WPDeskFIVendor\Composer\Plugin\PluginInterface;
final class Plugin implements PluginInterface, Capable
{
    public function activate(Composer $composer, IOInterface $io)
    {
    }
    public function deactivate(Composer $composer, IOInterface $io)
    {
    }
    public function uninstall(Composer $composer, IOInterface $io)
    {
    }
    /**
     * @return array<class-string, class-string>
     */
    public function getCapabilities()
    {
        return [\WPDeskFIVendor\Composer\Plugin\Capability\CommandProvider::class => CommandProvider::class];
    }
}
