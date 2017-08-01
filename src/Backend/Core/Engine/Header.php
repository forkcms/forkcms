<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Core\Header\Asset;
use Common\Core\Header\AssetCollection;
use Common\Core\Header\JsData;
use Common\Core\Header\Minifier;
use Common\Core\Header\Priority;
use ForkCMS\App\KernelLoader;
use Symfony\Component\HttpKernel\KernelInterface;
use Backend\Core\Language\Language as BL;

/**
 * This class will be used to alter the head-part of the HTML-document that will be created by he Backend
 * Therefore it will handle meta-stuff (title, including JS, including CSS, ...)
 */
final class Header extends KernelLoader
{
    /**
     * The added css-files
     *
     * @var AssetCollection
     */
    private $cssFiles;

    /**
     * Data that will be passed to js
     *
     * @var JsData
     */
    private $jsData;

    /**
     * The added js-files
     *
     * @var AssetCollection
     */
    private $jsFiles;

    /**
     * Template instance
     *
     * @var TwigTemplate
     */
    private $template;

    /**
     * URL-instance
     *
     * @var Url
     */
    private $url;

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $container = $this->getContainer();
        $container->set('header', $this);

        $this->url = $container->get('url');
        $this->template = $container->get('template');

        $this->cssFiles = new AssetCollection(
            Minifier::css(
                $container->getParameter('site.path_www'),
                BACKEND_CACHE_URL . '/MinifiedCss/',
                BACKEND_CACHE_PATH . '/MinifiedCss/'
            )
        );
        $this->jsFiles = new AssetCollection(
            Minifier::js(
                $container->getParameter('site.path_www'),
                BACKEND_CACHE_URL . '/MinifiedJs/',
                BACKEND_CACHE_PATH . '/MinifiedJs/'
            )
        );
        $this->jsData = new JsData(
            [
                'interface_language' => $this->getInterfaceLanguage(),
                'debug' => $this->getContainer()->getParameter('kernel.debug'),
            ]
        );

