<?php

/**
 * BackendHeader
 *
 * This class will be used to alter the head-part of the HTML-document that will be created by Fork
 * Therefore it will handle meta-stuff (title, including JS, including CSS, ...)
 *
 * @package		Backend
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
	private $CSSFiles = array();


	/**
	 * All added JS-files
	 *
	 * @var	array
	 */
	private $JSFiles = array();


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
	private $url;


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
		$this->url = Spoon::getObjectReference('url');
		$this->tpl = Spoon::getObjectReference('template');
	}


	/**
	 * Add a CSS-file.
	 * If you don't specify a module, the current one will be used
	 * If you set overwritePath to true we expect a full path (It has to start with a /)
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
		$module = (string) ($module !== null) ? $module : $this->url->getModule();
		$overwritePath = (bool) $overwritePath;

		// init var
		$realPath = '';
		if($overwritePath) $realPath = $fileName;
		elseif($module !== 'core') $realPath = '/backend/modules/'. $module .'/layout/css/'. $fileName;
		else $realPath = '/backend/core/layout/css/'. $fileName;

		// add if not already added
		if(!in_array($realPath, $this->CSSFiles)) $this->CSSFiles[] = array('path' => $realPath);
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
	public function addJS($fileName, $module = null, $parseThroughPHP = false, $overwritePath = false)
	{
		// redefine
		$fileName = (string) $fileName;
		$module = (string) ($module !== null) ? $module : $this->url->getModule();
		$parseThroughPHP = (bool) $parseThroughPHP;
		$overwritePath = (bool) $overwritePath;

		// validate parameters
		if($parseThroughPHP && $overwritePath) throw new BackendException('parseThroughPHP and overwritePath can\'t be both true.');

		// init var
		$realPath = '';
		if($overwritePath) $realPath = $fileName;
		elseif($parseThroughPHP) $realPath = '/js.php?module='. $module .'&amp;file='. $fileName .'&amp;secretkey='. BackendAction::getUser()->getSecretKey();
		elseif($module !== 'core') $realPath = '/backend/modules/'. $module .'/js/'. $fileName;
		else $realPath = '/backend/core/js/'. $fileName;

		// add if not already added
		if(!in_array($realPath, $this->JSFiles)) $this->JSFiles[] = array('path' => $realPath);
	}


	/**
	 * Parse the JS, CSS files and meta-info into the head of the HTML-document
	 *
	 * @return	void
	 */
	public function parse()
	{
		// assign CSS-files
		$this->tpl->assign('cssFiles', $this->CSSFiles);

		// assign JS-files
		$this->tpl->assign('javascriptFiles', $this->JSFiles);
	}

}
?>