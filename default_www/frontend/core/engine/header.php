<?php

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 * @subpackage	header
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
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
	private $jsFiles = array();


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
	 * Adds a css file into the array
	 *
	 * @return	void
	 * @param 	string $file
	 * @param	string[optional] $media
	 */
	public function addCssFile($file, $media = 'screen',  $condition = null, $minify = true) // @todo ik zou doen $minify = null, en bij defininen spoon_debug laten meespelen als default, zo kunde manueel nog overrulen als ge wilt.
	{
		// redefine
		$file = (string) $file;
		$media = (string) $media;
		$condition = ($condition !== null) ? (string) $condition : null;
		$minify = (bool) $minify;

		// no minifying when debugging
		if(SPOON_DEBUG) $minify = false;

		// try to modify
		if($minify) $file = $this->minifyCss($file);

		// in array
		$inArray = false;

		// check if the file already exists in the array
		foreach ($this->cssFiles as $row) if($row['file'] == $file && $row['media'] == $media) $inArray = true;

		// add to array
		if(!$inArray)
		{
			// build temporary arrat
			$aTemp['file'] = (string) $file;
			$aTemp['media'] = (string) $media;
			$aTemp['condition'] = (string) $condition;

			// options
			$aTemp['oHasCondition'] = (bool) ($condition !== null);
			$aTemp['oHasNoCondition'] = (bool) ($condition === null);

			// add to files
			$this->cssFiles[] = $aTemp;
		}
	}


	/**
	 * Adds a js file into the array
	 *
	 * @return	void
	 * @param 	string $file
	 * @param	bool[optional] $minify
	 */
	public function addJsFile($file, $minify = true)// @todo idem met minify hier.
	{
		// redefine
		$file = (string) $file;
		$minify = (bool) $minify;

		// no minifying when debugging
		if(SPOON_DEBUG) $minify = false;

		// try to modify
		if($minify) $file = $this->minifyJs($file);

		// init var
		$inArray = false;

		// check if the file already exists in the array
		foreach ($this->jsFiles as $row) if($row['file'] == $file) $inArray = true;

		// add to array
		if(!$inArray)
		{
			// build temporary array
			$aTemp['file'] = $file;

			// add to files
			$this->jsFiles[] = $aTemp;
		}
	}


	/**
	 * Sort function for CSS-files
	 *
	 * @return	void
	 */
	public function cssSort()
	{
		// init vars
		$i = 0;
		$aTemp = array();

		// loop files
		foreach($this->cssFiles as $file)
		{
			// if condition is not empty, add to lowest key
			if($file['condition'] != '') $aTemp['z'.$i][] = $file;
			else
			{
				// if media == screen, add to highest key
				if($file['media'] == 'screen') $aTemp['a'.$i][] = $file;

				// fallback
				else $aTemp['b'. $file['media'] .$i][] = $file;

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
	 * Get all added CSS-files
	 *
	 * @return	array
	 */
	public function getCssFiles()
	{
		// sort the cssfiles
		$this->cssSort();

		// fetch files
		return $this->cssFiles;
	}


	/**
	 * get all added JS-files
	 *
	 * @return	array
	 */
	public function getJsFiles()
	{
		return $this->aJsFiles;
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
	 * @param	string $file
	 */
	private function minifyCss($file)
	{
		// create unique filename
		$fileName = md5($file) .'.css';
		$finalUrl = FRONTEND_CACHE_URL .'/minified_css/'. $fileName;
		$finalPath = FRONTEND_CACHE_PATH .'/minified_css/'. $fileName;

		// file already exists (if SPOON_DEBUG is true, we should reminify every time
		if(SpoonFile::exists($finalPath) && !SPOON_DEBUG) return $finalUrl;

		// grab content
		$content = SpoonFile::getFileContent(PATH_WWW . $file);

		// remove comments
		$content = preg_replace('|/\*(.*)\*/|iUs', '', $content);
		$content = preg_replace('|\/\/.*|i', '', $content);

		// remove tabs
		$content = preg_replace('|\t|i', '', $content);

		// remove spaces on end off line
		$content = preg_replace('| \n|i', "\n", $content);

		// match stuff between brackets
		$aMatches = array();
		preg_match_all('| \{(.*)}|iUms', $content, $aMatches);

		// are there any matches
		if(isset($aMatches[0]))
		{
			// loop matches
			foreach ($aMatches[0] as $key => $match)
			{
				// remove faulty newlines
				$tempContent = preg_replace('|\r|iU', '', $aMatches[1][$key]);

				// removes real newlines
				$tempContent = preg_replace('|\n|iU', ' ', $tempContent);

				// @todo comment ?
				$content = str_replace($aMatches[0][$key], '{'. $tempContent .'}', $content);
			}
		}

		// remove faulty newlines
		$content = preg_replace('|\r|iU', '', $content);

		// remove empty lines
		$content = preg_replace('/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/', "\n", $content);

		// remove newlines at start and end
		$content = trim($content);

		// save content
		SpoonFile::setContent($finalPath, $content);

		// return
		return $finalUrl;
	}


	/**
	 * Minify a JS-file
	 *
	 * @return	string
	 * @param	string $file
	 */
	private function minifyJs($file)
	{
		// create unique filename
		$fileName = md5($file) .'.js';
		$finalUrl = FRONTEND_CACHE_URL .'/minified_js/'. $fileName;
		$finalPath = FRONTEND_CACHE_PATH .'/minified_js/'. $fileName;

		// file already exists (if SPOON_DEBUG is true, we should reminify every time
		if(SpoonFile::exists($finalPath) && !SPOON_DEBUG) return $finalUrl;

		// grab content
		$content = trim(SpoonFile::getFileContent(PATH_WWW . $file));

		// remove comments
		$content = preg_replace('|/\*(.*)\*/|iUs', '', $content);
		$content = preg_replace('|\/\/.*|i', '', $content);

		// remove tabs
		$content = preg_replace('|\t|i', ' ', $content);

		// remove faulty newlines
		$content = preg_replace('|\r|iU', '', $content);

		// remove empty lines
		$content = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $content);

		// store
		SpoonFile::setFileContent($finalPath, $content);

		// return
		return $finalUrl;
	}


	/**
	 * Parse the header into the template
	 *
	 * @return	void
	 */
	public function parse()
	{
		// assign page title
		$this->tpl->assign('pageTitle', $this->getPageTitle());

		// assign meta
		$this->tpl->assign('metaDescription', (string) $this->getMetaDescription());
		$this->tpl->assign('metaKeywords', (string) $this->getMetaKeywords());
		$this->tpl->assign('metaCustom', (string) $this->getMetaCustom());

		// css-files
		$this->tpl->assign('iCssFiles', (array) $this->getCssFiles());

		// js-files
		$this->tpl->assign('iJsFiles', (array) $this->getJsFiles());
	}


	/**
	 * Set meta-custom
	 *
	 * @return	void
	 * @param	string $value
	 */
	public function setMetaCustom($value)
	{
		$this->metaCustom = (string) $value;
	}


	/**
	 * Set meta-description
	 *
	 * @return	void
	 * @param	string $value
	 * @param	bool[optional] $overwrite
	 */
	public function setMetaDescription($value, $overwrite = false)
	{
		// redefine vars
		$value = (string) $value;
		$overwrite = (bool) $overwrite;

		// set var
		if($overwrite) $this->metaDescription = $value;
		else
		{
			if($this->metaDescription == '') $this->metaDescription = $value;
			else $this->metaDescription .= ', '. $value;
		}
	}


	/**
	 * Set meta-keywords
	 *
	 * @return	void
	 * @param	string $value
	 * @param	bool[optional] $overwrite
	 */
	public function setMetaKeywords($value, $overwrite = false)
	{
		// redefine vars
		$value = (string) $value;
		$overwrite = (bool) $overwrite;

		// set var
		if ($overwrite) $this->metaKeywords = $value;
		else
		{
			if($this->metaKeywords == '') $this->metaKeywords = $value;
			else $this->metaKeywords .= ', '. $value;
		}
	}


	/**
	 * Set the pagetitle
	 *
	 * @return	void
	 * @param	string $value
	 * @param	bool[optional] $overwrite
	 */
	public function setPageTitle($value, $overwrite = false)
	{
		// redefine vars
		$value = (string) $value;
		$overwrite = (bool) $overwrite;

		// set var
		if($overwrite) $this->pageTitle = $value;
		else
		{
			if(empty($value)) $this->pageTitle = CoreModel::getModuleSetting('core', 'site_title_'. FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE);
			else
			{
				if($this->pageTitle == '') $this->pageTitle = $value . SITE_TITLE_SEPERATOR . CoreModel::getModuleSetting('core', 'site_title_'. FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE);
				else $this->pageTitle = $value . SITE_TITLE_SEPERATOR . $this->pageTitle;
			}
		}

	}
}
?>