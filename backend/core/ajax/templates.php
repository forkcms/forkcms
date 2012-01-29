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
		$templates = array();
		$theme = BackendModel::getModuleSetting('core', 'theme');
		$files[] = BACKEND_PATH . '/core/layout/editor_templates/templates.js';
		$themePath = FRONTEND_PATH . '/themes/' . $theme . '/core/layout/editor_templates/templates.js';

		if(SpoonFile::exists($themePath)) $files[] = $themePath;

		// loop all files
		foreach($files as $file)
		{
			// process file
			$templates = array_merge($templates, $this->processFile($file));
		}

		// set headers
		SpoonHTTP::setHeaders('Content-type: text/javascript');

		// output the templates
		if(!empty($templates))
		{
			echo 'CKEDITOR.addTemplates(\'default\', { imagesPath: \'/\', templates:' . "\n";
			echo json_encode($templates) . "\n";
			echo '});';
		}
		exit;
	}

	/**
	 * Process the content of the file.
	 *
	 * @param string $file The file to process.
	 * @return boolean|array
	 */
	private function processFile($file)
	{
		// if the files doesn't exists we can stop here and just return an empty string
		if(!SpoonFile::exists($file)) return array();

		// fetch content from file
		$content = SpoonFile::getContent($file);
		$json = @json_decode($content, true);

		// skip invalid JSON
		if($json === false || $json === null) return array();

		$return = array();

		// loop templates
		foreach($json as $template)
		{
			// skip items without a title
			if(!isset($template['title'])) continue;

			if(isset($template['file']))
			{
				if(SpoonFile::exists(PATH_WWW . $template['file']))
				{
					$template['html'] = SpoonFile::getContent(PATH_WWW . $template['file']);
				}
			}

			// skip items without HTML
			if(!isset($template['html'])) continue;

			$image = '';
			if(isset($template['image']))
			{
				// we have to remove the first slash, because that is set in the wrapper. Otherwise the images don't work
				$image = ltrim($template['image'], '/');
			}

			$temp['title'] = $template['title'];
			$temp['description'] = (isset($template['description'])) ? $template['description'] : '';
			$temp['image'] = $image;
			$temp['html'] = $template['html'];

			// add the template
			$return[] = $temp;
		}

		return $return;
	}
}
