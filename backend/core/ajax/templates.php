<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will generate JS that represents the templates that will be available in CK Editor
 *
 * @author Tijs Verkoyen <tijs@sumocoders.eu>
 */
class BackendCoreAjaxTemplates extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// init vars
		$templates = '';
		$theme = BackendModel::getModuleSetting('core', 'theme');
		$files[] = BACKEND_PATH . '/core/layout/editor_templates/templates.js';
		$themePath = FRONTEND_PATH . '/themes/' . $theme . '/core/layout/editor_templates/templates.js';

		if(SpoonFile::exists($themePath)) $files[] = $themePath;

		// loop all files
		foreach($files as $file)
		{
			// process file
			$templates[] = $this->processFile($file);
		}

		// set headers
		SpoonHTTP::setHeaders('Content-type: text/javascript');

		// output the templates
		if(!empty($templates))
		{
			echo 'CKEDITOR.addTemplates(\'default\', { imagesPath: \'/\', templates:' . "\n";
			echo '[' . implode(',' . "\n", $templates) . ']' . "\n";
			echo '});';
		}
		exit;
	}

	/**
	 * Process the content of the file.
	 *
	 * @param string $file The file to process.
	 * @return boolean|string
	 */
	private function processFile($file)
	{
		// if the files doesn't exists we can stop here and just return an empty string
		if(!SpoonFile::exists($file)) return '';

		// fetch content from file
		$content = SpoonFile::getContent($file);

		$search = array("\n", "\r", "\t", 'image: "/', 'image: \'/');
		$replace = array('', '', '', 'image: "', 'image: \'');

		// replace some stuff, we need to replace the first slash for images, because we will set it in the config
		// it ourself, otherwise no images will be shown
		$content = str_replace($search, $replace, $content);

		// remove array stuff
		$content = trim($content, '[]');

		// split the templates
		$content = str_replace('},', "},\n", $content);

		return $content;
	}
}
