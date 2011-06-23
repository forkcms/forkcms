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
	 * Custom meta
	 *
	 * @var	string
	 */
	private $metaCustom;


	/**
	 * Metadescription
	 *
	 * @var	string
	 */
	private $metaDescription;


	/**
	 * Metakeywords
	 *
	 * @var	string
	 */
	private $metaKeywords;


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
		$this->addJS('/frontend/core/js/frontend.js', false, true);
		$this->addJS('/frontend/core/js/utils.js', true);
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
		$file = FrontendTheme::getPath($file);

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
	 * Add data into metacustom
	 *
	 * @return	void
	 * @param	string $value	The string that should be appended to the meta-custom.
	 */
	public function addMetaCustom($value)
	{
		$this->metaCustom .= (string) $value;
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
	 * Get meta-custom
	 *
	 * @return	string
	 */
	public function getMetaCustom()
	{
		return $this->metaCustom;
	}


	/**
	 * Get the meta-description
	 *
	 * @return	string
	 */
	public function getMetaDescription()
	{
		return $this->metaDescription;
	}


	/**
	 * Get the meta-keywords
	 *
	 * @return	string
	 */
	public function getMetaKeywords()
	{
		return $this->metaKeywords;
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

		// assign meta
		$this->tpl->assign('metaDescription', (string) $this->getMetaDescription());
		$this->tpl->assign('metaKeywords', (string) $this->getMetaKeywords());

		// initial value for footer HTML
		$metaCustom = (string) $this->getMetaCustom();

		// facebook admins given?
		if(FrontendModel::getModuleSetting('core', 'facebook_admin_ids', null) !== null)
		{
			// add Facebook tag
			$metaCustom .= "\n" . '<meta property="fb:admins" content="' . FrontendModel::getModuleSetting('core', 'facebook_admin_ids', null) . '" />' . "\n";
		}
		$this->tpl->assign('metaCustom', $metaCustom);

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

		// assign site wide html
		$this->tpl->assign('siteHTMLHeader', (string) FrontendModel::getModuleSetting('core', 'site_html_header', null));
	}


	/**
	 * Set meta-custom
	 *
	 * @return	void
	 * @param	string $value	Overwrite the meta-custom with this value.
	 */
	public function setMetaCustom($value)
	{
		$this->metaCustom = (string) $value;
	}


	/**
	 * Set meta-description
	 *
	 * @return	void
	 * @param	string $value				The description to be set or to be appended.
	 * @param	bool[optional] $overwrite	Should the existing description be overwritten?
	 */
	public function setMetaDescription($value, $overwrite = false)
	{
		// redefine vars
		$value = trim((string) $value);
		$overwrite = (bool) $overwrite;

		// overwrite? reset the current value
		if($overwrite) $this->metaDescription = $value;

		// add to current value
		else
		{
			// current value is empty?
			if($this->metaDescription == '') $this->metaDescription = $value;

			// append to current value
			else $this->metaDescription .= ', ' . $value;
		}
	}


	/**
	 * Set meta-keywords
	 *
	 * @return	void
	 * @param	string $value				The keywords to be set or to be appended.
	 * @param	bool[optional] $overwrite	Should the existing keyword be overwritten?
	 */
	public function setMetaKeywords($value, $overwrite = false)
	{
		// redefine vars
		$value = trim((string) $value);
		$overwrite = (bool) $overwrite;

		// overwrite? reset the current value
		if($overwrite) $this->metaKeywords = $value;

		// add to current value
		else
		{
			// current value is empty
			if($this->metaKeywords == '') $this->metaKeywords = $value;

			// append to current value
			else $this->metaKeywords .= ', ' . $value;
		}
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