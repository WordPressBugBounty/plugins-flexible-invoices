<?php

namespace WPDeskFIVendor\WPDesk\InvoicesCheckout\I18n;

use WPDeskFIVendor\WPDesk\InvoicesCheckout\ConfigInterface;
/**
 * Registers Gutenberg script translations against the integrating plugin lang folder.
 *
 * After merge-translations the JS domain is rewritten to the plugin domain and
 * wp i18n make-json names files from PO references (assets/js/*.js), not the
 * vendor_prefixed runtime path. This maps those JSON files onto the registered handles.
 */
class ScriptTranslations
{
    private ConfigInterface $config;
    /**
     * @var array<string, string>
     */
    private array $handles = [];
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }
    public function hooks(): void
    {
        add_filter('load_script_translation_file', [$this, 'map_script_translation_file'], 10, 3);
    }
    public function set_for_handle(string $handle, string $relative_js): void
    {
        $domain = $this->config->get_plugin_text_domain();
        $lang_dir = $this->config->get_plugin_lang_dir();
        if ('' === $domain || '' === $lang_dir) {
            return;
        }
        $this->handles[$handle] = $relative_js;
        wp_set_script_translations($handle, $domain, trailingslashit($lang_dir));
    }
    /**
     * @param string|false $file
     * @param string       $handle
     * @param string       $domain
     * @return string|false
     */
    public function map_script_translation_file($file, $handle, $domain)
    {
        if ($domain !== $this->config->get_plugin_text_domain() || !isset($this->handles[$handle])) {
            return $file;
        }
        $mapped = trailingslashit($this->config->get_plugin_lang_dir()) . $domain . '-' . determine_locale() . '-' . md5($this->handles[$handle]) . '.json';
        if (is_readable($mapped)) {
            return $mapped;
        }
        return $file;
    }
}
