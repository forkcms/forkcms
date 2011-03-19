<?php

/**
 * This is the analyse-action, it will display an overview of used locale.
 *
 * @package		backend
 * @subpackage	locale
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
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
	 * Format a serialized path-array into something that is usable in a datagrid
	 *
	 * @return	string
	 * @param	string $files	The serialized array with the paths.
	 */
	public static function formatFilesList($files)
	{
		// redefine
		$files = (array) unserialize((string) $files);

		// no files
		if(empty($files)) return '';

		// start
		$return = '<ul>' . "\n";

		// loop files
		foreach($files as $file) $return .= '<li><code title="' . str_replace(PATH_WWW, '', $file) . '">' . wordwrap(str_replace(PATH_WWW, '', $file), 80, '<br />', true) . '</code></li>' . "\n";

		// end
		$return .= '</ul>';

		// cleanup
		return $return;
	}


	/**
	 * Get the passed key should be treated as a label we add it to the array
	 *
	 * @return	void
	 * @param	mixed $value	The value of the element.
	 * @param	mixed $key		The key of the element.
	 * @param	array $items	The array to append the found values to.
	 */
	private static function getLabelsFromBackendNavigation($value, $key, $items)
	{
		// add if needed
		if((string) $key == 'label') $items[] = $value;
	}


	/**
	 * Get the filetree
	 *
	 * @return	array
	 * @param	string $path			The path to get the filetree for.
	 * @param	array[optional] $tree	An array to hold the results.
	 */
	private static function getTree($path, array $tree = array())
	{
		// paths that should be ignored
		$ignore = array(BACKEND_CACHE_PATH, BACKEND_CORE_PATH . '/js/tiny_mce', FRONTEND_CACHE_PATH);

		// get the folder listing
		$items = SpoonDirectory::getList($path, true, array('.svn'));

		// loop items
		foreach($items as $item)
		{
			// if the path should be ignored, skip it
			if(in_array($path . '/' . $item, $ignore)) continue;

			// if the item is a directory we should index it also (recursive)
			if(is_dir($path . '/' . $item)) $tree = self::getTree($path . '/' . $item, $tree);

			else
			{
				// if the file has an extension that has to be processed add it into the tree
				if(in_array(SpoonFile::getExtension($item), array('js', 'php', 'tpl'))) $tree[] = $path . '/' . $item;
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
		/*
		 * Frontend datagrid
		 */
		// create datagrid
		$this->dgFrontend = new BackendDataGridArray($this->processFrontend());

		// overrule default URL
		$this->dgFrontend->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]'), false));

		// sorting columns
		$this->dgFrontend->setSortingColumns(array('language', 'application', 'module', 'type', 'name'), 'name');

		// set colum URLs
		$this->dgFrontend->setColumnURL('name', BackendModel::createURLForAction('add') . '&amp;language=[language]&amp;application=[application]&amp;module=[module]&amp;type=[type]&amp;name=[name]');

		// set column functions
		$this->dgFrontend->setColumnFunction(array(__CLASS__, 'formatFilesList'), '[used_in]', 'used_in', true);

		// add columns
		$this->dgFrontend->addColumn('add', null, BL::lbl('Add'), BackendModel::createURLForAction('add') . '&amp;language=[language]&amp;application=[application]&amp;module=[module]&amp;type=[type]&amp;name=[name]', BL::lbl('Add'));

		/*
		 * Backend datagrid
		 */
		// create datagrid
		$this->dgBackend = new BackendDataGridArray($this->processBackend());

		// overrule default URL
		$this->dgBackend->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]'), false));

		// sorting columns
		$this->dgBackend->setSortingColumns(array('language', 'application', 'module', 'type', 'name'), 'name');

		// set column URLs
		$this->dgBackend->setColumnURL('name', BackendModel::createURLForAction('add') . '&amp;language=[language]&amp;application=[application]&amp;module=[module]&amp;type=[type]&amp;name=[name]');

		// set column functions
		$this->dgBackend->setColumnFunction(array(__CLASS__, 'formatFilesList'), '[used_in]', 'used_in', true);

		// add columns
		$this->dgBackend->addColumn('add', null, BL::lbl('Add'), BackendModel::createURLForAction('add') . '&amp;language=[language]&amp;application=[application]&amp;module=[module]&amp;type=[type]&amp;name=[name]', BL::lbl('Add'));
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
		// init some vars
		$tree = self::getTree(BACKEND_PATH);
		$modules = BackendModel::getModules(false);

		// search fo the error module
		$key = array_search('error', $modules);

		// remove error module
		if($key !== false) unset($modules[$key]);

		$used = array();
		$navigation = Spoon::get('navigation');
		$lbl = array();

		// get labels from navigation
		array_walk_recursive($navigation->navigation, array('self', 'getLabelsFromBackendNavigation'), &$lbl);
		foreach($lbl as $label) $used['lbl'][$label] = array('files' => array('<small>used in navigation</small>'), 'module_specific' => array());

		// get labels from table
		$lbl = (array) BackendModel::getDB()->getColumn('SELECT label FROM pages_extras');
		foreach($lbl as $label) $used['lbl'][$label] = array('files' => array('<small>used in database</small>'), 'module_specific' => array());

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
							if(!in_array($file, $used[$type][$match]['files'])) $used[$type][$match]['files'][] = $file;
						}
					}
				break;

				// PHP file
				case 'php':
					$matches = array();
					$matchesURL = array();

					// get matches
					preg_match_all('/(BackendLanguage|BL)::(get(Label|Error|Message)|act|err|lbl|msg)\(\'(.*)\'(.*)?\)/iU', $content, $matches);

					// match errors
					preg_match_all('/&(amp;)?(error|report)=([A-Z0-9-_]+)/i', $content, $matchesURL);

					// any errormessages
					if(!empty($matchesURL[0]))
					{
						// loop matches
						foreach($matchesURL[3] as $key => $match)
						{
							$type = 'lbl';
							if($matchesURL[2][$key] == 'error') $type = 'Error';
							if($matchesURL[2][$key] == 'report') $type = 'Message';

							$matches[0][] = '';
							$matches[1][] = 'BL';
							$matches[2][] = '';
							$matches[3][] = $type;
							$matches[4][] = SpoonFilter::toCamelCase($match, array('-', '_'));
							$matches[5][] = '';
						}
					}

					// any matches?
					if(!empty($matches[4]))
					{
						// loop matches
						foreach($matches[4] as $key => $match)
						{
							// set type
							$type = 'lbl';
							if($matches[3][$key] == 'Error' || $matches[2][$key] == 'err') $type = 'err';
							if($matches[3][$key] == 'Message' || $matches[2][$key] == 'msg') $type = 'msg';

							// specific module?
							if(isset($matches[5][$key]) && $matches[5][$key] != '')
							{
								// try to grab the module
								$specificModule = $matches[5][$key];
								$specificModule = trim(str_replace(array(',', '\''), '', $specificModule));

								// not core?
								if($specificModule != 'core')
								{
									// dynamic module
									if($specificModule == '$this->URL->getModule(')
									{
										// init var
										$count = 0;

										// replace
										$modulePath = str_replace(BACKEND_MODULES_PATH, '', $file, $count);

										// validate
										if($count == 1)
										{
											// split into chunks
											$chunks = (array) explode('/', trim($modulePath, '/'));

											// set specific module
											if(isset($chunks[0])) $specificModule = $chunks[0];

											// skip
											else continue;
										}
									}

									// init if needed
									if(!isset($used[$type][$match])) $used[$type][$match] = array('files' => array(), 'module_specific' => array());

									// add module
									$used[$type][$match]['module_specific'][] = $specificModule;
								}
							}

							else
							{
								// loop modules
								foreach($modules as $module)
								{
									// determine if this is a module specific locale
									if(substr($match, 0, mb_strlen($module)) == SpoonFilter::toCamelCase($module) && mb_strlen($match) > mb_strlen($module) && ctype_upper(substr($match, mb_strlen($module) + 1, 1)))
									{
										// cleanup
										$match = str_replace(SpoonFilter::toCamelCase($module), '', $match);

										// init if needed
										if(!isset($used[$type][$match])) $used[$type][$match] = array('files' => array(), 'module_specific' => array());

										// add module
										$used[$type][$match]['module_specific'][] = $module;
									}
								}
							}

							// init if needed
							if(!isset($used[$type][$match])) $used[$type][$match] = array('files' => array(), 'module_specific' => array());

							// add file
							if(!in_array($file, $used[$type][$match]['files'])) $used[$type][$match]['files'][] = $file;
						}
					}
				break;

				// template file
				case 'tpl':
					$matches = array();

					// get matches
					preg_match_all('/\{\$(act|err|lbl|msg)([A-Z][a-zA-Z_]*)(\|.*)?\}/U', $content, $matches);

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
							if(!in_array($file, $used[$type][$match]['files'])) $used[$type][$match]['files'][] = $file;
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
								if(substr_count(BL::err($key, $module), '{$' . $type) > 0) $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'backend', 'module' => $module, 'type' => $type, 'name' => $key, 'used_in' => serialize($data['files']));
							}
						}

						// not specific
						else
						{
							// if the error isn't found add it to the list
							if(substr_count(BL::err($key), '{$' . $type) > 0)
							{
								// init var
								$exists = false;

								// loop files
								foreach($data['files'] as $file)
								{
									// init var
									$count = 0;

									// replace
									$modulePath = str_replace(BACKEND_MODULES_PATH, '', $file, $count);

									// validate
									if($count == 1)
									{
										// split into chunks
										$chunks = (array) explode('/', trim($modulePath, '/'));

										// first part is the module
										if(isset($chunks[0]) && BL::err($key, $chunks[0]) != '{$' . $type . SpoonFilter::toCamelCase($chunks[0]) . $key . '}') $exists = true;
									}
								}

								// doesn't exists
								if(!$exists) $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'backend', 'module' => 'core', 'type' => $type, 'name' => $key, 'used_in' => serialize($data['files']));
							}
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
								if(substr_count(BL::lbl($key, $module), '{$' . $type) > 0) $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'backend', 'module' => $module, 'type' => $type, 'name' => $key, 'used_in' => serialize($data['files']));
							}
						}

						// not specific
						else
						{
							// if the label isn't found, check in the specific module
							if(substr_count(BL::lbl($key), '{$' . $type) > 0)
							{
								// init var
								$exists = false;

								// loop files
								foreach($data['files'] as $file)
								{
									// init var
									$count = 0;

									// replace
									$modulePath = str_replace(BACKEND_MODULES_PATH, '', $file, $count);

									// validate
									if($count == 1)
									{
										// split into chunks
										$chunks = (array) explode('/', trim($modulePath, '/'));

										// first part is the module
										if(isset($chunks[0]) && BL::lbl($key, $chunks[0]) != '{$' . $type . SpoonFilter::toCamelCase($chunks[0]) . $key . '}') $exists = true;
									}
								}

								// doesn't exists
								if(!$exists) $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'backend', 'module' => 'core', 'type' => $type, 'name' => $key, 'used_in' => serialize($data['files']));
							}
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
								if(substr_count(BL::msg($key, $module), '{$' . $type) > 0) $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'backend', 'module' => $module, 'type' => $type, 'name' => $key, 'used_in' => serialize($data['files']));
							}
						}

						// not specific
						else
						{
							// if the message isn't found add it to the list
							if(substr_count(BL::msg($key), '{$' . $type) > 0)
							{
								// init var
								$exists = false;

								// loop files
								foreach($data['files'] as $file)
								{
									// init var
									$count = 0;

									// replace
									$modulePath = str_replace(BACKEND_MODULES_PATH, '', $file, $count);

									// validate
									if($count == 1)
									{
										// split into chunks
										$chunks = (array) explode('/', trim($modulePath, '/'));

										// first part is the module
										if(isset($chunks[0]) && BL::msg($key, $chunks[0]) != '{$' . $type . SpoonFilter::toCamelCase($chunks[0]) . $key . '}') $exists = true;
									}
								}

								// doesn't exists
								if(!$exists) $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'backend', 'module' => 'core', 'type' => $type, 'name' => $key, 'used_in' => serialize($data['files']));
							}
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
							if(!in_array($file, $used[$type][$match]['files'])) $used[$type][$match]['files'][] = $file;
						}
					}
				break;

				// PHP file
				case 'php':
					$matches = array();

					// get matches
					preg_match_all('/(FrontendLanguage|FL)::(get(Action|Label|Error|Message)|act|lbl|err|msg)\(\'(.*)\'\)/iU', $content, $matches);

					// any matches?
					if(!empty($matches[4]))
					{
						// loop matches
						foreach($matches[4] as $key => $match)
						{
							$type = 'lbl';
							if($matches[3][$key] == 'Action') $type = 'act';
							if($matches[2][$key] == 'act') $type = 'act';
							if($matches[3][$key] == 'Error') $type = 'err';
							if($matches[2][$key] == 'err') $type = 'err';
							if($matches[3][$key] == 'Message') $type = 'msg';
							if($matches[2][$key] == 'msg') $type = 'msg';

							// init if needed
							if(!isset($used[$type][$match])) $used[$type][$match] = array('files' => array());

							// add file
							if(!in_array($file, $used[$type][$match]['files'])) $used[$type][$match]['files'][] = $file;
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
							if(!in_array($file, $used[$type][$match]['files'])) $used[$type][$match]['files'][] = $file;
						}
					}
				break;
			}
		}

		// init var
		$nonExisting = array();

		// set language
		FrontendLanguage::setLocale(BL::getWorkingLanguage());

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
						if(FL::act($key) == '{$' . $type . $key . '}') $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'frontend', 'module' => 'core', 'type' => $type, 'name' => $key, 'used_in' => serialize($data['files']));
					break;

					// error
					case 'err':
						// if the error isn't available add it to the list
						if(FL::err($key) == '{$' . $type . $key . '}') $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'frontend', 'module' => 'core', 'type' => $type, 'name' => $key, 'used_in' => serialize($data['files']));
					break;

					// label
					case 'lbl':
						// if the label isn't available add it to the list
						if(FL::lbl($key) == '{$' . $type . $key . '}') $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'frontend', 'module' => 'core', 'type' => $type, 'name' => $key, 'used_in' => serialize($data['files']));
					break;

					// message
					case 'msg':
						// if the message isn't available add it to the list
						if(FL::msg($key) == '{$' . $type . $key . '}') $nonExisting[] = array('language' => BL::getWorkingLanguage(), 'application' => 'frontend', 'module' => 'core', 'type' => $type, 'name' => $key, 'used_in' => serialize($data['files']));
					break;
				}
			}
		}

		// return
		return $nonExisting;
	}
}

?>