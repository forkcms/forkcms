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
 		$style = preg_replace('|/\*(.*)\*/|iUms', '', $style);

		// remove whitespace characters
 		$style = preg_replace('/\s+/', ' ', $style);
 		$style = trim($style);

		return $style;
	}

	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		$styles = array();
		$theme = BackendModel::getModuleSetting('core', 'theme');

		// add the styles from the default frontend stylesheet
		$styles = array_merge($styles, $this->processFile(FRONTEND_PATH . '/core/layout/css/screen.css', true));

		// add the styles that should be overruled in the editor
		$styles = array_merge($styles, $this->processFile(BACKEND_PATH . '/core/layout/css/editor_content.css'));

		// theme
		if($theme !== null)
		{
			// add the styles specific for the current theme
			$styles = array_merge($styles, $this->processFile(FRONTEND_PATH .'/themes/' . $theme . '/core/layout/css/screen.css', true));

			// add the styles that should be overruled in the editor specific for the current theme
			$styles = array_merge($styles, $this->processFile(FRONTEND_PATH . '/themes/' . $theme . '/core/layout/css/editor_content.css'));
		}

		// set headers
		SpoonHTTP::setHeaders('Content-type: text/css');

		// output
		if(!empty($styles)) echo implode("\n", $styles);
		exit;
	}

	/**
	 * Grab all the styles from a CSS-file and return them as an array
	 *
	 * @param string $file The file that will be processed
	 * @param bool[optional] $isContent If true, only styles that are scoped inside the .content class will be returned.
	 * @return array
	 */
	private function processFile($file, $isContent = false)
	{
		// if the files doesn't exists we can stop here and just return an empty array
		if(!SpoonFile::exists($file)) return array();

		$content = SpoonFile::getContent($file);
		$matches = array();

		if($isContent)
		{
			// get all CSS that is scoped in .content, if we no items are found we return just an empty array
			if((int) preg_match_all('|^\s?\.content.*\{.*\}|imsU', $content, $matches) <= 0) return array();
		}

		else
		{
			// get all CSS that is scoped in .content, if we no items are found we return just an empty array
			if((int) preg_match_all('|(.*{.*})|imsU', $content, $matches) <= 0) return array();
		}

		// loop the matches and clean them up
		foreach($matches[0] as &$match) $match = $this->cleanupStyle($match);

		return $matches[0];
	}
}
