<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Helpers;

use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class BlockTemplateGuard implements Hookable
{
    public function hooks()
    {
        add_action('init', [$this, 'check_enabled_templates']);
    }
    public function check_enabled_templates()
    {
        $active_template = BlockTemplateEditor::get_active_template_id();
        if ($active_template === 0) {
            BlockTemplateEditor::activate_first_template();
        }
    }
}
