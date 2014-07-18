<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use MatthiasMullie\Minify;

use Common\Cookie as CommonCookie;

use Frontend\Core\Engine\Base\Object as FrontendBaseObject;

/**
 * This class will be used to alter the head-part of the HTML-document that will be created by the frontend
 * Therefore it will handle meta-stuff (title, including JS, including CSS, ...)
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class Header extends FrontendBaseObject
{
    /**
     * The canonical URL
     *
     * @var string
     */
    private $canonical;

    /**
     * The added css-files
     *
     * @var    array
     */
    private $cssFiles = array();

    /**
     * Data that will be passed to js
     *
     * @var array
     */
    private $jsData = array();

    /**
     * The added js-files
     *
     * @var    array
     */
    private $jsFiles = array();

    /**
     * The links
     *
     * @var    array
     */
    private $links = array();

    /**
     * Meta data
     *
     * @var    array
     */
    private $meta = array();

    /**
     * The custom meta data
     *
     * @var    string
     */
    private $metaCustom = '';

    /**
     * Page title
     *
     * @var    string
     */
    private $pageTitle;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);
        $this->getContainer()->set('header', $this);

        // add some default CSS files
        $this->addCSS('/src/Frontend/Core/Layout/Css/jquery_ui/jquery_ui.css', false);
        $this->addCSS('/src/Frontend/Core/Layout/Css/screen.css');

        // debug stylesheet
        if (SPOON_DEBUG) {
            $this->addCSS('/src/Frontend/Core/Layout/Css/debug.css');
        }

        // add default javascript-files
        $this->addJS('/src/Frontend/Core/Js/jquery/jquery.js', false);
        $this->addJS('/src/Frontend/Core/Js/jquery/jquery.ui.js', false);
        $this->addJS('/src/Frontend/Core/Js/jquery/jquery.frontend.js', true);
        $this->addJS('/src/Frontend/Core/Js/utils.js', true);
        $this->addJS('/src/Frontend/Core/Js/frontend.js', false);
    }

    /**
     * Add a CSS file into the array
     *
     * @param string $file         The path for the CSS-file that should be loaded.
     * @param bool   $minify       Should the CSS be minified?
     * @param bool   $addTimestamp May we add a timestamp for caching purposes?
     */
    public function addCSS($file, $minify = true, $addTimestamp = null)
    {
        $file = (string) $file;
        $minify = (bool) $minify;
        $addTimestamp = (bool) $addTimestamp;
        $file = Theme::getPath($file);

        // no minifying when debugging
        if (SPOON_DEBUG) {
            $minify = false;
        }

        if ($minify) {
            $file = $this->minifyCSS($file);
        }

        $inArray = false;
        foreach ($this->cssFiles as $row) {
            if ($row['file'] == $file) {
                $inArray = true;
            }
        }

        // add to array if it isn't there already
        if (!$inArray) {
            $temp['file'] = (string) $file;
            $temp['add_timestamp'] = $addTimestamp;
            $this->cssFiles[] = $temp;
        }
    }

    /**
     * Add a javascript file into the array
     *
     * @param string $file         The path to the javascript-file that should be loaded.
     * @param bool   $minify       Should the file be minified?
     * @param bool   $addTimestamp May we add a timestamp for caching purposes?
     */
    public function addJS($file, $minify = true, $addTimestamp = null)
    {
        $file = (string) $file;
        $minify = (bool) $minify;
        $addTimestamp = (bool) $addTimestamp;

        // get file path
        if (substr($file, 0, 4) != 'http') {
            $file = Theme::getPath($file);
        }

        // no minifying when debugging
        if (SPOON_DEBUG) {
            $minify = false;
        }

        if ($minify) {
            $file = $this->minifyJS($file);
        }

        // already in array?
        if (!in_array(array('file' => $file, 'add_timestamp' => $addTimestamp), $this->jsFiles)) {
            $this->jsFiles[] = array('file' => $file, 'add_timestamp' => $addTimestamp);
        }
    }

    /**
     * Add data into the jsData
     *
     * @param string $module The name of the module.
     * @param string $key    The key whereunder the value will be stored.
     * @param mixed  $value  The value
     */
    public function addJsData($module, $key, $value)
    {
        $this->jsData[$module][$key] = $value;
    }

    /**
     * Add link
     *
     * @param array $attributes The attributes to parse.
     * @param bool  $overwrite  Should we overwrite the current value?
     * @param mixed $uniqueKeys Which keys can we use to decide if an item is unique.
     */
    public function addLink(array $attributes, $overwrite = false, $uniqueKeys = null)
    {
        $overwrite = (bool) $overwrite;
        $uniqueKeys = (array) $uniqueKeys;

        if ($uniqueKeys == null) {
            $uniqueKeys = array('rel', 'type', 'title');
        }

        // stop if the content is empty
        if (isset($attributes['href']) && $attributes['href'] == '') {
            return;
        }

        ksort($uniqueKeys);

        $uniqueKey = '';
        foreach ($uniqueKeys as $key) {
            if (isset($attributes[$key])) {
                $uniqueKey .= $attributes[$key] . '|';
            }
        }

        // is the metadata already available?
        if (isset($this->links[$uniqueKey])) {
            if ($overwrite) {
                $this->links[$uniqueKey] = $attributes;
            }
        } else {
            $this->links[$uniqueKey] = $attributes;
        }
    }

    /**
     * Add meta data
     *
     * @param array $attributes The attributes to parse.
     * @param bool  $overwrite  Should we overwrite the current value?
     * @param mixed $uniqueKeys Which keys can we use to decide if an item is unique.
     */
    public function addMetaData(array $attributes, $overwrite = false, $uniqueKeys = null)
    {
        $overwrite = (bool) $overwrite;
        $uniqueKeys = (array) $uniqueKeys;
        if ($uniqueKeys == null) {
            $uniqueKeys = array('name');
        }

        // stop if the content is empty
        if (isset($attributes['content']) && $attributes['content'] == '') {
            return;
        }

        ksort($uniqueKeys);

        $uniqueKey = '';
        foreach ($uniqueKeys as $key) {
            if (isset($attributes[$key])) {
                $uniqueKey .= $attributes[$key] . '|';
            }
        }

        // is the metadata already available?
        if (isset($this->meta[$uniqueKey])) {
            // should we overwrite the key?
            if ($overwrite) {
                $this->meta[$uniqueKey] = $attributes;
            } else {
                // some keys should be appended instead of ignored.
                if (in_array($uniqueKey, array('description|', 'keywords|', 'robots|'))) {
                    foreach ($attributes as $key => $value) {
                        if (isset($this->meta[$uniqueKey][$key]) && $key == 'content') {
                            $this->meta[$uniqueKey][$key] .= ', ' . $value;
                        } else {
                            $this->meta[$uniqueKey][$key] = $value;
                        }
                    }
                }
            }
        } else {
            $this->meta[$uniqueKey] = $attributes;
        }
    }

    /**
     * Add meta-description, somewhat a shortcut for the addMetaData-method
     *
     * @param string $value     The description.
     * @param bool   $overwrite Should we overwrite the previous value?
     */
    public function addMetaDescription($value, $overwrite = false)
    {
        $this->addMetaData(array('name' => 'description', 'content' => $value), $overwrite);
    }

    /**
     * Add meta-keywords, somewhat a shortcut for the addMetaData-method
     *
     * @param string $value     The description.
     * @param bool   $overwrite Should we overwrite the previous value?
     */
    public function addMetaKeywords($value, $overwrite = false)
    {
        $this->addMetaData(array('name' => 'keywords', 'content' => $value), $overwrite);
    }

    /**
     * Add Open Graph data
     *
     * @param string $key       The key (without og:).
     * @param string $value     The value.
     * @param bool   $overwrite Should we overwrite the previous value?
     */
    public function addOpenGraphData($key, $value, $overwrite = false)
    {
        $this->addMetaData(array('property' => 'og:' . $key, 'content' => $value), $overwrite, 'property');
    }

    /**
     * Add Open Graph image
     *
     * @param string $image     The path to the image.
     * @param bool   $overwrite Should we overwrite the previous value?
     */
    public function addOpenGraphImage($image, $overwrite = false)
    {
        // remove site url from path
        $image = str_replace(SITE_URL, '', $image);

        // check if it no longer points to an absolute uri
        if (substr($image, 0, 7) != SITE_PROTOCOL . '://') {
            if (!is_file(PATH_WWW . $image)) {
                return;
            }
            $image = SITE_URL . $image;
        }

        // add to metadata
        $this->addMetaData(
            array('property' => 'og:image', 'content' => $image),
            $overwrite,
            array('property', 'content')
        );
        if (SITE_PROTOCOL == 'https') {
            $this->addMetaData(
                array('property' => 'og:image:secure_url', 'content' => $image),
                $overwrite,
                array('property', 'content')
            );
        }
    }

    /**
     * Sort function for CSS-files
     *
     * @param array $cssFiles The css files to sort.
     * @return array
     */
    private function cssSort($cssFiles)
    {
        $cssFiles = (array) $cssFiles;

        $i = 0;
        $aTemp = array();

        foreach ($cssFiles as $file) {
            // debug should be the last file
            if (strpos($file['file'], 'debug.css') !== false) {
                $aTemp['e' . $i][] = $file;
            } else {
                $aTemp['a' . $i][] = $file;
                $i++;
            }
        }

        ksort($aTemp);

        $return = array();

        foreach ($aTemp as $aFiles) {
            foreach ($aFiles as $file) {
                $return[] = $file;
            }
        }

        return $return;
    }

    /**
     * Extract images from content that can be added add Open Graph image
     *
     * @param string $content The content (where from to extract the images).
     */
    public function extractOpenGraphImages($content)
    {
        $matches = array();

        // check if any img-tags are present in the content
        if (preg_match_all('/<img.*?src="(.*?)".*?\/>/i', $content, $matches)) {
            // loop all found images and add to Open Graph metadata
            foreach ($matches[1] as $image) {
                $this->addOpenGraphImage($image);
            }
        }
    }

    /**
     * Get all added CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $this->cssFiles = $this->cssSort($this->cssFiles);

        return $this->cssFiles;
    }

    /**
     * get all added javascript files
     *
     * @return array
     */
    public function getJSFiles()
    {
        return $this->jsFiles;
    }

    /**
     * Get all links
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Get meta
     *
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Get the custom meta
     *
     * @return string
     */
    public function getMetaCustom()
    {
        return $this->metaCustom;
    }

    /**
     * Get all attributes for meta tag specified by the attribute and the value for that attribute.
     *
     * @param string $attribute      The attribute to match on.
     * @param string $attributeValue The value for the unique attribute.
     * @return array
     */
    public function getMetaValue($attribute, $attributeValue)
    {
        foreach ($this->meta as $item) {
            // if the key and the value match we return the item
            if (isset($item[$attribute]) && $item[$attribute] == $attributeValue) {
                return $item;
            }
        }
    }

    /**
     * Get the page title
     *
     * @return string
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * Minify a CSS-file
     *
     * @param string $file The file to be minified.
     * @return string
     */
    private function minifyCSS($file)
    {
        $fileName = md5($file) . '.css';
        $finalURL = FRONTEND_CACHE_URL . '/MinifiedCss/' . $fileName;
        $finalPath = FRONTEND_CACHE_PATH . '/MinifiedCss/' . $fileName;

        // check that file does not yet exist or has been updated already
        $fs = new Filesystem();
        if (!$fs->exists($finalPath) || filemtime(PATH_WWW . $file) > filemtime($finalPath)) {
            // create directory if it does not exist
            if (!$fs->exists(dirname($finalPath))) {
                $fs->mkdir(dirname($finalPath));
            }

            // minify the file
            $css = new Minify\CSS(PATH_WWW . $file);
            $css->minify($finalPath);
        }

        return $finalURL;
    }

    /**
     * Minify a javascript-file
     *
     * @param string $file The file to be minified.
     * @return string
     */
    private function minifyJS($file)
    {
        $fileName = md5($file) . '.js';
        $finalURL = FRONTEND_CACHE_URL . '/MinifiedJs/' . $fileName;
        $finalPath = FRONTEND_CACHE_PATH . '/MinifiedJs/' . $fileName;

        // check that file does not yet exist or has been updated already
        $fs = new Filesystem();
        if (!$fs->exists($finalPath) || filemtime(PATH_WWW . $file) > filemtime($finalPath)) {
            // create directory if it does not exist
            if (!$fs->exists(dirname($finalPath))) {
                $fs->mkdir(dirname($finalPath));
            }

            // minify the file
            $js = new Minify\JS(PATH_WWW . $file);
            $js->minify($finalPath);
        }

        return $finalURL;
    }

    /**
     * Parse the header into the template
     */
    public function parse()
    {
        $this->parseFacebook();
        $this->parseSeo();

        // in debug mode we don't want our pages to be indexed.
        if (SPOON_DEBUG) {
            $this->addMetaData(array('name' => 'robots', 'content' => 'noindex, nofollow'), true);
        }

        $this->parseMetaAndLinks();
        $this->parseCSS();
        $this->parseJS();
        $this->parseCustomHeaderHTMLAndGoogleAnalytics();

        $this->tpl->assign('pageTitle', (string) $this->getPageTitle());
        $this->tpl->assign(
            'siteTitle',
            (string) Model::getModuleSetting('Core', 'site_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE)
        );
    }

    /**
     * Parse the CSS-files
     */
    private function parseCSS()
    {
        $cssFiles = array();
        $existingCSSFiles = $this->getCSSFiles();

        // if there aren't any CSS-files added we don't need to do something
        if (!empty($existingCSSFiles)) {
            foreach ($existingCSSFiles as $file) {
                if ($file['add_timestamp'] !== false) {
                    $file['file'] .= (strpos(
                                          $file['file'],
                                          '?'
                                      ) !== false) ? '&m=' . LAST_MODIFIED_TIME : '?m=' . LAST_MODIFIED_TIME;
                }
                $cssFiles[] = $file;
            }
        }

        $this->tpl->assign('cssFiles', $cssFiles);
    }

    /**
     * Parse Google Analytics
     */
    private function parseCustomHeaderHTMLAndGoogleAnalytics()
    {
        // get the data
        $siteHTMLHeader = (string) Model::getModuleSetting('Core', 'site_html_header', null);
        $siteHTMLFooter = (string) Model::getModuleSetting('Core', 'site_html_footer', null);
        $webPropertyId = Model::getModuleSetting('Analytics', 'web_property_id', null);
        $type = Model::getModuleSetting('Analytics', 'tracking_type', 'universal_analytics');

        // search for the webpropertyId in the header and footer, if not found we should build the GA-code
        if (
            $webPropertyId != '' &&
            strpos($siteHTMLHeader, $webPropertyId) === false &&
            strpos($siteHTMLFooter, $webPropertyId) === false
        ) {
            $anonymize = (
                Model::getModuleSetting('Core', 'show_cookie_bar', false) &&
                !CommonCookie::hasAllowedCookies()
            );

            switch ($type) {
                case 'classic_analytics':
                    $trackingCode = '<script>
                                        var _gaq = _gaq || [];
                                        _gaq.push([\'_setAccount\', \'' . $webPropertyId . '\']);
                                        _gaq.push([\'_setDomainName\', \'none\']);
                                        _gaq.push([\'_trackPageview\']);
                                    ';
                    if ($anonymize) {
                        $trackingCode .= '_gaq.push([\'_gat._anonymizeIp\']);';
                    }
                    $trackingCode .= '
                                        (function() {
                                            var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
                                            ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
                                            var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
                                        })();
                                    </script>';
                    break;
                case 'display_advertising':
                    $trackingCode = '<script>
                                        var _gaq = _gaq || [];
                                        _gaq.push([\'_setAccount\', \'' . $webPropertyId . '\']);
                                        _gaq.push([\'_setDomainName\', \'none\']);
                                        _gaq.push([\'_trackPageview\']);
                                    ';
                    if ($anonymize) {
                        $trackingCode .= '_gaq.push([\'_gat._anonymizeIp\']);';
                    }
                    $trackingCode .= '
                                        (function() {
                                            var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
                                            ga.src = (\'https:\' == document.location.protocol ? \'https://\' : \'http://\') + \'stats.g.doubleclick.net/dc.js\';
                                            var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
                                        })();
                                    </script>';
                    break;
                case 'universal_analytics':
                    $url = $this->getContainer()->get('url');
                    $trackingCode = '<script>
                                      (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
                                      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                                      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                                      })(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');
                                      ga(\'create\', \'' . $webPropertyId . '\', \'' . $url->getHost() . '\');
                                    ';

                    if ($anonymize) {
                        $trackingCode .= 'ga(\'send\', \'pageview\', {\'anonymizeIp\': true});';
                    } else {
                        $trackingCode .= 'ga(\'send\', \'pageview\');';
                    }
                    $trackingCode .= '</script>';
                    break;
                default:
                    throw new \Exception('Unknown type. (' . $type . ')');
            }

            $siteHTMLHeader .= "\n" . $trackingCode;
        }

        // store language
        $this->jsData['FRONTEND_LANGUAGE'] = FRONTEND_LANGUAGE;

        // encode and add
        $jsData = json_encode($this->jsData);
        $siteHTMLHeader .= "\n" . '<script>var jsData = ' . $jsData . '</script>';

        // assign site wide html
        $this->tpl->assign('siteHTMLHeader', trim($siteHTMLHeader));
    }

    /**
     * Parse Facebook related header-data
     */
    private function parseFacebook()
    {
        $parseFacebook = false;
        $facebookAdminIds = Model::getModuleSetting('Core', 'facebook_admin_ids', null);
        $facebookAppId = Model::getModuleSetting('Core', 'facebook_app_id', null);

        // check if facebook admins are set
        if ($facebookAdminIds !== null) {
            $this->addMetaData(
                array(
                    'property' => 'fb:admins',
                    'content' => $facebookAdminIds
                ),
                true,
                array('property')
            );
            $parseFacebook = true;
        }

        // check if no facebook admin is set but an app is configured we use the application as an admin
        if ($facebookAdminIds == '' && $facebookAppId !== null) {
            $this->addMetaData(
                array(
                    'property' => 'fb:app_id',
                    'content' => $facebookAppId
                ),
                true,
                array('property')
            );
            $parseFacebook = true;
        }

        // should we add extra open-graph data?
        if ($parseFacebook) {
            // build correct locale
            switch (FRONTEND_LANGUAGE) {
                case 'en':
                    $locale = 'en_US';
                    break;

                case 'zh':
                    $locale = 'zh_CN';
                    break;

                case 'cs':
                    $locale = 'cs_CZ';
                    break;

                case 'el':
                    $locale = 'el_GR';
                    break;

                case 'ja':
                    $locale = 'ja_JP';
                    break;

                case 'sv':
                    $locale = 'sv_SE';
                    break;

                case 'uk':
                    $locale = 'uk_UA';
                    break;

                default:
                    $locale = strtolower(FRONTEND_LANGUAGE) . '_' . strtoupper(FRONTEND_LANGUAGE);
            }

            $this->addOpenGraphData('locale', $locale);

            // if a default image has been set for facebook, assign it
            $this->addOpenGraphImage('/frontend/themes/' . Theme::getTheme() . '/facebook.png');
            $this->addOpenGraphImage('/facebook.png');
        }
    }

    /**
     * Parse the JS-files
     */
    private function parseJS()
    {
        $jsFiles = array();
        $existingJSFiles = $this->getJSFiles();

        // if there aren't any JS-files added we don't need to do something
        if (!empty($existingJSFiles)) {
            // some files should be cached, even if we don't want cached (mostly libraries)
            $ignoreCache = array(
                '/src/Frontend/Core/Js/Jquery/jquery.js',
                '/src/Frontend/Core/Js/Jquery/jquery.ui.js'
            );

            foreach ($existingJSFiles as $file) {
                // some files shouldn't be uncacheable
                if (in_array($file['file'], $ignoreCache) || $file['add_timestamp'] === false) {
                    $file = array('file' => $file['file']);
                } else {
                    // add last modified time
                    $modifiedTime = (strpos(
                                         $file['file'],
                                         '?'
                                     ) !== false) ? '&amp;m=' . LAST_MODIFIED_TIME : '?m=' . LAST_MODIFIED_TIME;
                    $file = array('file' => $file['file'] . $modifiedTime);
                }

                $jsFiles[] = $file;
            }
        }

        $this->tpl->assign('jsFiles', $jsFiles);
    }

    /**
     * Parse the meta and link-tags
     */
    private function parseMetaAndLinks()
    {
        $meta = '';
        foreach ($this->meta as $attributes) {
            $meta .= '<meta ';
            foreach ($attributes as $key => $value) {
                $meta .= $key . '="' . $value . '" ';
            }
            $meta = trim($meta);
            $meta .= '>' . "\n";
        }

        $link = '';
        foreach ($this->links as $attributes) {
            $link .= '<link ';
            foreach ($attributes as $key => $value) {
                $link .= $key . '="' . $value . '" ';
            }
            $link = trim($link);
            $link .= '>' . "\n";
        }

        $this->tpl->assign('meta', $meta . "\n" . $link);
        $this->tpl->assign('metaCustom', $this->getMetaCustom());
    }

    /**
     * Parse SEO specific data
     */
    private function parseSeo()
    {
        // when on the homepage of the default language, set the clean site url as canonical, because of redirect fix
        $queryString = trim($this->URL->getQueryString(), '/');
        $language = Model::getModuleSetting('Core', 'default_language', SITE_DEFAULT_LANGUAGE);
        if ($queryString == $language) {
            $this->canonical = rtrim(SITE_URL, '/');
        }

        // any canonical URL provided?
        if ($this->canonical != '') {
            $url = $this->canonical;
        } else {
            // get the chunks of the current url
            $urlChunks = parse_url($this->URL->getQueryString());

            // a canonical url should contain the domain. So make sure you
            // redirect your website to a single url with .htaccess
            $url = rtrim(SITE_URL, '/');
            if (isset($urlChunks['port'])) {
                $url .= ':' . $urlChunks['port'];
            }
            if (isset($urlChunks['path'])) {
                $url .= '/' . $urlChunks['path'];
            }

            // any items provided through GET?
            if (isset($urlChunks['query'])) {
                // the items we should add into the canonical url
                $itemsToAdd = array('page');
                $addToUrl = array();

                // loop all items in GET and check if we should ignore them
                foreach ($_GET as $key => $value) {
                    if (in_array($key, $itemsToAdd)) {
                        $addToUrl[$key] = $value;
                    }
                }

                // add GET-params
                if (!empty($addToUrl)) {
                    $url .= '?' . http_build_query($addToUrl);
                }
            }
        }

        // prevent against xss
        $url = (SPOON_CHARSET == 'utf-8') ? \SpoonFilter::htmlspecialchars($url) : \SpoonFilter::htmlentities($url);
        $this->addLink(array('rel' => 'canonical', 'href' => $url));

        if (Model::getModuleSetting('Core', 'seo_noodp', false)) {
            $this->addMetaData(
                array('name' => 'robots', 'content' => 'noodp')
            );
        }
        if (Model::getModuleSetting('Core', 'seo_noydir', false)) {
            $this->addMetaData(
                array('name' => 'robots', 'content' => 'noydir')
            );
        }
    }

    /**
     * Set the canonical URL
     *
     * @param string $url The Canonical URL.
     */
    public function setCanonicalUrl($url)
    {
        $url = (string) $url;

        // convert relative url
        if (substr($url, 0, 1) == '/') {
            $url = SITE_URL . $url;
        }

        // store
        $this->canonical = $url;
    }

    /**
     * Set the custom meta
     *
     * @param string $meta The meta data to set.
     */
    public function setMetaCustom($meta)
    {
        $this->metaCustom = (string) $meta;
    }

    /**
     * Set the page title
     *
     * @param string $value     The page title to be set or to be prepended.
     * @param bool   $overwrite Should the existing page title be overwritten?
     */
    public function setPageTitle($value, $overwrite = false)
    {
        $value = trim((string) $value);
        $overwrite = (bool) $overwrite;

        // overwrite? reset the current value
        if ($overwrite) {
            $this->pageTitle = $value;
        } else {
            // empty value given?
            if (empty($value)) {
                $this->pageTitle = Model::getModuleSetting(
                    'Core',
                    'site_title_' . FRONTEND_LANGUAGE,
                    SITE_DEFAULT_TITLE
                );
            } else {
                // if the current page title is empty we should add the site title
                if ($this->pageTitle == '') {
                    $this->pageTitle = $value . ' -  ' .
                                       Model::getModuleSetting(
                                           'Core',
                                           'site_title_' . FRONTEND_LANGUAGE,
                                           SITE_DEFAULT_TITLE
                                       );
                } else {
                    // prepend the value to the current page title
                    $this->pageTitle = $value . ' - ' . $this->pageTitle;
                }
            }
        }
    }
}
