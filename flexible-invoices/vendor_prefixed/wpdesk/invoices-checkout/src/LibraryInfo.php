<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout;

use WPDeskFIVendor\Composer\InstalledVersions;
/**
 * Defines library paths and URLs for invoices-checkout.
 */
class LibraryInfo
{
    private const PACKAGE_NAME = 'wpdesk/invoices-checkout';
    private const FALLBACK_VERSION = '0.0.0';
    private ?string $library_version = null;
    public function get_version(): string
    {
        if (null === $this->library_version) {
            $this->library_version = $this->resolve_version();
        }
        return $this->library_version;
    }
    /**
     * Gets the full URL to the library's assets/js/ directory.
     */
    public function get_js_assets_url(): string
    {
        return plugins_url('assets/js/', dirname(__DIR__) . '/composer.json');
    }
    /**
     * Gets the filesystem directory path to the library's assets/js/ directory.
     */
    public function get_js_assets_dir(): string
    {
        return dirname(__DIR__) . '/assets/js/';
    }
    /**
     * Gets the filesystem directory path to the library root.
     */
    public function get_library_dir(): string
    {
        return dirname(__DIR__);
    }
    /**
     * Gets the filesystem directory path to the templates directory.
     */
    public function get_templates_dir(): string
    {
        return dirname(__DIR__) . '/templates/';
    }
    private function resolve_version(): string
    {
        if (!class_exists(InstalledVersions::class)) {
            return self::FALLBACK_VERSION;
        }
        try {
            if (!InstalledVersions::isInstalled(self::PACKAGE_NAME)) {
                return self::FALLBACK_VERSION;
            }
            $version = InstalledVersions::getPrettyVersion(self::PACKAGE_NAME);
            if (is_string($version) && '' !== $version) {
                return $version;
            }
        } catch (\Throwable $exception) {
            return self::FALLBACK_VERSION;
        }
        return self::FALLBACK_VERSION;
    }
}
