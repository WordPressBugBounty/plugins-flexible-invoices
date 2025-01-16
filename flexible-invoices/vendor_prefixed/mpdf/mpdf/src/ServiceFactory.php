<?php

namespace WPDeskFIVendor\Mpdf;

use WPDeskFIVendor\Mpdf\Color\ColorConverter;
use WPDeskFIVendor\Mpdf\Color\ColorModeConverter;
use WPDeskFIVendor\Mpdf\Color\ColorSpaceRestrictor;
use WPDeskFIVendor\Mpdf\File\LocalContentLoader;
use WPDeskFIVendor\Mpdf\Fonts\FontCache;
use WPDeskFIVendor\Mpdf\Fonts\FontFileFinder;
use WPDeskFIVendor\Mpdf\Http\CurlHttpClient;
use WPDeskFIVendor\Mpdf\Http\SocketHttpClient;
use WPDeskFIVendor\Mpdf\Image\ImageProcessor;
use WPDeskFIVendor\Mpdf\Pdf\Protection;
use WPDeskFIVendor\Mpdf\Pdf\Protection\UniqidGenerator;
use WPDeskFIVendor\Mpdf\Writer\BaseWriter;
use WPDeskFIVendor\Mpdf\Writer\BackgroundWriter;
use WPDeskFIVendor\Mpdf\Writer\ColorWriter;
use WPDeskFIVendor\Mpdf\Writer\BookmarkWriter;
use WPDeskFIVendor\Mpdf\Writer\FontWriter;
use WPDeskFIVendor\Mpdf\Writer\FormWriter;
use WPDeskFIVendor\Mpdf\Writer\ImageWriter;
use WPDeskFIVendor\Mpdf\Writer\JavaScriptWriter;
use WPDeskFIVendor\Mpdf\Writer\MetadataWriter;
use WPDeskFIVendor\Mpdf\Writer\OptionalContentWriter;
use WPDeskFIVendor\Mpdf\Writer\PageWriter;
use WPDeskFIVendor\Mpdf\Writer\ResourceWriter;
use WPDeskFIVendor\Psr\Log\LoggerInterface;
class ServiceFactory
{
    /**
     * @var \Mpdf\Container\ContainerInterface|null
     */
    private $container;
    public function __construct($container = null)
    {
        $this->container = $container;
    }
    public function getServices(Mpdf $mpdf, LoggerInterface $logger, $config, $languageToFont, $scriptToLanguage, $fontDescriptor, $bmp, $directWrite, $wmf)
    {
        $sizeConverter = new SizeConverter($mpdf->dpi, $mpdf->default_font_size, $mpdf, $logger);
        $colorModeConverter = new ColorModeConverter();
        $colorSpaceRestrictor = new ColorSpaceRestrictor($mpdf, $colorModeConverter);
        $colorConverter = new ColorConverter($mpdf, $colorModeConverter, $colorSpaceRestrictor);
        $tableOfContents = new TableOfContents($mpdf, $sizeConverter);
        $cacheBasePath = $config['tempDir'] . '/mpdf';
        $cache = new Cache($cacheBasePath, $config['cacheCleanupInterval']);
        $fontCache = new FontCache(new Cache($cacheBasePath . '/ttfontdata', $config['cacheCleanupInterval']));
        $fontFileFinder = new FontFileFinder($config['fontDir']);
        if ($this->container && $this->container->has('httpClient')) {
            $httpClient = $this->container->get('httpClient');
        } elseif (\function_exists('curl_init')) {
            $httpClient = new CurlHttpClient($mpdf, $logger);
        } else {
            $httpClient = new SocketHttpClient($logger);
        }
        $localContentLoader = $this->container && $this->container->has('localContentLoader') ? $this->container->get('localContentLoader') : new LocalContentLoader();
        $assetFetcher = new AssetFetcher($mpdf, $localContentLoader, $httpClient, $logger);
        $cssManager = new CssManager($mpdf, $cache, $sizeConverter, $colorConverter, $assetFetcher);
        $otl = new Otl($mpdf, $fontCache);
        $protection = new Protection(new UniqidGenerator());
        $writer = new BaseWriter($mpdf, $protection);
        $gradient = new Gradient($mpdf, $sizeConverter, $colorConverter, $writer);
        $formWriter = new FormWriter($mpdf, $writer);
        $form = new Form($mpdf, $otl, $colorConverter, $writer, $formWriter);
        $hyphenator = new Hyphenator($mpdf);
        $imageProcessor = new ImageProcessor($mpdf, $otl, $cssManager, $sizeConverter, $colorConverter, $colorModeConverter, $cache, $languageToFont, $scriptToLanguage, $assetFetcher, $logger);
        $tag = new Tag($mpdf, $cache, $cssManager, $form, $otl, $tableOfContents, $sizeConverter, $colorConverter, $imageProcessor, $languageToFont);
        $fontWriter = new FontWriter($mpdf, $writer, $fontCache, $fontDescriptor);
        $metadataWriter = new MetadataWriter($mpdf, $writer, $form, $protection, $logger);
        $imageWriter = new ImageWriter($mpdf, $writer);
        $pageWriter = new PageWriter($mpdf, $form, $writer, $metadataWriter);
        $bookmarkWriter = new BookmarkWriter($mpdf, $writer);
        $optionalContentWriter = new OptionalContentWriter($mpdf, $writer);
        $colorWriter = new ColorWriter($mpdf, $writer);
        $backgroundWriter = new BackgroundWriter($mpdf, $writer);
        $javaScriptWriter = new JavaScriptWriter($mpdf, $writer);
        $resourceWriter = new ResourceWriter($mpdf, $writer, $colorWriter, $fontWriter, $imageWriter, $formWriter, $optionalContentWriter, $backgroundWriter, $bookmarkWriter, $metadataWriter, $javaScriptWriter, $logger);
        return ['otl' => $otl, 'bmp' => $bmp, 'cache' => $cache, 'cssManager' => $cssManager, 'directWrite' => $directWrite, 'fontCache' => $fontCache, 'fontFileFinder' => $fontFileFinder, 'form' => $form, 'gradient' => $gradient, 'tableOfContents' => $tableOfContents, 'tag' => $tag, 'wmf' => $wmf, 'sizeConverter' => $sizeConverter, 'colorConverter' => $colorConverter, 'hyphenator' => $hyphenator, 'localContentLoader' => $localContentLoader, 'httpClient' => $httpClient, 'assetFetcher' => $assetFetcher, 'imageProcessor' => $imageProcessor, 'protection' => $protection, 'languageToFont' => $languageToFont, 'scriptToLanguage' => $scriptToLanguage, 'writer' => $writer, 'fontWriter' => $fontWriter, 'metadataWriter' => $metadataWriter, 'imageWriter' => $imageWriter, 'formWriter' => $formWriter, 'pageWriter' => $pageWriter, 'bookmarkWriter' => $bookmarkWriter, 'optionalContentWriter' => $optionalContentWriter, 'colorWriter' => $colorWriter, 'backgroundWriter' => $backgroundWriter, 'javaScriptWriter' => $javaScriptWriter, 'resourceWriter' => $resourceWriter];
    }
    public function getServiceIds()
    {
        return ['otl', 'bmp', 'cache', 'cssManager', 'directWrite', 'fontCache', 'fontFileFinder', 'form', 'gradient', 'tableOfContents', 'tag', 'wmf', 'sizeConverter', 'colorConverter', 'hyphenator', 'localContentLoader', 'httpClient', 'assetFetcher', 'imageProcessor', 'protection', 'languageToFont', 'scriptToLanguage', 'writer', 'fontWriter', 'metadataWriter', 'imageWriter', 'formWriter', 'pageWriter', 'bookmarkWriter', 'optionalContentWriter', 'colorWriter', 'backgroundWriter', 'javaScriptWriter', 'resourceWriter'];
    }
}
