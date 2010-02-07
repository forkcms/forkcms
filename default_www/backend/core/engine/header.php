<?php

/**
 * BackendHeader
 *
 * This class will be used to alter the head-part of the HTML-document that will be created by he Backend
 * Therefore it will handle meta-stuff (title, including JS, including CSS, ...)
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
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
	private $javascriptFiles = array();


	/**
	 * Template instance
	 *
	 * @var	BackendTemplate
	 */
	private $tpl;


	/**
	 * Url-instance
	 *
	 * @var	BackendURL
	 */
	private $URL;


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// store in reference so we can access it from everywhere
		Spoon::setObjectReference('header', $this);

		// grab from the reference
		$this->URL = Spoon::getObjectReference('url');
		$this->tpl = Spoon::getObjectReference('template');
	}


	/**
	 * Add a CSS-file.
	 * If you don't specify a module, the current one will be used
	 * If you set overwritePath to true we expect a full path (It has to start with a slash '/')
	 *
	 * @return	void
	 * @param	string $fileName
	 * @param	string[optional] $module
	 * @param	bool[optional] $overwritePath
	 */
	public function addCSS($fileName, $module = null, $overwritePath = false)
	{
		// redefine
		$fileName = (string) $fileName;
		$module = (string) ($module !== null) ? $module : $this->URL->getModule();
		$overwritePath = (bool) $overwritePath;

		// init var
		$realPath = '';

		// is the given path the real path?
		if($overwritePath) $realPath = $fileName;

		// we have to build the path, but core is a special one
		elseif($module !== 'core') $realPath = '/backend/modules/'. $module .'/layout/css/'. $fileName;

		// core is special because it isn't a real module
		else $realPath = '/backend/core/layout/css/'. $fileName;

		// add if not already added
		if(!in_array($realPath, $this->cssFiles)) $this->cssFiles[] = array('path' => $realPath);
	}


	/**
	 * Add a JS-file.
	 * If you don't specify a module, the current one will be used
	 * If you set parseThroughPHP to true, the JS will be parsed by PHP (labels and vars will be assignes)
	 * If you set overwritePath to true we expect a full path (It has to start with a /)
	 *
	 * @return	void
	 * @param	string $fileName
	 * @param	string[optional] $module
	 * @param	bool[optional] $parseThroughPHP
	 * @param	bool[optional] $overwritePath
	 */
	public function addJavascript($fileName, $module = null, $parseThroughPHP = false, $overwritePath = false)
	{
		// redefine
		$fileName = (string) $fileName;
		$module = (string) ($module !== null) ? $module : $this->URL->getModule();
		$parseThroughPHP = (bool) $parseThroughPHP;
		$overwritePath = (bool) $overwritePath;

		// validate parameters
		if($parseThroughPHP && $overwritePath) throw new BackendException('parseThroughPHP and overwritePath can\'t be both true.');

		// init var
		$realPath = '';

		// is the given path the real path?
		if($overwritePath) $realPath = $fileName;

		// should we parse the js-file? as in assign variables
		elseif($parseThroughPHP) $realPath = '/backend/js.php?module='. $module .'&amp;file='. $fileName .'&amp;language='. BackendLanguage::getWorkingLanguage();

		// we have to build the path, but core is a special one
		elseif($module !== 'core') $realPath = '/backend/modules/'. $module .'/js/'. $fileName;

		// core is special because it isn't a real module
		else $realPath = '/backend/core/js/'. $fileName;

		// add if not already added
		if(!in_array($realPath, $this->javascriptFiles)) $this->javascriptFiles[] = $realPath;
	}


	/**
	 * Parse the JS, CSS files and meta-info into the head of the HTML-document
	 *
	 * @return	void
	 */
	public function parse()
	{
		// init vars
		$cssFiles = array();
		$javascriptFiles = array();

		// get last modified time for the header template
		$lastModifiedTime = @filemtime($this->tpl->getCompileDirectory() .'/'. md5(realpath(BACKEND_CORE_PATH .'/layout/templates/header.tpl')) . '_header.tpl.php');

		// reset lastmodified time if needed (SPOON_DEBUG is enabled or we don't get a decent timestamp)
		if($lastModifiedTime === false || SPOON_DEBUG) $lastModifiedTime = time();

		// if there aren't any CSS-files added we don't need to do something
		if(!empty($this->cssFiles))
		{
			// loop the CSS-files and add the modified-time
			foreach($this->cssFiles as $file) $cssFiles[] = array('path' => $file['path'] .'?m='. $lastModifiedTime);
		}

		// assign CSS-files
		$this->tpl->assign('cssFiles', $cssFiles);

		// if there aren't any JS-files added we don't need to do something
		if(!empty($this->javascriptFiles))
		{
			// some files should be cached, even if we don't want cached (mostly libraries)
			$ignoreCache = array('/backend/core/js/jquery/jquery.js',
									'/backend/core/js/jquery/jquery.ui.js',
									'/backend/core/js/jquery/jquery.tools.js',
									'/backend/core/js/jquery/jquery.autocomplete.js',
									'/backend/core/js/jquery/jquery.backend.js',
									'/backend/core/js/tiny_mce/tiny_mce.js');

			// loop the JS-files
			foreach($this->javascriptFiles as $file)
			{
				// some files shouldn't be uncachable
				if(in_array($file, $ignoreCache)) $javascriptFiles[] = array('path' => $file);

				// make the file uncacheble
				else
				{
					// if the file is processed by PHP we don't want any caching
					if(substr($file, 0, 11) == '/backend/js') $javascriptFiles[] = array('path' => $file .'&amp;m='. time());

					// add lastmodified time
					else $javascriptFiles[] = array('path' => $file .'?m='. $lastModifiedTime);
				}
			}
		}

		// assign JS-files
		$this->tpl->assign('javascriptFiles', $javascriptFiles);
	}
}

?>