<?php

/**
 * This class will be used to alter the head-part of the HTML-document that will be created by the frontend
 * Therefore it will handle meta-stuff (title, including JS, including CSS, ...)
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class FrontendHeader extends FrontendBaseObject
{
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
	private $javascriptFiles = array();


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


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// call the parent
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
		$this->addJS('/frontend/core/js/utils.js', true);
		$this->addJS('/frontend/core/js/frontend.js', false, true);
	}


	/**
	 * Add a CSS file into the array
	 *
	 * @return	void
	 * @param 	string $file					The path for the CSS-file that should be loaded.
	 * @param	bool[optional] $minify			Should the CSS be minified?
	 * @param	bool[optional] $addTimestamp	May we add a timestamp for caching purposes?
	 */
	public function addCSS($file, $minify = true, $addTimestamp = null)
	{
		// redefine
		$file = (string) $file;
		$minify = (bool) $minify;

		// get file path
		$file = FrontendTheme::getPath($file);

		// no minifying when debugging
		if(SPOON_DEBUG) $minify = false;

		// try to modify
		if($minify) $file = $this->minifyCSS($file);

		// in array
		$inArray = false;

		// check if the file already exists in the array
		foreach($this->cssFiles as $row) if($row['file'] == $file) $inArray = true;

		// add to array if it isn't there already
		if(!$inArray)
		{
			// build temporary arrat
			$temp['file'] = (string) $file;
			$temp['add_timestamp'] = $addTimestamp;

			// add to files
			$this->cssFiles[] = $temp;
		}
	}


	/**
	 * Add a javascript file into the array
	 *
	 * @return	void
	 * @param 	string $file						The path to the javascript-file that should be loaded.
	 * @param	bool[optional] $minify				Should the file be minified?
	 * @param	bool[optional] $parseThroughPHP		Should the file be parsed through PHP?
	 * @param	bool[optional] $addTimestamp		May we add a timestamp for caching purposes?
	 */
	public function addJS($file, $minify = true, $parseThroughPHP = false, $addTimestamp = null)
	{
		// redefine
		$file = (string) $file;
		$minify = (bool) $minify;

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
			if(!isset($chunks[2])) throw new FrontendException('Invalid file (' . $file . ').');

			// reset module for core
			if($chunks[0] == '') $chunks[0] = 'core';

			// alter the file
			$file = '/frontend/js.php?module=' . $chunks[0] . '&amp;file=' . $chunks[2] . '&amp;language=' . FRONTEND_LANGUAGE;
		}

		// try to minify
		if($minify) $file = $this->minifyJavascript($file);

		// already in array?
		if(!in_array(array('file' => $file, 'add_timestamp' => $addTimestamp), $this->javascriptFiles))
		{
			// add to files
			$this->javascriptFiles[] = array('file' => $file, 'add_timestamp' => $addTimestamp);
		}
	}


	/**
	 * Add link
	 *
	 * @return	void
	 * @param	array $attributes			The attributes to parse.
	 * @param	bool[optional] $overwrite	Should we overwrite the current value?
	 * @param	mixed[optional] $uniqueKeys	Which keys can we use to decide if an item is unique.
	 */
	public function addLink(array $attributes, $overwrite = false, $uniqueKeys = null)
	{
		// redefine
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
	 * @return	void
	 * @param	array $attributes			The attributes to parse.
	 * @param	bool[optional] $overwrite	Should we overwrite the current value?
	 * @param	mixed[optional] $uniqueKeys	Which keys can we use to decide if an item is unique.
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
	 * @return	void
	 * @param	string $value				The description.
	 * @param	bool[optional] $overwrite	Should we overwrite the previous value?
	 */
	public function addMetaDescription($value, $overwrite = false)
	{
		$this->addMetaData(array('name' => 'description', 'content' => $value), $overwrite);
	}


	/**
	 * Add meta-keywords, somewhat a shortcut for the addMetaData-method
	 *
	 * @return	void
	 * @param	string $value				The description.
	 * @param	bool[optional] $overwrite	Should we overwrite the previous value?
	 */
	public function addMetaKeywords($value, $overwrite = false)
	{
		$this->addMetaData(array('name' => 'keywords', 'content' => $value), $overwrite);
	}


	/**
	 * Add Open Graph data
	 *
	 * @return	void
	 * @param	string $key					The key (without og:).
	 * @param	string $value				The value.
	 * @param	bool[optional] $overwrite	Should we overwrite the previous value?
	 */
	public function addOpenGraphData($key, $value, $overwrite = false)
	{
		$this->addMetaData(array('property' => 'og:' . $key, 'content' => $value), $overwrite, 'property');
	}


	/**
	 * Sort function for CSS-files
	 *
	 * @return	void
	 */
	private function cssSort()
	{
		// init vars
		$i = 0;
		$aTemp = array();

		// loop files
		foreach($this->cssFiles as $file)
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
		$this->cssFiles = $return;
	}


	/**
	 * Get all added CSS files
	 *
	 * @return	array
	 */
	public function getCSSFiles()
	{
		// sort the cssfiles
		$this->cssSort();

		// fetch files
		return $this->cssFiles;
	}


	/**
	 * get all added javascript files
	 *
	 * @return	array
	 */
	public function getJavascriptFiles()
	{
		return $this->javascriptFiles;
	}


	/**
	 * Get all links
	 *
	 * @return	array
	 */
	public function getLinks()
	{
		return $this->links;
	}


	/**
	 * Get meta
	 *
	 * @return	array
	 */
	public function getMeta()
	{
		return $this->meta;
	}


	/**
	 * Get the custom meta
	 *
	 * @return	string
	 */
	public function getMetaCustom()
	{
		return $this->metaCustom;
	}


	/**
	 * Get all attributes for meta tag specified by the attribute and the value for that attribute.
	 *
	 * @return	array
	 * @param	string $attribute			The attribute to match on.
	 * @param	string $attributeValue		The value for the unique attribute.
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
	 * @return	string
	 */
	public function getPageTitle()
	{
		return $this->pageTitle;
	}


	/**
	 * Minify a CSS-file
	 *
	 * @return	string
	 * @param	string $file	The file to be minified.
	 */
	private function minifyCSS($file)
	{
		// create unique filename
		$fileName = md5($file) . '.css';
		$finalURL = FRONTEND_CACHE_URL . '/minified_css/' . $fileName;
		$finalPath = FRONTEND_CACHE_PATH . '/minified_css/' . $fileName;

		// file already exists (if SPOON_DEBUG is true, we should reminify every time)
		if(SpoonFile::exists($finalPath) && !SPOON_DEBUG) return $finalURL;

		// grab content
		$content = SpoonFile::getContent(PATH_WWW . $file);

		// fix urls
		$matches = array();
		$pattern = '/url\(';
		$pattern .= 	'("|\'){0,1}';
		$pattern .= 		'([\/\.a-z].*)';
		$pattern .= 	'("|\'){0,1}';
		$pattern .= 	'\)/iUs';

		$content = preg_replace($pattern, 'url($3' . dirname($file) . '/$2$3)', $content);

		// remove comments
		$content = preg_replace('/\/\*(.*)\*\//iUs', '', $content);
		$content = preg_replace('/([\t\w]{1,})\/\/.*/i', '', $content);

		// remove tabs
		$content = preg_replace('/\t/i', '', $content);

		// remove spaces on end of line
		$content = preg_replace('/ \n/i', "\n", $content);

		// match stuff between brackets
		$matches = array();
		preg_match_all('/ \{(.*)}/iUms', $content, $matches);

		// are there any matches
		if(isset($matches[0]))
		{
			// loop matches
			foreach($matches[0] as $key => $match)
			{
				// remove faulty newlines
				$tempContent = preg_replace('/\r/iU', '', $matches[1][$key]);

				// removes real newlines
				$tempContent = preg_replace('/\n/iU', ' ', $tempContent);

				// replace the new block in the general content
				$content = str_replace($matches[0][$key], '{' . $tempContent . '}', $content);
			}
		}

		// remove faulty newlines
		$content = preg_replace('/\r/iU', '', $content);

		// remove empty lines
		$content = preg_replace('/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/', "\n", $content);

		// remove newlines at start and end
		$content = trim($content);

		// save content
		SpoonFile::setContent($finalPath, $content);

		// return
		return $finalURL;
	}


	/**
	 * Minify a javascript-file
	 *
	 * @return	string
	 * @param	string $file	The file to be minified.
	 */
	private function minifyJavascript($file)
	{
		// create unique filename
		$fileName = md5($file) . '.js';
		$finalURL = FRONTEND_CACHE_URL . '/minified_js/' . $fileName;
		$finalPath = FRONTEND_CACHE_PATH . '/minified_js/' . $fileName;

		// file already exists (if SPOON_DEBUG is true, we should reminify every time
		if(SpoonFile::exists($finalPath) && !SPOON_DEBUG) return $finalURL;

		// grab content
		$content = SpoonFile::getContent(PATH_WWW . $file);

		// remove comments
		$content = preg_replace('/\/\*(.*)\*\//iUs', '', $content);
		$content = preg_replace('/([\t\w]{1,})\/\/.*/i', '', $content);

		// remove tabs
		$content = preg_replace('/\t/i', ' ', $content);

		// remove faulty newlines
		$content = preg_replace('/\r/iU', '', $content);

		// remove empty lines
		$content = preg_replace('/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/', "\n", $content);

		// store
		SpoonFile::setContent($finalPath, $content);

		// return
		return $finalURL;
	}


	/**
	 * Parse the header into the template
	 *
	 * @return	void
	 */
	public function parse()
	{
		// assign page title
		$this->tpl->assign('pageTitle', (string) $this->getPageTitle());

		// facebook admins given?
		if(FrontendModel::getModuleSetting('core', 'facebook_admin_ids', null) !== null) $this->addMetaData(array('property' => 'fb:admins', 'content' => FrontendModel::getModuleSetting('core', 'facebook_admin_ids', null)), true, array('property'));

		// in debugmode we don't want our pages to be indexed.
		if(SPOON_DEBUG) $this->addMetaData(array('name' => 'robots', 'content' => 'noindex, nofollow'), true);

		// noodp, noydir
		if(FrontendModel::getModuleSetting('core', 'seo_noodp', false)) $this->addMetaData(array('name' => 'robots', 'content' => 'noodp'));
		if(FrontendModel::getModuleSetting('core', 'seo_noydir', false)) $this->addMetaData(array('name' => 'robots', 'content' => 'noydir'));

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

		// init var
		$cssFiles = null;
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

		// init var
		$javascriptFiles = null;
		$existingJavascriptFiles = $this->getJavascriptFiles();

		// if there aren't any JS-files added we don't need to do something
		if(!empty($existingJavascriptFiles))
		{
			// some files should be cached, even if we don't want cached (mostly libraries)
			$ignoreCache = array('/frontend/core/js/jquery/jquery.js',
									'/frontend/core/js/jquery/jquery.ui.js');

			// loop the JS-files
			foreach($existingJavascriptFiles as $file)
			{
				// some files shouldn't be uncachable
				if(in_array($file['file'], $ignoreCache) || $file['add_timestamp'] === false) $javascriptFiles[] = array('file' => $file['file']);

				// make the file uncachable
				else
				{
					// if the file is processed by PHP we don't want any caching
					if(substr($file['file'], 0, 11) == '/frontend/js') $javascriptFiles[] = array('file' => $file['file'] . '&amp;m=' . time());

					// add lastmodified time
					else
					{
						$modifiedTime = (strpos($file['file'], '?') !== false) ? '&amp;m=' . LAST_MODIFIED_TIME : '?m=' . LAST_MODIFIED_TIME;
						$javascriptFiles[] = array('file' => $file['file'] . $modifiedTime);
					}
				}
			}
		}

		// js-files
		$this->tpl->assign('javascriptFiles', $javascriptFiles);

		// assign site title
		$this->tpl->assign('siteTitle', (string) FrontendModel::getModuleSetting('core', 'site_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE));

		// get the data
		$siteHTMLHeader = (string) FrontendModel::getModuleSetting('core', 'site_html_header', null);
		$siteHTMLFooter = (string) FrontendModel::getModuleSetting('core', 'site_html_footer', null);
		$webPropertyId = FrontendModel::getModuleSetting('analytics', 'web_property_id', null);

		// search for the webpropertyId, if not found we should build the GA-code
		if($webPropertyId != '' && strpos($siteHTMLHeader, $webPropertyId) === false && strpos($siteHTMLFooter, $webPropertyId) === false)
		{
			// build GA-tracking code
			$trackingCode = '<script type="text/javascript">
								var _gaq = _gaq || [];
								_gaq.push([\'_setAccount\', \'' . $webPropertyId . '\']);
								_gaq.push([\'_setDomainName\', \'none\']);
								_gaq.push([\'_trackPageview\']);
								_gaq.push([\'_trackPageLoadTime\']);

								(function() {
									var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
									ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
									var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
								})();
							</script>';

			// add to the header
			$siteHTMLHeader .= "\n" . $trackingCode;
		}

		// assign site wide html
		$this->tpl->assign('siteHTMLHeader', trim($siteHTMLHeader));
	}


	/**
	 * Set the custom meta
	 *
	 * @return	void
	 * @param	string $meta	The meta data to set.
	 */
	public function setMetaCustom($meta)
	{
		$this->metaCustom = (string) $meta;
	}


	/**
	 * Set the pagetitle
	 *
	 * @return	void
	 * @param	string $value				The pagetitle to be set or to be prepended.
	 * @param	bool[optional] $overwrite	Should the existing pagetitle be overwritten?
	 */
	public function setPageTitle($value, $overwrite = false)
	{
		// redefine vars
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

?>