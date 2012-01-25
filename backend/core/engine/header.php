<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will be used to alter the head-part of the HTML-document that will be created by he Backend
 * Therefore it will handle meta-stuff (title, including JS, including CSS, ...)
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class BackendHeader
{
	/**
	 * All added CSS-files
	 *
	 * @var array
	 */
	private $cssFiles = array();

	/**
	 * All added JS-files
	 *
	 * @var	array
	 */
	private $jsFiles = array();

	/**
	 * Template instance
	 *
	 * @var	BackendTemplate
	 */
	private $tpl;

	/**
	 * URL-instance
	 *
	 * @var	BackendURL
	 */
	private $URL;

	public function __construct()
	{
		// store in reference so we can access it from everywhere
		Spoon::set('header', $this);

		// grab from the reference
		$this->URL = Spoon::get('url');
		$this->tpl = Spoon::get('template');
	}

	/**
	 * Add a CSS-file.
	 *
	 * If you don't specify a module, the current one will be used to automatically create the path to the file.
	 * Automatic creation of the filename will result in
	 *   /backend/modules/MODULE/layout/css/FILE (for modules)
	 *   /backend/core/layout/css/FILE (for core)
	 *
	 * If you set overwritePath to true, the above-described automatic path creation will not happend, instead the
	 * file-parameter will be used as path; which we then expect to be a full path (It has to start with a slash '/')
	 *
	 * @param string $file The name of the file to load.
	 * @param string[optional] $module The module wherin the file is located.
	 * @param bool[optional] $overwritePath Should we overwrite the full path?
	 * @param bool[optional] $minify Should the CSS be minified?
	 * @param bool[optional] $addTimestamp May we add a timestamp for caching purposes?
	 */
	public function addCSS($file, $module = null, $overwritePath = false, $minify = true, $addTimestamp = false)
	{
		$file = (string) $file;
		$module = (string) ($module !== null) ? $module : $this->URL->getModule();
		$overwritePath = (bool) $overwritePath;
		$minify = (bool) $minify;
		$addTimestamp = (bool) $addTimestamp;

		// init var
		$realPath = '';

		// no actual path given: create
		if(!$overwritePath)
		{
			// we have to build the path, but core is a special one
			if($module !== 'core') $file = '/backend/modules/' . $module . '/layout/css/' . $file;

			// core is special because it isn't a real module
			else $file = '/backend/core/layout/css/' . $file;
		}

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
	 * Add a JS-file.
	 * If you don't specify a module, the current one will be used
	 * If you set parseThroughPHP to true, the JS will be parsed by PHP (labels and vars will be assignes)
	 * If you set overwritePath to true we expect a full path (It has to start with a /)
	 *
	 * @param string $file The file to load.
	 * @param string[optional] $module The module wherin the file is located.
	 * @param bool[optional] $parseThroughPHP Should the file be parsed by PHP?
	 * @param bool[optional] $overwritePath Should we overwrite the full path?
	 * @param bool[optional] $addTimestamp May we add a timestamp for caching purposes?
	 */
	public function addJS($file, $module = null, $minify = true, $parseThroughPHP = false, $overwritePath = false, $addTimestamp = false)
	{
		$file = (string) $file;
		$module = (string) ($module !== null) ? $module : $this->URL->getModule();
		$minify = (bool) $minify;
		$parseThroughPHP = (bool) $parseThroughPHP;
		$overwritePath = (bool) $overwritePath;
		$addTimestamp = (bool) $addTimestamp;

		// validate parameters
		if($parseThroughPHP && $overwritePath) throw new BackendException('parseThroughPHP and overwritePath can\'t be both true.');

		// no minifying when debugging
//		if(SPOON_DEBUG) $minify = false;

		// no minifying when parsing through PHP
		if($parseThroughPHP) $minify = false;

		// is the given path the real path?
		if(!$overwritePath)
		{
			// should we parse the js-file? as in assign variables
			if($parseThroughPHP) $file = '/backend/js.php?module=' . $module . '&amp;file=' . $file . '&amp;language=' . BL::getWorkingLanguage();

			// we have to build the path, but core is a special one
			elseif($module !== 'core') $file = '/backend/modules/' . $module . '/js/' . $file;

			// core is special because it isn't a real module
			else $file = '/backend/core/js/' . $file;
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
	 * Get all added CSS files
	 *
	 * @return array
	 */
	public function getCSSFiles()
	{
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
	 * Minify a CSS-file
	 *
	 * @param string $file The file to be minified.
	 * @return string
	 */
	private function minifyCSS($file)
	{
		// create unique filename
		$fileName = md5($file) . '.css';
		$finalURL = BACKEND_CACHE_URL . '/minified_css/' . $fileName;
		$finalPath = BACKEND_CACHE_PATH . '/minified_css/' . $fileName;

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
	 * Minify a JS-file
	 *
	 * @param string $file The file to be minified.
	 * @return string
	 */
	private function minifyJS($file)
	{
		// create unique filename
		$fileName = md5($file) . '.js';
		$finalURL = BACKEND_CACHE_URL . '/minified_js/' . $fileName;
		$finalPath = BACKEND_CACHE_PATH . '/minified_js/' . $fileName;

		// check that file does not yet exist or has been updated already
//		if(!SpoonFile::exists($finalPath) || filemtime(PATH_WWW . $file) > filemtime($finalPath))
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
		// parse CSS
		$this->parseCSS();

		// parse JS
		$this->parseJS();
	}

	/**
	 * Parse the CSS-files
	 */
	public function parseCSS()
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
	 * Parse the JS-files
	 */
	public function parseJS()
	{
		$jsFiles = array();
		$existingJSFiles = $this->getJSFiles();

		// if there aren't any JS-files added we don't need to do something
		if(!empty($existingJSFiles))
		{
			// some files should be cached, even if we don't want cached (mostly libraries)
			$ignoreCache = array(
				'/backend/core/js/jquery/jquery.js',
				'/backend/core/js/jquery/jquery.ui.js',
				'/backend/core/js/ckeditor/jquery.ui.dialog.patch.js',
				'/backend/core/js/jquery/jquery.tools.js',
				'/backend/core/js/jquery/jquery.backend.js',
				'/backend/core/js/ckeditor/ckeditor.js',
				'/backend/core/js/ckeditor/adapters/jquery.js',
				'/backend/core/js/ckfinder/ckfinder.js'
			);

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

		// assign JS-files
		$this->tpl->assign('jsFiles', $jsFiles);
	}
}
