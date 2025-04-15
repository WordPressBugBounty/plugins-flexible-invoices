<?php

namespace WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\PDF;

/**
 * Defines and return fonts data for mPDF.
 *
 * @package WPDesk\Library\WPCoupons\PDF
 */
final class FontsData
{
    /**
     * @var array[]
     */
    private $fonts_data = ['freeserif' => ['R' => 'FreeSerif.ttf', 'I' => 'FreeSerif.ttf', 'B' => 'FreeSerif-Bold.ttf', 'BI' => 'FreeSerif-Bold.ttf', 'useOTL' => 0xff, 'useKashida' => 75], 'dejavusans' => ['R' => 'DejaVuSans.ttf', 'I' => 'DejaVuSans.ttf', 'B' => 'DejaVuSans-Bold.ttf', 'BI' => 'DejaVuSans-Bold.ttf', 'useOTL' => 0xff, 'useKashida' => 75], 'dejavuserif' => ['R' => 'DejaVuSerif.ttf', 'I' => 'DejaVuSerif.ttf', 'B' => 'DejaVuSerif-Bold.ttf', 'BI' => 'DejaVuSerif-Bold.ttf'], 'dejavusanscondensed' => ['R' => 'DejaVuSansCondensed.ttf', 'I' => 'DejaVuSansCondensed.ttf', 'B' => 'DejaVuSansCondensed-Bold.ttf', 'BI' => 'DejaVuSansCondensed-Bold.ttf'], 'montserrat' => ['R' => 'Montserrat.ttf', 'I' => 'Montserrat.ttf', 'B' => 'Montserrat-Bold.ttf', 'BI' => 'Montserrat-Bold.ttf'], 'opensans' => ['R' => 'OpenSans.ttf', 'I' => 'OpenSans.ttf', 'B' => 'OpenSans-Bold.ttf', 'BI' => 'OpenSans-Bold.ttf'], 'opensanscondensed' => ['R' => 'OpenSansCondensed.ttf', 'I' => 'OpenSansCondensed.ttf', 'B' => 'OpenSansCondensed-Bold.ttf', 'BI' => 'OpenSansCondensed-Bold.ttf'], 'roboto' => ['R' => 'Roboto.ttf', 'I' => 'Roboto.ttf', 'B' => 'Roboto-Bold.ttf', 'BI' => 'Roboto-Bold.ttf'], 'robotoslab' => ['R' => 'RobotoSlab.ttf', 'I' => 'RobotoSlab.ttf', 'B' => 'RobotoSlab-Bold.ttf', 'BI' => 'RobotoSlab-Bold.ttf'], 'rubik' => ['R' => 'Rubik.ttf', 'I' => 'Rubik.ttf', 'B' => 'Rubik-Bold.ttf', 'BI' => 'Rubik-Bold.ttf'], 'titilliumweb' => ['R' => 'TitilliumWeb.ttf', 'I' => 'TitilliumWeb.ttf', 'B' => 'TitilliumWeb-Bold.ttf', 'BI' => 'TitilliumWeb-Bold.ttf']];
    /**
     * @param string $slug Slug of font, used as default font or in css. Must be in lowercase.
     * @param string $name Name of font that can be found in the defined dirs.
     * @param array $attr Custom attributes.
     *
     * @return $this
     */
    public function set_font(string $slug, string $name, array $attr = []): FontsData
    {
        $this->fonts_data[$slug] = ['R' => $name . '.ttf', 'I' => $name . '-Italic.ttf', 'B' => $name . '-Bold.ttf', 'BI' => $name . '-BoldItalic.ttf'];
        if (!empty($attr)) {
            $this->fonts_data[$slug] = $attr;
        }
        return $this;
    }
    /**
     * @param string $slug Slug of font, used as default font or in css. Must be in lowercase.
     * @param string $name Name of font that can be found in the defined dirs.
     * @param array $attr Custom attributes.
     *
     * @return $this
     */
    public function set_font_without_italic(string $slug, string $name, array $attr = []): FontsData
    {
        $this->fonts_data[$slug] = ['R' => $name . '.ttf', 'I' => $name . '.ttf', 'B' => $name . '-Bold.ttf', 'BI' => $name . '-Bold.ttf'];
        if (!empty($attr)) {
            $this->fonts_data[$slug] = $attr;
        }
        return $this;
    }
    /**
     * @param string $slug Slug of font, used as default font or in css. Must be in lowercase.
     * @param string $name Name of font that can be found in the defined dirs.
     * @param array $attr Custom attributes.
     *
     * @return $this
     */
    public function set_font_without_bold(string $slug, string $name, array $attr = []): FontsData
    {
        $this->fonts_data[$slug] = ['R' => $name . '.ttf', 'I' => $name . '-Italic.ttf', 'B' => $name . '.ttf', 'BI' => $name . '-Italic.ttf'];
        if (!empty($attr)) {
            $this->fonts_data[$slug] = $attr;
        }
        return $this;
    }
    /**
     * @param string $slug Slug of font, used as default font or in css. Must be in lowercase.
     * @param string $name Name of font that can be found in the defined dirs.
     * @param array $attr Custom attributes.
     *
     * @return $this
     */
    public function set_font_without_bold_italic(string $slug, string $name, array $attr = []): FontsData
    {
        $this->fonts_data[$slug] = ['R' => $name . '.ttf', 'I' => $name . '.ttf', 'B' => $name . '.ttf', 'BI' => $name . '.ttf'];
        if (!empty($attr)) {
            $this->fonts_data[$slug] = $attr;
        }
        return $this;
    }
    /**
     * @return array
     */
    public function get()
    {
        return $this->fonts_data;
    }
}
