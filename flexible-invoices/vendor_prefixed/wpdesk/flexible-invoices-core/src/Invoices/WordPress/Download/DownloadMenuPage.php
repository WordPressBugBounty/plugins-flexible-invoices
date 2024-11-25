<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\Download;

use WPDeskFIVendor\WPDesk\Forms\Field;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields\FixedSubmitField;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Fields\GroupedFields;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\WordPress\RegisterPostType;
use WPDeskFIVendor\WPDesk\Forms\Resolver\DefaultFormFieldResolver;
use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDeskFIVendor\WPDesk\View\Renderer\Renderer;
use WPDeskFIVendor\WPDesk\View\Renderer\SimplePhpRenderer;
use WPDeskFIVendor\WPDesk\View\Resolver\ChainResolver;
use WPDeskFIVendor\WPDesk\View\Resolver\DirResolver;
/**
 * Register document creators.
 *
 * @package WPDesk\Library\FlexibleInvoicesCore\Integration
 */
class DownloadMenuPage implements Hookable
{
    /**
     * @var string;
     */
    const MENU_SLUG = 'download';
    const NONCE_ACTION = 'batch_download';
    const NONCE_NAME = 'download_invoices';
    /**
     * @var string
     */
    private $template_dir;
    /**
     * @param string $template_dir
     */
    public function __construct(string $template_dir)
    {
        $this->template_dir = $template_dir;
    }
    /**
     * Fires hooks.
     */
    public function hooks()
    {
        add_action('admin_menu', function () {
            add_submenu_page(RegisterPostType::POST_TYPE_MENU_URL, $this->get_tab_name(), $this->get_tab_name(), 'download_flexible_invoices', self::MENU_SLUG, [$this, 'render_page_action'], 20);
        });
    }
    /**
     * @return Renderer
     */
    private function get_renderer()
    {
        $resolver = new ChainResolver();
        $resolver->appendResolver(new DirResolver($this->template_dir . 'settings'));
        $resolver->appendResolver(new DefaultFormFieldResolver());
        return new SimplePhpRenderer($resolver);
    }
    /**
     * @return void
     */
    public function render_page_action()
    {
        $url = 'https://docs.flexibleinvoices.com/article/804-printing-and-downloading-documents?utm_source=flexible-invoices-settings&utm_medium=link&utm_campaign=flexible-invoices-docs-link&utm_content=download-invoices';
        if (get_locale() === 'pl_PL') {
            $url = 'https://www.wpdesk.pl/docs/faktury-woocommerce-docs/?utm_source=flexible-invoices-settings&utm_medium=link&utm_campaign=flexible-invoices-docs-link&utm_content=download-invoices#hurtowe-pobieranie-faktur';
        }
        $docs_description = sprintf('%2$s%1$s%3$s', sprintf(esc_html__('Read more in the %1$splugin documentation &rarr;%2$s', 'flexible-invoices'), '<a href="' . $url . '" target="_blank" style="color: #4BB04E; font-weight: 700;">', '</a>'), '<strong>', '</strong>');
        $renderer = $this->get_renderer();
        $content = '<div class="wrap"><h1 class="wp-heading-inline">' . esc_html__('Download', 'flexible-invoices') . '</h1>';
        $content .= '<div class="support-url-wrapper">' . $docs_description . '</div>';
        $content .= '<hr class="wp-header-end">';
        $content .= $renderer->render('form-start', ['form' => $this, 'method' => 'POST', 'action' => '']);
        $content .= $this->render_fields($renderer);
        $content .= $renderer->render('form-end');
        $content .= '</div>';
        echo $content;
        //phpcs:ignore
    }
    /**
     * @param Renderer $renderer
     *
     * @return string
     */
    public function render_fields(Renderer $renderer): string
    {
        $content = '';
        foreach ($this->get_fields() as $field) {
            $content .= $renderer->render($field->should_override_form_template() ? $field->get_template_name() : 'form-field', ['field' => $field, 'renderer' => $renderer, 'name_prefix' => $this->get_form_id(), 'value' => '', 'template_name' => $field->get_template_name()]);
        }
        return $content;
    }
    /**
     * @return array|Field[]
     */
    protected function get_fields(): array
    {
        return [(new Field\Header())->set_label(esc_html__('Download Invoices', 'flexible-invoices'))->set_description(esc_html__('If the download fails select smaller date range and try again.', 'flexible-invoices')), (new GroupedFields())->set_name('grouped_field')->set_grouped_fields([(new Field\DateField())->set_name('start_date')->set_label(esc_html__('From:', 'flexible-invoices'))->add_class('medium-text hs-beacon-search')->set_default_value(date('Y-m-d', strtotime('NOW - 1 months')))->set_attribute('data-beacon_search', 'Download'), (new Field\DateField())->set_name('end_date')->set_label(esc_html__('To:', 'flexible-invoices'))->add_class('medium-text hs-beacon-search')->set_default_value(date('Y-m-d'))->set_attribute('data-beacon_search', 'Download')]), (new FixedSubmitField())->set_name('download_documents')->set_label(esc_html__('Download', 'flexible-invoices'))->add_class('button-primary'), (new Field\NoOnceField(self::NONCE_ACTION))->set_name(self::NONCE_NAME)];
    }
    /**
     * @return string
     */
    public function get_method(): string
    {
        return 'POST';
    }
    /**
     * @return string
     */
    public function get_action(): string
    {
        return admin_url('admin-ajax.php?action=documents-batch-download');
    }
    /**
     * @return string
     */
    public function get_form_id(): string
    {
        return 'download';
    }
    /**
     * @return string
     */
    public static function get_tab_slug(): string
    {
        return 'download';
    }
    /**
     * @return string
     */
    public function get_tab_name(): string
    {
        return esc_html__('Download', 'flexible-invoices');
    }
}
