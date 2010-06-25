<?php

/**
 * BackendLocaleAnalyse
 * This is the analyse-action, it will display an overview of used locale.
 *
 * @package		backend
 * @subpackage	locale
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendLocaleAnalyse extends BackendBaseActionIndex
{
	/**
	 * Datagrid instances
	 *
	 * @var	BackendDataGridArray
	 */
	private $dgBackend,
			$dgFrontend;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load datagrids
		$this->loadDataGrids();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Get the filetree
	 *
	 * @return	array
	 * @param	string $path			The path to get the filetree for.
	 * @param	array[optional] $tree	An array to hold the results
	 */
	private static function getTree($path, array $tree = array())
	{
		// paths that should be ignored
		$ignore = array(BACKEND_CACHE_PATH, BACKEND_CORE_PATH .'/js/tiny_mce', FRONTEND_CACHE_PATH);

		// get the folder listing
		$items = SpoonDirectory::getList($path, true, array('.svn'));

		// loop items
		foreach($items as $item)
		{
			// if the path should be ignored, skip it
			if(in_array($path .'/'. $item, $ignore)) continue;

			// if the item is a directory we should index it also (recursive)
			if(is_dir($path .'/'. $item)) $tree = self::getTree($path .'/'. $item, $tree);

			else
			{
				// if the file has an extension that has to be processed add it into the tree
				if(in_array(SpoonFile::getExtension($item), array('js', 'php', 'tpl'))) $tree[] = $path .'/'. $item;
			}
		}

		// return
		return $tree;
	}


	/**
	 * Load the datagrids
	 *
	 * @return	void
	 */
	private function loadDataGrids()
	{
		// create datagrid
		$this->dgFrontend = new BackendDataGridArray($this->processFrontend());

		// overrule default URL
		$this->dgFrontend->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]'), false));

		// header labels
		$this->dgFrontend->setHeaderLabels(array('language' => ucfirst(BL::getLabel('Language')), 'application' => ucfirst(BL::getLabel('Application')), 'module' => ucfirst(BL::getLabel('Module')), 'type' => ucfirst(BL::getLabel('Type')), 'name' => ucfirst(BL::getLabel('Name'))));

		// sorting columns
		$this->dgFrontend->setSortingColumns(array('language', 'application', 'module', 'type', 'name'), 'name');

		// set colum URLs
		$this->dgFrontend->setColumnURL('name', BackendModel::createURLForAction('add') .'&amp;language=[language]&amp;application=[application]&amp;module=[module]&amp;type=[type]&amp;name=[name]');

		// add columns
		$this->dgFrontend->addColumn('add', null, BL::getLabel('Add'), BackendModel::createURLForAction('add') .'&amp;language=[language]&amp;application=[application]&amp;module=[module]&amp;type=[type]&amp;name=[name]', BL::getLabel('Add'));


		// create datagrid
		$this->dgBackend = new BackendDataGridArray($this->processBackend());

		// overrule default URL
		$this->dgBackend->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]'), false));

		// header labels
		$this->dgBackend->setHeaderLabels(array('language' => ucfirst(BL::getLabel('Language')), 'application' => ucfirst(BL::getLabel('Application')), 'module' => ucfirst(BL::getLabel('Module')), 'type' => ucfirst(BL::getLabel('Type')), 'name' => ucfirst(BL::getLabel('Name'))));

		// sorting columns
		$this->dgBackend->setSortingColumns(array('language', 'application', 'module', 'type', 'name'), 'name');

		// set colum URLs
		$this->dgBackend->setColumnURL('name', BackendModel::createURLForAction('add') .'&amp;language=[language]&amp;application=[application]&amp;module=[module]&amp;type=[type]&amp;name=[name]');

		// add columns
		$this->dgBackend->addColumn('add', null, BL::getLabel('Add'), BackendModel::createURLForAction('add') .'&amp;language=[language]&amp;application=[application]&amp;module=[module]&amp;type=[type]&amp;name=[name]', BL::getLabel('Add'));
	}



	/**
	 * Parse & display the page
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse datagrid
		$this->tpl->assign('dgBackend', ($this->dgBackend->getNumResults() != 0) ? $this->dgBackend->getContent() : false);
		$this->tpl->assign('dgFrontend', ($this->dgFrontend->getNumResults() != 0) ? $this->dgFrontend->getContent() : false);
	}


	/**
	 * Process the backend
	 *
	 * @return	array
	 */
	private function processBackend()
	{
		// get files to process
		$tree = self::getTree(BACKEND_PATH);
		$modules = BackendModel::getModules(false);
		$used = array();

		// loop files
		foreach($tree as $file)
		{
			// grab content
			$content = SpoonFile::getContent($file);

			// process based on extension
			switch(SpoonFile::getExtension($file))
			{
				// javascript file
				case 'js':
					$matches = array();

					// get matches
					preg_match_all('/\{\$(act|err|lbl|msg)(.*)(\|.*)?\}/iU', $content, $matches);

					// any matches?
					if(isset($matches[2]))
					{
						// loop matches
						foreach($matches[2] as $key => $match)
						{
							// set type
							$type = $matches[1][$key];

							// loop modules
							foreach($modules as $module)
							{
								// determine if this is a module specific locale
								if(substr($match, 0, mb_strlen($module)) == SpoonFilter::toCamelCase($module) && mb_strlen($match) > mb_strlen($module))
								{
									// cleanup
									$match = str_replace(SpoonFilter::toCamelCase($module), '', $match);

									// init if needed
									if(!isset($used[$type][$match])) $used[$type][$match] = array('files' => array(), 'module_specific' => array());

									// add module
									$used[$type][$match]['module_specific'][] = $module;
								}
							}

							// init if needed
							if(!isset($used[$match])) $used[$type][$match] = array('files' => array(), 'module_specific' => array());

							// add file
							$used[$type][$match]['files'][] = $file;
						}
					}
				break;

				// PHP file
				case 'php':
					$matches = array();

					// get matches
					preg_match_all('/(BackendLanguage|BL)::get(Label|Error|Message)\(\'(.*)\'(.*)?\)/iU', $content, $matches);

					// any matches?
					if(isset($matches[3]))
					{
						// loop matches
						foreach($matches[3] as $key => $match)
						{
							// set type
							$type = 'lbl';
							if($matches[2][$key] == 'Error') $type = 'err';
							if($matches[2][$key] == 'Message') $type = 'msg';

							// loop modules
							foreach($modules as $module)
							{
								// determine if this is a module specific locale
								if(substr($match, 0, mb_strlen($module)) == SpoonFilter::toCamelCase($module) && mb_strlen($match) > mb_strlen($module))
								{
									// cleanup
									$match = str_replace(SpoonFilter::toCamelCase($module), '', $match);

									// init if needed
									if(!isset($used[$type][$match])) $used[$type][$match] = array('files' => array(), 'module_specific' => array());

									// add module
									$used[$type][$match]['module_specific'][] = $module;
								}
							}

							// init if needed
							if(!isset($used[$type][$match])) $used[$type][$match] = array('files' => array(), 'module_specific' => array());

							// add file
							$used[$type][$match]['files'][] = $file;
						}
					}
				break;

				// template file
				case 'tpl':
					$matches = array();

					// get matches
					preg_match_all('/\{\$(act|err|lbl|msg)([a-z-_]*)(\|.*)?\}/iU', $content, $matches);

					// any matches?
					if(isset($matches[2]))
					{
						// loop matches
						foreach($matches[2] as $key => $match)
						{
							// set type
							$type = $matches[1][$key];

							// loop modules
							foreach($modules as $module)
							{
								// determine if this is a module specific locale
								if(substr($match, 0, mb_strlen($module)) == SpoonFilter::toCamelCase($module) && mb_strlen($match) > mb_strlen($module))
								{
									// cleanup
									$match = str_replace(SpoonFilter::toCamelCase($module), '', $match);

									// init if needed
									if(!isset($used[$type][$match])) $used[$type][$match] = array('files' => array(), 'module_specific' => array());

									// add module
									$used[$type][$match]['module_specific'][] = $module;
								}
							}

							// init if needed
							if(!isset($used[$type][$match])) $used[$type][$match] = array('files' => array(), 'module_specific' => array());

							// add file
							$used[$type][$match]['files'][] = $file;
						}
					}
				break;
			}
		}

		// init var
		$nonExisting = array();

		// check if the locale is present in the current language
		foreach($used as $type => $items)
		{
			// loop items
			foreach($items as $key => $data)
			{
				// process based on type
				switch($type)
				{
					// error
					case 'err':
						// module specific?
						if(!empty($data['module_specific']))
						{
							// loop modules
							foreach($data['module_specific'] as $module)
							{
								// if the error isn't found add it to the list
								if(BL::getError($key, $module) == '{$'. $type .'Locale'. $key .'}') $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'backend', 'module' => $module, 'type' => $type, 'name' => $key);
							}
						}

						// not specific
						else
						{
							// if the error isn't found add it to the list
							if(BL::getError($key) == '{$'. $type .'Locale'. $key .'}') $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'backend', 'module' => 'core', 'type' => $type, 'name' => $key);
						}
					break;

					// label
					case 'lbl':
						// module specific?
						if(!empty($data['module_specific']))
						{
							// loop modules
							foreach($data['module_specific'] as $module)
							{
								// if the label isn't found add it to the list
								if(BL::getLabel($key, $module) == '{$'. $type .'Locale'. $key .'}') $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'backend', 'module' => $module, 'type' => $type, 'name' => $key);
							}
						}

						// not specific
						else
						{
							// if the label isn't found add it to the list
							if(BL::getLabel($key) == '{$'. $type .'Locale'. $key .'}') $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'backend', 'module' => 'core', 'type' => $type, 'name' => $key);
						}
					break;

					// message
					case 'msg':
						// module specific?
						if(!empty($data['module_specific']))
						{
							// loop modules
							foreach($data['module_specific'] as $module)
							{
								// if the message isn't found add it to the list
								if(BL::getMessage($key, $module) == '{$'. $type .'Locale'. $key .'}') $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'backend', 'module' => $module, 'type' => $type, 'name' => $key);
							}
						}

						// not specific
						else
						{
							// if the message isn't found add it to the list
							if(BL::getMessage($key) == '{$'. $type .'Locale'. $key .'}') $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'backend', 'module' => 'core', 'type' => $type, 'name' => $key);
						}
					break;
				}
			}
		}

		// return
		return $nonExisting;
	}


	/**
	 * Process the frontend
	 *
	 * @return	array
	 */
	private function processFrontend()
	{
		// get files to process
		$tree = self::getTree(FRONTEND_PATH);
		$used = array();

		// loop files
		foreach($tree as $file)
		{
			// grab content
			$content = SpoonFile::getContent($file);

			// process the file based on extension
			switch(SpoonFile::getExtension($file))
			{
				// javascript file
				case 'js':
					$matches = array();

					// get matches
					preg_match_all('/\{\$(act|err|lbl|msg)(.*)(\|.*)?\}/iU', $content, $matches);

					// any matches?
					if(isset($matches[2]))
					{
						// loop matches
						foreach($matches[2] as $key => $match)
						{
							// set type
							$type = $matches[1][$key];

							// init if needed
							if(!isset($used[$match])) $used[$type][$match] = array('files' => array());

							// add file
							$used[$type][$match]['files'][] = $file;
						}
					}
				break;

				// PHP file
				case 'php':
					$matches = array();

					// get matches
					preg_match_all('/(FrontendLanguage|FL)::get(Action|Label|Error|Message)\(\'(.*)\'\)/iU', $content, $matches);

					// any matches?
					if(isset($matches[3]))
					{
						// loop matches
						foreach($matches[3] as $key => $match)
						{
							$type = 'lbl';
							if($matches[2][$key] == 'Action') $type = 'act';
							if($matches[2][$key] == 'Error') $type = 'err';
							if($matches[2][$key] == 'Message') $type = 'msg';

							// init if needed
							if(!isset($used[$type][$match])) $used[$type][$match] = array('files' => array());

							// add file
							$used[$type][$match]['files'][] = $file;
						}
					}
				break;

				// template file
				case 'tpl':
					$matches = array();

					// get matches
					preg_match_all('/\{\$(act|err|lbl|msg)([a-z-_]*)(\|.*)?\}/iU', $content, $matches);

					// any matches?
					if(isset($matches[2]))
					{
						// loop matches
						foreach($matches[2] as $key => $match)
						{
							// set type
							$type = $matches[1][$key];

							// init if needed
							if(!isset($used[$type][$match])) $used[$type][$match] = array('files' => array());

							// add file
							$used[$type][$match]['files'][] = $file;
						}
					}
				break;
			}
		}

		// init var
		$nonExisting = array();

		// check if the locale is present in the current language
		foreach($used as $type => $items)
		{
			// loop items
			foreach($items as $key => $data)
			{
				// process based on type
				switch($type)
				{
					// action
					case 'act':
						// if the action isn't available add it to the list
						if(FrontendLanguage::getAction($key) == '{$'. $type . $key .'}') $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'frontend', 'module' => 'core', 'type' => $type, 'name' => $key);
					break;

					// error
					case 'err':
						// if the error isn't available add it to the list
						if(FrontendLanguage::getError($key) == '{$'. $type . $key .'}') $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'frontend', 'module' => 'core', 'type' => $type, 'name' => $key);
					break;

					// label
					case 'lbl':
						// if the label isn't available add it to the list
						if(FrontendLanguage::getLabel($key) == '{$'. $type . $key .'}') $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'frontend', 'module' => 'core', 'type' => $type, 'name' => $key);
					break;

					// message
					case 'msg':
						// if the message isn't available add it to the list
						if(FrontendLanguage::getMessage($key) == '{$'. $type . $key .'}') $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'frontend', 'module' => 'core', 'type' => $type, 'name' => $key);
					break;
				}
			}
		}

		// return
		return $nonExisting;
	}
}

?>