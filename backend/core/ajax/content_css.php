<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will generate a valid url based upon the submitted url.
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class BackendCoreAjaxContentCss extends BackendBaseAJAXAction
{
	/**
	 * Cleanup a CSS-style
	 *
	 * @param string $style The style to cleanup.
	 * @return string
	 */
	private function cleanupStyle($style)
	{
		// remove comments
 		$style = preg_replace('/\/\*(.*?)\*\//ims', '', $style);

		// tone down whitespace characters
 		$style = preg_replace('/\s+/', ' ', $style);
 		$style = trim($style);

 		// ignore form element's styles
 		$style = preg_replace('/([,\}]) ?(input|button|textarea|select)\.[^,\{]*/', '$1', $style);

 		// apart from .content, we don't want any inline styles
 		$style = preg_replace('/([,\}]) ?\.(?!content)[^,\{]*/', '$1', $style);

		return $style;
	}

	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// init vars
		$styles = '';
		$theme = BackendModel::getModuleSetting('core', 'theme');
		$coreCssPath = FRONTEND_PATH . '/core/layout/css/';
		$cssPath = ($theme ? FRONTEND_PATH . '/themes/' . $theme . '/core/layout/css/' : $coreCssPath);

		// grab all of the css files
		$files = (array) SpoonFile::getList($cssPath);

		// editor_content is a special one, we'll get to that later
		$pos = array_search('editor_content.css', $files);
		if($pos !== false) array_splice($files, $pos, 1);

		// prepend files with full path
		foreach($files as $i => $file) $files[$i] = $cssPath . $file;

		// editor content overrides some previous site defaults for better presentation in the editor
		if(SpoonFile::exists($cssPath . 'editor_content.css')) array_push($files, $cssPath . 'editor_content.css');
		else array_push($files, $coreCssPath . 'editor_content.css');

		// loop all css files
		foreach($files as $file)
		{
			// process file
			$styles .= $this->processFile($file);
		}

		// set headers
		SpoonHTTP::setHeaders('Content-type: text/css');

		// output
		echo $styles;
		exit;
	}

	/**
	 * Grab all the styles from a CSS-file and return them as an array
	 *
	 * @param string $file The file that will be processed
	 * @param bool[optional] $isContent If true, only styles that are scoped inside the .content class will be returned.
	 * @return array
	 */
	private function processFile($file)
	{
		// if the files doesn't exists we can stop here and just return an empty array
		if(!SpoonFile::exists($file)) return array();

		// fetch content from file
		$content = SpoonFile::getContent($file);

		// cleanup content
		$content = $this->cleanupStyle($content);

		return $content;
	}
}
