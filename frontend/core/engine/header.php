<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will be used to alter the head-part of the HTML-document that will be created by the frontend
 * Therefore it will handle meta-stuff (title, including JS, including CSS, ...)
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class FrontendHeader extends FrontendBaseObject
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
	 * @var	array
	 */
	private $cssFiles = array();

	/**
	 * The added js-files
	 *
	 * @var	array
	 */
	private $jsFiles = array();

	/**
	 * The links
	 *
	 * @var	array
	 */
	private $links = array();

	/**
	 * Meta data
	 *
	 * @var	array
	 */
	private $meta = array();

	/**
	 * The custom meta data
	 *
	 * @var	string
	 */
	private $metaCustom = '';

	/**
	 * Pagetitle
	 *
	 * @var	string
	 */
	private $pageTitle;

	public function __construct()
	{
		parent::__construct();

		// store in reference
		Spoon::set('header', $this);

		// add some default CSS files
		$this->addCSS('/frontend/core/layout/css/jquery_ui/jquery_ui.css');
		$this->addCSS('/frontend/core/layout/css/screen.css');

		// debug stylesheet
		if(SPOON_DEBUG) $this->addCSS('/frontend/core/layout/css/debug.css');

		// add default javascript-files
		$this->addJS('/frontend/core/js/jquery/jquery.js', false);
		$this->addJS('/frontend/core/js/jquery/jquery.ui.js', false);
		$this->addJS('/frontend/core/js/jquery/jquery.frontend.js', true);
		$this->addJS('/frontend/core/js/utils.js', true);
		$this->addJS('/frontend/core/js/frontend.js', false, true);
	}

	/**
	 * Add a CSS file into the array
	 *
	 * @param string $file The path for the CSS-file that should be loaded.
	 * @param bool[optional] $minify Should the CSS be minified?
	 * @param bool[optional] $addTimestamp May we add a timestamp for caching purposes?
	 */
	public function addCSS($file, $minify = true, $addTimestamp = null)
	{
		$file = (string) $file;
		$minify = (bool) $minify;
		$addTimestamp = (bool) $addTimestamp;

		// get file path
		$file = FrontendTheme::getPath($file);

		// no minifying when debugging
		if(SPOON_DEBUG) $minify = false;

		// try to minify
		if($minify) $file = $this->minifyCSS($file);

		// in array
		$inArray = false;

		// check if the file already exists in the array
		foreach($this->cssFiles as $row) if($row['file'] == $file) $inArray = true;

		// add to array if it isn't there already
		if(!$inArray)
		{
			// build temporary array
			$temp['file'] = (string) $file;
			$temp['add_timestamp'] = $addTimestamp;

			// add to files
			$this->cssFiles[] = $temp;
		}
	}

	/**
	 * Add a javascript file into the array
	 *
	 * @param  string $file The path to the javascript-file that should be loaded.
	 * @param bool[optional] $minify Should the file be minified?
	 * @param bool[optional] $parseThroughPHP Should the file be parsed through PHP?
	 * @param bool[optional] $addTimestamp May we add a timestamp for caching purposes?
	 */
	public function addJS($file, $minify = true, $parseThroughPHP = false, $addTimestamp = null)
	{
		$file = (string) $file;
		$minify = (bool) $minify;
		$parseThroughPHP = (bool) $parseThroughPHP;
		$addTimestamp = (bool) $addTimestamp;

		// get file path
		if(substr($file, 0, 4) != 'http') $file = FrontendTheme::getPath($file);

		// no minifying when debugging
		if(SPOON_DEBUG) $minify = false;

		// no minifying when parsing through PHP
		if($parseThroughPHP) $minify = false;

		// if parse through PHP we should alter the path
		if($parseThroughPHP)
		{
			// process the path
			$chunks = explode('/', str_replace(array('/frontend/modules/', '/frontend/core'), '', $file));

			// validate
			if(!isset($chunks[count($chunks) - 3])) throw new FrontendException('Invalid file (' . $file . ').');

			// fetch values
			$module = $chunks[count($chunks) - 3];
			$file = $chunks[count($chunks) - 1];

			// reset module for core
			if($module == '') $module = 'core';

			// alter the file
			$file = '/frontend/js.php?module=' . $module . '&amp;file=' . $file . '&amp;language=' . FRONTEND_LANGUAGE;
		}

		// try to minify
		if($minify) $file = $this->minifyJS($file);

		// already in array?
		if(!in_array(array('file' => $file, 'add_timestamp' => $addTimestamp), $this->jsFiles))
		{
			// add to files
			$this->jsFiles[] = array('file' => $file, 'add_timestamp' => $addTimestamp);
		}
	}

	/**
	 * Add link
	 *
	 * @param array $attributes The attributes to parse.
	 * @param bool[optional] $overwrite Should we overwrite the current value?
	 * @param mixed[optional] $uniqueKeys Which keys can we use to decide if an item is unique.
	 */
	public function addLink(array $attributes, $overwrite = false, $uniqueKeys = null)
	{
		$overwrite = (bool) $overwrite;
		$uniqueKeys = (array) $uniqueKeys;

		if($uniqueKeys == null) $uniqueKeys = array('rel', 'type', 'title');

		// stop if the content is empty
		if(isset($attributes['href']) && $attributes['href'] == '') return;

		// sort the keys
		ksort($uniqueKeys);

		// build key
		$uniqueKey = '';
		foreach($uniqueKeys as $key) if(isset($attributes[$key])) $uniqueKey .= $attributes[$key] . '|';

		// is the metadata already available?
		if(isset($this->links[$uniqueKey]))
		{
			// should we overwrite the key?
			if($overwrite) $this->links[$uniqueKey] = $attributes;
		}

		// add into the array
		else $this->links[$uniqueKey] = $attributes;
	}

	/**
	 * Add meta data
	 *
	 * @param array $attributes The attributes to parse.
	 * @param bool[optional] $overwrite Should we overwrite the current value?
	 * @param mixed[optional] $uniqueKeys Which keys can we use to decide if an item is unique.
	 */
	public function addMetaData(array $attributes, $overwrite = false, $uniqueKeys = null)
	{
		// redefine
		$overwrite = (bool) $overwrite;
		$uniqueKeys = (array) $uniqueKeys;
		if($uniqueKeys == null) $uniqueKeys = array('name');

		// stop if the content is empty
		if(isset($attributes['content']) && $attributes['content'] == '') return;

		// sort the keys
		ksort($uniqueKeys);

		// build key
		$uniqueKey = '';
		foreach($uniqueKeys as $key) if(isset($attributes[$key])) $uniqueKey .= $attributes[$key] . '|';

		// is the metadata already available?
		if(isset($this->meta[$uniqueKey]))
		{
			// should we overwrite the key?
			if($overwrite) $this->meta[$uniqueKey] = $attributes;
			else
			{
				// some keys should be appended instead of ignored.
				if(in_array($uniqueKey, array('description|', 'keywords|', 'robots|')))
				{
					foreach($attributes as $key => $value)
					{
						if(isset($this->meta[$uniqueKey][$key]) && $key == 'content') $this->meta[$uniqueKey][$key] .= ', ' . $value;
						else $this->meta[$uniqueKey][$key] = $value;
					}
				}
			}
		}

		// add into the array
		else $this->meta[$uniqueKey] = $attributes;
	}

	/**
	 * Add meta-description, somewhat a shortcut for the addMetaData-method
	 *
	 * @param string $value The description.
	 * @param bool[optional] $overwrite Should we overwrite the previous value?
	 */
	public function addMetaDescription($value, $overwrite = false)
	{
		$this->addMetaData(array('name' => 'description', 'content' => $value), $overwrite);
	}

	/**
	 * Add meta-keywords, somewhat a shortcut for the addMetaData-method
	 *
	 * @param string $value The description.
	 * @param bool[optional] $overwrite Should we overwrite the previous value?
	 */
	public function addMetaKeywords($value, $overwrite = false)
	{
		$this->addMetaData(array('name' => 'keywords', 'content' => $value), $overwrite);
	}

	/**
	 * Add Open Graph data
	 *
	 * @param string $key The key (without og:).
	 * @param string $value The value.
	 * @param bool[optional] $overwrite Should we overwrite the previous value?
	 */
	public function addOpenGraphData($key, $value, $overwrite = false)
	{
		$this->addMetaData(array('property' => 'og:' . $key, 'content' => $value), $overwrite, 'property');
	}

	/**
	 * Add Open Graph image
	 *
	 * @param string $image The path to the image.
	 * @param bool[optional] $overwrite Should we overwrite the previous value?
	 */
	public function addOpenGraphImage($image, $overwrite = false)
	{
		// remove site url from path
		$image = str_replace(SITE_URL, '', $image);

		// check if it no longer points to an absolute uri
		if(substr($image, 0, 7) != 'http://')
		{
			// check if image exists
			if(!SpoonFile::exists(PATH_WWW . $image)) return;

			// convert to absolute path
			$image = SITE_URL . $image;
		}

		// add to metadata
		$this->addMetaData(array('property' => 'og:image', 'content' => $image), $overwrite, array('property', 'content'));
	}

	/**
	 * Sort function for CSS-files
	 *
	 * @return array
	 */
	private function cssSort($cssFiles)
	{
		$cssFiles = (array) $cssFiles;

		// init vars
		$i = 0;
		$aTemp = array();

		// loop files
		foreach($cssFiles as $file)
		{
			// debug should be the last file
			if(strpos($file['file'], 'debug.css') !== false) $aTemp['e' . $i][] = $file;

			else
			{
				// add file
				$aTemp['a' . $i][] = $file;

				// increase
				$i++;
			}
		}

		// key sort
		ksort($aTemp);

		// init var
		$return = array();

		// loop by key
		foreach($aTemp as $aFiles)
		{
			// loop files
			foreach($aFiles as $file) $return[] = $file;
		}

		// reset property
		return $return;
	}

	/**
	 * Extract images from content that can be added add Open Graph image
	 *
	 * @param string $content The content (wherefrom to extract the images).
	 */
	public function extractOpenGraphImages($content)
	{
		// try to get an image in the content
		$matches = array();

		// check if images are present in the content
		if(preg_match_all('/<img.*?src="(.*?)".*?\/>/i', $content, $matches))
		{
			// loop all found images and add to Open Graph metadata
			foreach($matches[1] as $image) $this->addOpenGraphImage($image);
		}
	}

	/**
	 * Get all added CSS files
	 *
	 * @return array
	 */
	public function getCSSFiles()
	{
		// sort the cssfiles
		$this->cssFiles = $this->cssSort($this->cssFiles);

		// fetch files
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
	 * @param string $attribute The attribute to match on.
	 * @param string $attributeValue The value for the unique attribute.
	 * @return array
	 */
	public function getMetaValue($attribute, $attributeValue)
	{
		// loop all meta data
		foreach($this->meta as $item)
		{
			// if the key and the value match we return the item
			if(isset($item[$attribute]) && $item[$attribute] == $attributeValue) return $item;
		}
	}

	/**
	 * Get the pagetitle
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
		// create unique filename
		$fileName = md5($file) . '.css';
		$finalURL = FRONTEND_CACHE_URL . '/minified_css/' . $fileName;
		$finalPath = FRONTEND_CACHE_PATH . '/minified_css/' . $fileName;

		// check that file does not yet exist or has been updated already
		if(!SpoonFile::exists($finalPath) || filemtime(PATH_WWW . $file) > filemtime($finalPath))
		{
			// minify the file
			require_once PATH_LIBRARY . '/external/minify.php';
			$css = new MinifyCSS(PATH_WWW . $file);
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
		// create unique filename
		$fileName = md5($file) . '.js';
		$finalURL = FRONTEND_CACHE_URL . '/minified_js/' . $fileName;
		$finalPath = FRONTEND_CACHE_PATH . '/minified_js/' . $fileName;

		// check that file does not yet exist or has been updated already
		if(!SpoonFile::exists($finalPath) || filemtime(PATH_WWW . $file) > filemtime($finalPath))
		{
			// minify the file
			require_once PATH_LIBRARY . '/external/minify.php';
			$js = new MinifyJS(PATH_WWW . $file);
			$js->minify($finalPath);
		}

		return $finalURL;
	}

	/**
	 * Parse the header into the template
	 */
	public function parse()
	{
		// parse Facebook
		$this->parseFacebook();

		// parse SEO
		$this->parseSeo();

		// in debugmode we don't want our pages to be indexed.
		if(SPOON_DEBUG) $this->addMetaData(array('name' => 'robots', 'content' => 'noindex, nofollow'), true);

		// parse meta tags
		$this->parseMetaAndLinks();

		// parse CSS
		$this->parseCSS();

		// parse JS
		$this->parseJS();

		// parse custom header HTML and Google Analytics
		$this->parseCustomHeaderHTMLAndGoogleAnalytics();

		// assign page title
		$this->tpl->assign('pageTitle', (string) $this->getPageTitle());

		// assign site title
		$this->tpl->assign('siteTitle', (string) FrontendModel::getModuleSetting('core', 'site_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE));
	}

	/**
	 * Parse the CSS-files
	 */
	private function parseCSS()
	{
		// init var
		$cssFiles = array();
		$existingCSSFiles = $this->getCSSFiles();

		// if there aren't any JS-files added we don't need to do something
		if(!empty($existingCSSFiles))
		{
			foreach($existingCSSFiles as $file)
			{
				// add lastmodified time
				if($file['add_timestamp'] !== false) $file['file'] .= (strpos($file['file'], '?') !== false) ? '&m=' . LAST_MODIFIED_TIME : '?m=' . LAST_MODIFIED_TIME;

				// add
				$cssFiles[] = $file;
			}
		}

		// css-files
		$this->tpl->assign('cssFiles', $cssFiles);
	}

	/**
	 * Parse Google Analytics
	 */
	private function parseCustomHeaderHTMLAndGoogleAnalytics()
	{
		// get the data
		$siteHTMLHeader = (string) FrontendModel::getModuleSetting('core', 'site_html_header', null);
		$siteHTMLFooter = (string) FrontendModel::getModuleSetting('core', 'site_html_footer', null);
		$webPropertyId = FrontendModel::getModuleSetting('analytics', 'web_property_id', null);

		// search for the webpropertyId in the header and footer, if not found we should build the GA-code
		if($webPropertyId != '' && strpos($siteHTMLHeader, $webPropertyId) === false && strpos($siteHTMLFooter, $webPropertyId) === false)
		{
			// build GA-tracking code
			$trackingCode = '<script type="text/javascript">
								var _gaq = [[\'_setAccount\', \'' . $webPropertyId . '\'],
											[\'_setDomainName\', \'none\'],
											[\'_trackPageview\'],
											[\'_trackPageLoadTime\']];

								(function(d, t) {
									var g = d.createElement(t), s = d.getElementsByTagName(t)[0];
									g.async = true;
									g.src = \'//www.google-analytics.com/ga.js\';
									s.parentNode.insertBefore(g, s);
								}(document, \'script\'));
							</script>';

			// add to the header
			$siteHTMLHeader .= "\n" . $trackingCode;
		}

		// assign site wide html
		$this->tpl->assign('siteHTMLHeader', trim($siteHTMLHeader));
	}

	/**
	 * Parse Facebook related header-data
	 */
	private function parseFacebook()
	{
		$parseFacebook = false;

		// check if facebook admins are set
		if(FrontendModel::getModuleSetting('core', 'facebook_admin_ids', null) !== null)
		{
			$this->addMetaData(array('property' => 'fb:admins', 'content' => FrontendModel::getModuleSetting('core', 'facebook_admin_ids', null)), true, array('property'));
			$parseFacebook = true;
		}

		// check if no facebook admin is set but an app is configured we use the application as an admin
		if(FrontendModel::getModuleSetting('core', 'facebook_admin_ids', null) == '' && FrontendModel::getModuleSetting('core', 'facebook_app_id', null) !== null)
		{
			$this->addMetaData(array('property' => 'fb:app_id', 'content' => FrontendModel::getModuleSetting('core', 'facebook_app_id', null)), true, array('property'));
			$parseFacebook = true;
		}

		// should we add extra open-graph data?
		if($parseFacebook)
		{
			// build correct locale
			switch(FRONTEND_LANGUAGE)
			{
				case 'en':
					$locale = 'en_US';
					break;

				case 'cn':
					$locale = 'zh-CN';
					break;

				default:
					$locale = strtolower(FRONTEND_LANGUAGE) . '_' . strtoupper(FRONTEND_LANGUAGE);
			}

			// add the locale property
			$this->addOpenGraphData('locale', $locale);

			// if a default image has been set for facebook, assign it
			$this->addOpenGraphImage('/frontend/themes/' . FrontendTheme::getTheme() . '/facebook.png');
			$this->addOpenGraphImage('/facebook.png');
		}
	}

	/**
	 * Parse the JS-files
	 */
	private function parseJS()
	{
		// init var
		$jsFiles = array();
		$existingJSFiles = $this->getJSFiles();

		// if there aren't any JS-files added we don't need to do something
		if(!empty($existingJSFiles))
		{
			// some files should be cached, even if we don't want cached (mostly libraries)
			$ignoreCache = array(
				'/frontend/core/js/jquery/jquery.js',
				'/frontend/core/js/jquery/jquery.ui.js'
			);

			// loop the JS-files
			foreach($existingJSFiles as $file)
			{
				// some files shouldn't be uncachable
				if(in_array($file['file'], $ignoreCache) || $file['add_timestamp'] === false) $file = array('file' => $file['file']);

				// make the file uncachable
				else
				{
					// if the file is processed by PHP we don't want any caching
					if(substr($file['file'], 0, 11) == '/frontend/js') $file = array('file' => $file['file'] . '&amp;m=' . time());

					// add lastmodified time
					else
					{
						$modifiedTime = (strpos($file['file'], '?') !== false) ? '&amp;m=' . LAST_MODIFIED_TIME : '?m=' . LAST_MODIFIED_TIME;
						$file = array('file' => $file['file'] . $modifiedTime);
					}
				}

				// add
				$jsFiles[] = $file;
			}
		}

		// js-files
		$this->tpl->assign('jsFiles', $jsFiles);
	}

	/**
	 * Parse the meta and link-tags
	 */
	private function parseMetaAndLinks()
	{
		// build meta
		$meta = '';

		// loop meta
		foreach($this->meta as $attributes)
		{
			// start html
			$meta .= '<meta ';

			// add attributes
			foreach($attributes as $key => $value) $meta .= $key . '="' . $value . '" ';

			// remove last space
			$meta = trim($meta);

			// close html
			$meta .= '>' . "\n";
		}

		// build link
		$link = '';

		// loop links
		foreach($this->links as $attributes)
		{
			// start html
			$link .= '<link ';

			// add attributes
			foreach($attributes as $key => $value) $link .= $key . '="' . $value . '" ';

			// remove last space
			$link = trim($link);

			// close html
			$link .= '>' . "\n";
		}

		// assign meta
		$this->tpl->assign('meta', $meta . "\n" . $link);
		$this->tpl->assign('metaCustom', $this->getMetaCustom());
	}

	/**
	 * Parse SEO specific data
	 */
	private function parseSeo()
	{
		// any canonical URL provided?
		if($this->canonical != '') $url = $this->canonical;

		else
		{
			// get the chunks of the current url
			$urlChunks = parse_url($this->URL->getQueryString());

			// a canonical url should contain the domain. So make sure you redirect your website to a single url with .htaccess
			$url = rtrim(SITE_URL, '/');
			if(isset($urlChunks['port'])) $url .= ':' . $urlChunks['port'];
			if(isset($urlChunks['path'])) $url .= '/' . $urlChunks['path'];

			// any items provided through GET?
			if(isset($urlChunks['query']))
			{
				// the items we should add into the canonical url
				$itemsToAdd = array('page');
				$addToUrl = array();

				// loop all items in GET and check if we should ignore them
				foreach($_GET as $key => $value)
				{
					if(in_array($key, $itemsToAdd)) $addToUrl[$key] = $value;
				}

				// add GET-params
				if(!empty($addToUrl)) $url .= '?' . http_build_query($addToUrl);
			}
		}

		// prevent against xss
		$url = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($url) : SpoonFilter::htmlentities($url);

		// canonical
		$this->addLink(array('rel' => 'canonical', 'href' => $url));

		// noodp, noydir
		if(FrontendModel::getModuleSetting('core', 'seo_noodp', false)) $this->addMetaData(array('name' => 'robots', 'content' => 'noodp'));
		if(FrontendModel::getModuleSetting('core', 'seo_noydir', false)) $this->addMetaData(array('name' => 'robots', 'content' => 'noydir'));
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
		if(substr($url, 0, 1) == '/') $url = SITE_URL . $url;

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
	 * Set the pagetitle
	 *
	 * @param string $value The pagetitle to be set or to be prepended.
	 * @param bool[optional] $overwrite Should the existing pagetitle be overwritten?
	 */
	public function setPageTitle($value, $overwrite = false)
	{
		$value = trim((string) $value);
		$overwrite = (bool) $overwrite;

		// overwrite? reset the current value
		if($overwrite) $this->pageTitle = $value;

		// add to current value
		else
		{
			// empty value given?
			if(empty($value)) $this->pageTitle = FrontendModel::getModuleSetting('core', 'site_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE);

			// value isn't empty
			else
			{
				// if the current pagetitle is empty we should add the sitetitle
				if($this->pageTitle == '') $this->pageTitle = $value . SITE_TITLE_SEPERATOR . FrontendModel::getModuleSetting('core', 'site_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE);

				// prepend the value to the current pagetitle
				else $this->pageTitle = $value . SITE_TITLE_SEPERATOR . $this->pageTitle;
			}
		}
	}
}
