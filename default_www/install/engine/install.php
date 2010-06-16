<?php

/**
 * Installer
 *
 * @package		installer
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class Installer
{
	/**
	 * Form instance
	 *
	 * @var	SpoonForm
	 */
	private $frm;


	/**
	 * Template instance
	 *
	 * @var	SpoonTemplate
	 */
	private $tpl;


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// do init
		$this->init();

		// get step
		$step = SpoonFilter::getGetValue('step', array(1, 2, 3, 4), 1, 'int');

		// in step 1 we don't know where Spoon is located so we cant use the template-engine
		if($step != 1)
		{
			// create the template
			$this->tpl = new SpoonTemplate();

			// set some options
			$this->tpl->setCompileDirectory(WWW_PATH .'/install/cache');
			$this->tpl->setForceCompile(SPOON_DEBUG);
		}

		// execute the correct step
		switch($step)
		{
			case 1:
				$this->doStep1();
			break;

			case 2:
				$this->doStep2();
			break;

			case 3:
				$this->doStep3();
			break;

			case 4:
				$this->doStep4();
			break;
		}

		// parse the form
		if($this->frm !== null && $this->tpl !== null) $this->frm->parse($this->tpl);

		// show the template
		if($this->tpl !== null) $this->tpl->display(WWW_PATH .'/install/layout/templates/'. $step .'.tpl');
	}


	/**
	 * Execute step 1
	 *
	 * @return	void
	 */
	private function doStep1()
	{
		// init vars
		$hasError = false;
		$variables = array();

		// init
		$variables['error'] = '';
		$variables['WWW_PATH'] = WWW_PATH;
		$variables['SPOON_PATH'] = SPOON_PATH;

		// check PHP version
		$version = (int) str_replace('.', '', PHP_VERSION);
		if($version >= 520)
		{
			$variables['phpVersion'] = 'ok';
			$variables['phpVersionStatus'] = 'ok';
		}
		else
		{
			$variables['phpVersion'] = 'nok';
			$variables['phpVersionStatus'] = 'not ok';
			$hasError = true;
		}

		// check if cURL is loaded
		if(extension_loaded('curl'))
		{
			$variables['extensionCURL'] = 'ok';
			$variables['extensionCURLStatus'] = 'ok';
		}
		else
		{
			$variables['extensionCURL'] = 'nok';
			$variables['extensionCURLStatus'] = 'not ok';
			$hasError = true;
		}

		// check if SimpleXML is loaded
		if(extension_loaded('SimpleXML'))
		{
			$variables['extensionSimpleXML'] = 'ok';
			$variables['extensionSimpleXMLStatus'] = 'ok';
		}
		else
		{
			$variables['extensionSimpleXML'] = 'nok';
			$variables['extensionSimpleXMLStatus'] = 'not ok';
			$hasError = true;
		}

		// check if SPL is loaded
		if(extension_loaded('SPL'))
		{
			$variables['extensionSPL'] = 'ok';
			$variables['extensionSPLStatus'] = 'ok';
		}
		else
		{
			$variables['extensionSPL'] = 'nok';
			$variables['extensionSPLStatus'] = 'not ok';
			$hasError = true;
		}

		// check if PDO is loaded
		if(extension_loaded('PDO'))
		{
			$variables['extensionPDO'] = 'ok';
			$variables['extensionPDOStatus'] = 'ok';
		}
		else
		{
			$variables['extensionPDO'] = 'nok';
			$variables['extensionPDOStatus'] = 'not ok';
			$hasError = true;
		}

		// check if mbstring is loaded
		if(extension_loaded('mbstring'))
		{
			$variables['extensionMBString'] = 'ok';
			$variables['extensionMBStringStatus'] = 'ok';
		}
		else
		{
			$variables['extensionMBString'] = 'nok';
			$variables['extensionMBStringStatus'] = 'not ok';
			$hasError = true;
		}

		// check if iconv is loaded
		if(extension_loaded('iconv'))
		{
			$variables['extensionIconv'] = 'ok';
			$variables['extensionIconvStatus'] = 'ok';
		}
		else
		{
			$variables['extensionIconv'] = 'nok';
			$variables['extensionIconvStatus'] = 'not ok';
			$hasError = true;
		}

		// check if GD is loaded and the correct version is installed
		if(extension_loaded('gd') && function_exists('gd_info'))
		{
			$variables['extensionGD2'] = 'ok';
			$variables['extensionGD2Status'] = 'ok';
		}
		else
		{
			$variables['extensionGD2'] = 'nok';
			$variables['extensionGD2Status'] = 'not ok';
			$hasError = true;
		}

		// check if the backend-cache-directory is writable
		if(is_writable(WWW_PATH .'/backend/cache/'))
		{
			$variables['fileSystemBackendCache'] = 'ok';
			$variables['fileSystemBackendCacheStatus'] = 'ok';
		}
		else
		{
			$variables['fileSystemBackendCache'] = 'nok';
			$variables['fileSystemBackendCacheStatus'] = 'not ok';
			$hasError = true;
		}

		// check if the frontend-cache-directory is writable
		if(is_writable(WWW_PATH .'/frontend/cache/'))
		{
			$variables['fileSystemFrontendCache'] = 'ok';
			$variables['fileSystemFrontendCacheStatus'] = 'ok';
		}
		else
		{
			$variables['fileSystemFrontendCache'] = 'nok';
			$variables['fileSystemFrontendCacheStatus'] = 'not ok';
			$hasError = true;
		}

		// check if the frontend-files-directory is writable
		if(is_writable(WWW_PATH .'/frontend/files/'))
		{
			$variables['fileSystemFrontendFiles'] = 'ok';
			$variables['fileSystemFrontendFilesStatus'] = 'ok';
		}
		else
		{
			$variables['fileSystemFrontendFiles'] = 'nok';
			$variables['fileSystemFrontendFilesStatus'] = 'not ok';
			$hasError = true;
		}

		// check if the Spoon-directory is writable
		if(is_writable(SPOON_PATH))
		{
			$variables['fileSystemLibrary'] = 'ok';
			$variables['fileSystemLibraryStatus'] = 'ok';
		}
		else
		{
			$variables['fileSystemLibrary'] = 'nok';
			$variables['fileSystemLibraryStatus'] = 'not ok';
			$hasError = true;
		}

		// check if the installer-directory is writable
		if(is_writable(WWW_PATH .'/install'))
		{
			$variables['fileSystemInstaller'] = 'ok';
			$variables['fileSystemInstallerStatus'] = 'ok';
		}
		else
		{
			$variables['fileSystemInstaller'] = 'nok';
			$variables['fileSystemInstallerStatus'] = 'not ok';
			$hasError = true;
		}

		// any errors?
		if($hasError)
		{
			// addign the variable
			$variables['error'] = '<div class="message errorMessage singleMessage"><p>Fix the items below that are marked as <em>Not ok</em>.</p></div><br />';
		}
		else
		{
			// get values
			$buttonValue = SpoonFilter::getPostValue('installer', array('Next'), 'N');
			$stepValue = SpoonFilter::getPostValue('step', array(1, 2), 'N');

			// is the form submitted?
			if($buttonValue != 'N' && $stepValue != 'N') SpoonHTTP::redirect('/install/index.php?step=2');
		}

		// build template (I know this is wierd)
		$tpl = SpoonFile::getContent(WWW_PATH .'/install/layout/templates/1.tpl');

		// build the search & replace array
		$search = array_keys($variables);
		$replace = array_values($variables);

		// loop search values
		foreach($search as $key => $value) $search[$key] = '{$'. $value .'}';

		// build output
		$output = str_replace($search, $replace, $tpl);

		// show
		echo $output;

		// stop the script
		exit;
	}


	/**
	 * Execute step 2
	 *
	 * @return	void
	 */
	private function doStep2()
	{
		// init var (I know this is somewhat Netlash specific)
		$projectName = isset($_SERVER['HTTP_HOST']) ? str_replace(array('.svn.be', '.indevelopment.be', '.local'), '', $_SERVER['HTTP_HOST']) : '';

		// create the form
		$this->frm = new SpoonForm('step2');

		// create elements
		$this->frm->addText('spoon_path', SPOON_PATH);

		$this->frm->addText('debug_email', '');

		$this->frm->addText('database_host', '127.0.0.1');
		$this->frm->addText('database_name', $projectName);
		$this->frm->addText('database_username', $projectName);
		$this->frm->addText('database_password', '');

		$this->frm->addText('site_domain', (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '');
		$this->frm->addText('site_title', $projectName);

		$this->frm->addRadiobutton('multilanguage', array(array('value' => 'Y', 'label' => 'Multiple languages'),
													array('value' => 'N', 'label' => 'Just one language')), 'Y');

		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// validate
			if($this->frm->getField('debug_email')->isFilled()) $this->frm->getField('debug_email')->isEmail('This is an invalid email-address.');
			$this->frm->getField('spoon_path')->isFilled('This field is required.');
			$this->frm->getField('database_host')->isFilled('This field is required.');
			$this->frm->getField('database_name')->isFilled('This field is required.');
			$this->frm->getField('database_username')->isFilled('This field is required.');
			$this->frm->getField('database_password')->isFilled('This field is required.');
			$this->frm->getField('site_domain')->isFilled('This field is required.');
			$this->frm->getField('site_title')->isFilled('This field is required.');

			try
			{
				// create db-instance
				$db = new SpoonDatabase('mysql', $this->frm->getField('database_host')->getValue(), $this->frm->getField('database_username')->getValue(), $this->frm->getField('database_password')->getValue(), $this->frm->getField('database_name')->getValue());

				// try to get the modules
				$modules = $db->getRecords('SELECT * FROM modules');

				// no modules found, they should load the dump
				if(empty($modules)) throw new Exception('empty');
			}

			// catch exceptions
			catch(Exception $e)
			{
				// add error
				$this->frm->getField('database_name')->addError('Invalid database. Check the credentials and/or if you imported the default.sql-file.');
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build variables
				$variables['<spoon-debug-email>'] = $this->frm->getField('debug_email')->getValue(true);
				$variables['<database-name>'] = $this->frm->getField('database_name')->getValue(true);
				$variables['<database-hostname>'] = $this->frm->getField('database_host')->getValue(true);
				$variables['<database-username>'] = $this->frm->getField('database_username')->getValue(true);
				$variables['<database-password>'] = $this->frm->getField('database_password')->getValue(true);
				$variables['<default-domain>'] = $this->frm->getField('site_domain')->getValue(true);
				$variables['<default-title>'] = $this->frm->getField('site_title')->getValue(true);
				$variables['<multilanguage>'] = ($this->frm->getField('multilanguage')->getValue() == 'Y') ? 'true' : 'false';
				$variables['<path-of-document-root>'] = WWW_PATH;

				// store some values in the session
				SpoonSession::set('database_hostname', $this->frm->getField('database_host')->getValue(true));
				SpoonSession::set('database_name', $this->frm->getField('database_name')->getValue(true));
				SpoonSession::set('database_username', $this->frm->getField('database_username')->getValue(true));
				SpoonSession::set('database_password', $this->frm->getField('database_password')->getValue(true));
				SpoonSession::set('site_domain', $this->frm->getField('site_domain')->getValue(true));
				SpoonSession::set('site_title', $this->frm->getField('site_title')->getValue(true));

				// grab content
				$globalsContent = SpoonFile::getContent(SPOON_PATH .'/globals.example.php');

				// assign the variables
				$globalsContent = str_replace(array_keys($variables), array_values($variables), $globalsContent);

				// write the file
				SpoonFile::setContent(SPOON_PATH .'/globals.php', $globalsContent);

				// redirect
				SpoonHTTP::redirect('/install/index.php?step=3');
			}
		}
	}


	/**
	 * Execute step 3
	 *
	 * @return	void
	 */
	private function doStep3()
	{
		// validate
		if(!SpoonSession::exists('database_name')) SpoonHTTP::redirect('/install/index.php?step=2');
		if(!SpoonSession::exists('database_hostname')) SpoonHTTP::redirect('/install/index.php?step=2');
		if(!SpoonSession::exists('database_username')) SpoonHTTP::redirect('/install/index.php?step=2');
		if(!SpoonSession::exists('database_password')) SpoonHTTP::redirect('/install/index.php?step=2');
		if(!SpoonSession::exists('site_domain')) SpoonHTTP::redirect('/install/index.php?step=2');

		// create db connection
		$db = new SpoonDatabase('mysql',
								SpoonSession::get('database_hostname'),
								SpoonSession::get('database_username'),
								SpoonSession::get('database_password'),
								SpoonSession::get('database_name'));

		// get all modules
		$modules = $db->getRecords('SELECT name AS value, name AS label
										FROM modules
										ORDER BY name', null, 'value');

		// define modules that are always checked
		$alwaysChecked = array('core', 'contact', 'locale', 'pages', 'settings', 'sitemap', 'users');

		// disable alwayschecked items
		foreach($alwaysChecked as $key) $modules[$key]['attributes'] = array('disabled' => 'disabled');

		// create the form
		$this->frm = new SpoonForm('step3');

		// create elements
		$this->frm->addMultiCheckbox('modules', $modules, $alwaysChecked);

		$this->frm->addText('api_email');

		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// validate
			$this->frm->getField('api_email')->isFilled('Field is required.');

			// no errors?
			if($this->frm->isCorrect())
			{
				// require the API
				require_once 'external/fork_api.php';

				// create new instance
				$api = new ForkAPI();

				// get the keys
				$keys = $api->coreRequestKeys(SpoonSession::get('site_domain'), $this->frm->getField('api_email')->getValue());

				$this->storeSetting('core', 'fork_api_public_key', $keys['public']);
				$this->storeSetting('core', 'fork_api_private_key', $keys['private']);

				// define some constants
				define('DB_TYPE', 'mysql');
				define('DB_HOSTNAME', SpoonSession::get('database_hostname'));
				define('DB_USERNAME', SpoonSession::get('database_username'));
				define('DB_PASSWORD', SpoonSession::get('database_password'));
				define('DB_DATABASE', SpoonSession::get('database_name'));
				define('BACKEND_CACHE_PATH', WWW_PATH .'/backend/cache');
				define('FRONTEND_CACHE_PATH', WWW_PATH .'/frontend/cache');
				define('SITE_URL', 'http://'. SpoonSession::get('site_domain'));

				// get checked modules
				$checkedModules = (array) $this->frm->getField('modules')->getValue();
				$checkedModules = array_merge($checkedModules, $alwaysChecked);

				// store in session
				SpoonSession::set('admin_email', $this->frm->getField('api_email')->getValue());
				SpoonSession::set('modules', $checkedModules);

				// update
				$db->update('modules', array('active' => 'N'), 'name NOT IN("'. implode('","', $checkedModules) .'")');

				// loop checked modules
				foreach($checkedModules as $module)
				{
					// build the path
					$modulePath = WWW_PATH .'/backend/modules/'. $module;

					// core is a special module
					if($module == 'core') $modulePath = WWW_PATH .'/backend/core';

					// check if the model file exists
					if(SpoonFile::exists($modulePath .'/engine/model.php'))
					{
						// require
						require_once $modulePath .'/engine/model.php';

						// build class name
						$className = SpoonFilter::toCamelCase('backend_'. $module .'_model');

						// core is a special module
						if($module == 'core') $className = 'BackendModel';

						// check if the install method exists
						if(method_exists($className, 'install'))
						{
							// call the method
							$messages[$module] = call_user_func(array($className, 'install'));
						}
					}
				}

				// store the messages
				SpoonSession::set('messages', serialize($messages));

				// redirect
				SpoonHTTP::redirect('/install/index.php?step=4');
			}
		}
	}


	/**
	 * Execute step 4
	 *
	 * @return	void
	 */
	private function doStep4()
	{
		// validate
		if(!SpoonSession::exists('messages')) SpoonHTTP::redirect('/install/index.php?step=2');

		// get messages
		$storedMessages = (array) unserialize(SpoonSession::get('messages'));

		// init var
		$items = array();

		// loop messages
		foreach($storedMessages as $module => $messages)
		{
			// any messages?
			if(!empty($messages)) $items[] = array('name' => $module, 'messages' => $messages);
		}

		// assign
		$this->tpl->assign('items', $items);
	}


	/**
	 * Initialize some constants and variables
	 *
	 * @return	void
	 */
	private function init()
	{
		// get the www path
		define('WWW_PATH', realpath(str_replace('/install/engine/install.php', '', __FILE__)));

		// calculate the homefolder
		$homeFolder = realpath(WWW_PATH .'/..');

		// attempt to open directory
		$directory = @opendir($homeFolder);

		// do your thing if directory-handle isn't false
		if($directory !== false)
		{
			// start reading
			while((($folder = readdir($directory)) !== false))
			{
				// no '.' and '..' and it's a file
				if(($folder != '.') && ($folder != '..'))
				{
					// directory
					if(is_dir($homeFolder .'/'. $folder .'/spoon'))
					{
						// init var
						$matches = array();

						// get content
						$fileContent = file_get_contents($homeFolder .'/'. $folder .'/spoon/spoon.php');

						// try to get the version
						preg_match('/SPOON_VERSION\',\s\'(.*)\'/', $fileContent, $matches);

						if(!isset($matches[1])) continue;

						else
						{
							// get the version
							$version = (int) str_replace('.', '', $matches[1]);

							// validate the version
							if($version <= 120)
							{
								// set Spoon path
								define('SPOON_PATH', $homeFolder .'/'. $folder);

								// stop looking arround
								break;
							}
						}
					}
				}
			}
		}

		// close directory
		@closedir($directory);

		// validate
		if(!defined('SPOON_PATH'))
		{
			echo 'Can\'t find Spoon. Make sure their is a folder containing spoon on the same level as the document_root';
			exit;
		}

		// store in variables
		$this->variables['WWW_PATH'] = WWW_PATH;
		$this->variables['SPOON_PATH'] = SPOON_PATH;

		// set include path
		set_include_path(SPOON_PATH . PATH_SEPARATOR . get_include_path());

		// define some constants
		define('SPOON_DEBUG', true);

		// require spoon
		require_once 'spoon/spoon.php';

		// get spoon version
		$version = (int) str_replace('.', '', SPOON_VERSION);

		// validate version
		if($version < 120)
		{
			echo 'Can\'t find Spoon. Make sure their is a folder containing spoon on the same level as the document_root';
			exit;
		}
	}


	/**
	 * Store a setting
	 *
	 * @return	void
	 * @param	string $module
	 * @param	string $name
	 * @param	string $value
	 */
	private function storeSetting($module, $name, $value)
	{
		// redefine
		$module = (string) $module;
		$name = (string) $name;
		$value = serialize($value);

		// create db connection
		$db = new SpoonDatabase('mysql',
								SpoonSession::get('database_hostname'),
								SpoonSession::get('database_username'),
								SpoonSession::get('database_password'),
								SpoonSession::get('database_name'));

		// store the keys
		$db->execute('INSERT INTO modules_settings(module, name, value)
						VALUES(?, ?, ?)
						ON DUPLICATE KEY UPDATE value = ?;',
						array($module, $name, $value, $value));
	}

}

?>