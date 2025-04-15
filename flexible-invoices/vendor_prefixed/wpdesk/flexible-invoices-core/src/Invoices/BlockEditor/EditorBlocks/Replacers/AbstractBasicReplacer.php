<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\EditorBlocks\Replacers;

use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\BlockEditor\BlockTemplate\BlockTemplate;
abstract class AbstractBasicReplacer implements IBlockReplacer
{
    protected BlockTemplate $block_template;
    public function set_block_template(BlockTemplate $block_template): self
    {
        $this->block_template = $block_template;
        return $this;
    }
    protected function replace_shortcodes(array $shortcodes, string $content): string
    {
        foreach ($shortcodes as $key => $value) {
            $content = str_replace($key, $value, $content);
        }
        return $content;
    }
    protected function remove_whole_element(string $content): string
    {
        $tags = $this->get_wrapper_tags();
        return $this->replace_content_between_html_comments($content, $tags['start'], $tags['end']);
    }
    protected function replace_content_between_html_comments(string $content, string $starting_tag, string $ending_tag, string $replace_with = ''): string
    {
        return preg_replace('/<!--\s*\{' . $starting_tag . '\}\s*-->.*?<!--\s*\{' . $ending_tag . '\}\s*-->/s', $replace_with, $content);
    }
    abstract protected function get_wrapper_tags(): array;
}