        $this->addCoreJs();
        $this->addCoreCss();
    }

    private function addCoreJs(): void
    {
        $this->addJS('/js/vendors/jquery.min.js', 'Core', false, true, true, Priority::core());
        $this->addJS('/js/vendors/jquery-migrate.min.js', 'Core', false, true, true, Priority::core());
        $this->addJS('/js/vendors/jquery-ui.min.js', 'Core', false, true, true, Priority::core());
        $this->addJS('/js/vendors/bootstrap.min.js', 'Core', false, true, true, Priority::core());
        $this->addJS('/js/vendors/typeahead.bundle.min.js', 'Core', false, true, true, Priority::core());
        $this->addJS('/js/vendors/bootstrap-tagsinput.min.js', 'Core', false, true, true, Priority::core());
        $this->addJS('jquery/jquery.backend.js', 'Core', true, false, true, Priority::core());
        $this->addJS('utils.js', 'Core', true, false, true, Priority::core());
        $this->addJS('backend.js', 'Core', true, false, true, Priority::core());
    }

    private function addCoreCss(): void
    {
        $this->addCSS('/css/vendors/bootstrap-tagsinput.css', 'Core', true, true, true, Priority::core());
        $this->addCSS('/css/vendors/bootstrap-tagsinput-typeahead.css', 'Core', true, true, true, Priority::core());
        $this->addCSS('screen.css', 'Core', false, true, true, Priority::core());
        $this->addCSS('debug.css', 'Core', false, true, true, Priority::debug());
    }

    private function buildPathForModule(string $fileName, string $module, string $subDirectory): string
    {
        if ($module === 'Core') {
            return '/src/Backend/Core/' . $subDirectory . '/' . $fileName;
        }

        return '/src/Backend/Modules/' . $module . '/' . $subDirectory . '/' . $fileName;
    }

    /**
     * Add a CSS-file.
     *
     * If you don't specify a module, the current one will be used to automatically create the path to the file.
     * Automatic creation of the filename will result in
     *   src/Backend/Modules/MODULE/Layout/Css/FILE (for modules)
     *   src/Backend/Core/Layout/Css/FILE (for core)
     *
     * If you set overwritePath to true, the above-described automatic path creation will not happen, instead the
     * file-parameter will be used as path; which we then expect to be a full path (It has to start with a slash '/')
     *
     * @param string $file The name of the file to load.
     * @param string $module The module wherein the file is located.
     * @param bool $overwritePath Should we overwrite the full path?
     * @param bool $minify Should the CSS be minified?
     * @param bool $addTimestamp May we add a timestamp for caching purposes?
     * @param Priority $priority the files are added based on the priority
     *                           defaults to standard for full links or core or module for core or module css
     */
    public function addCSS(
        string $file,
        string $module = null,
        bool $overwritePath = false,
        bool $minify = true,
        bool $addTimestamp = false,
        Priority $priority = null
    ): void {
        $module = $module ?? $this->url->getModule();
        $this->cssFiles->add(
            new Asset(
                $overwritePath ? $file : $this->buildPathForModule($file, $module, 'Layout/Css'),
                $addTimestamp,
                $priority ?? ($overwritePath ? Priority::standard() : Priority::forModule($module))
            ),
            $minify && !$this->getContainer()->getParameter('kernel.debug')
        );
    }

    /**
     * Add a JS-file.
     * If you don't specify a module, the current one will be used
     * If you set overwritePath to true we expect a full path (It has to start with a /)
     *
     * @param string $file The file to load.
     * @param string $module The module wherein the file is located.
     * @param bool $minify Should the module be minified?
     * @param bool $overwritePath Should we overwrite the full path?
     * @param bool $addTimestamp May we add a timestamp for caching purposes?
     * @param Priority $priority the files are added based on the priority
     *                           defaults to standard for full links or core or module for core or module css
     */
    public function addJS(
        string $file,
        string $module = null,
        bool $minify = true,
        bool $overwritePath = false,
        bool $addTimestamp = false,
        Priority $priority = null
    ): void {
        $module = $module ?? $this->url->getModule();

        $this->jsFiles->add(
            new Asset(
                $overwritePath ? $file : $this->buildPathForModule($file, $module ?? $this->url->getModule(), 'Js'),
                $addTimestamp,
                $priority ?? ($overwritePath ? Priority::standard() : Priority::forModule($module))
            ),
            $minify
        );
    }

    /**
     * Add data into the jsData
     *
     * @param string $module The name of the module.
     * @param string $key The key whereunder the value will be stored.
     * @param mixed $value The value
     */
    public function addJsData(string $module, string $key, $value): void
    {
        $this->jsData->add($module, $key, $value);
    }

    /**
     * Parse the header into the template
     */
    public function parse(): void
    {
        $this->template->assign('page_title', BL::getLabel($this->url->getModule()));
        $this->cssFiles->parse($this->template, 'cssFiles');
        $this->jsFiles->parse($this->template, 'jsFiles');

        $this->jsData->add('site', 'domain', SITE_DOMAIN);
        $this->jsData->add('editor', 'language', $this->getCKEditorLanguage());

        if (!empty($this->get('fork.settings')->get('Core', 'theme'))) {
            $this->jsData->add('theme', 'theme', $this->get('fork.settings')->get('Core', 'theme'));
            $themePath = FRONTEND_PATH . '/Themes/' . $this->get('fork.settings')->get('Core', 'theme');
            $this->jsData->add('theme', 'path', $themePath);
            $this->jsData->add('theme', 'has_css', is_file($themePath . '/Core/Layout/Css/screen.css'));
            $this->jsData->add('theme', 'has_editor_css', is_file($themePath . '/Core/Layout/Css/editor_content.css'));
        }
        $this->template->assign('jsData', $this->jsData);
    }

    /**
     * @return string
     */
    private function getInterfaceLanguage(): string
    {
        if (Authentication::getUser()->isAuthenticated()) {
            return (string) Authentication::getUser()->getSetting('interface_language');
        }

        return BL::getInterfaceLanguage();
    }

    /**
     * @return string
     */
    private function getCKEditorLanguage(): string
    {
        $language = $this->getInterfaceLanguage();

        // CKeditor has support for simplified Chinese, but the language is called zh-cn instead of zn
        if ($language === 'zh') {
            return 'zh-cn';
        }

        return $language;
    }
}
