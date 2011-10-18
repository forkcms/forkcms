<?php

/**
 * Step 7 of the Fork installer
 *
 * @package		install
 * @subpackage	installer
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class InstallerStep7 extends InstallerStep
{
	/**
	 * Database connection, needed for installation
	 *
	 * @var	SpoonDatabases
	 */
	private $db;


	/**
	 * Build the language files
	 *
	 * @return	void
	 * @param	SpoonDatabase $db		The database connection instance.
	 * @param	string $language		The language to build the locale-file for.
	 * @param	string $application		The application to build the locale-file for.
	 */
	public function buildCache(SpoonDatabase $db, $language, $application)
	{
		// get types
		$types = $db->getEnumValues('locale', 'type');

		// get locale for backend
		$locale = (array) $db->getRecords('SELECT type, module, name, value
											FROM locale
											WHERE language = ? AND application = ?
											ORDER BY type ASC, name ASC, module ASC',
											array((string) $language, (string) $application));

		// start generating PHP
		$value = '<?php' . "\n";
		$value .= '/**' . "\n";
		$value .= ' *' . "\n";
		$value .= ' * This file is generated by the Installer, it contains' . "\n";
		$value .= ' * more information about the locale. Do NOT edit.' . "\n";
		$value .= ' * ' . "\n";
		$value .= ' * @author		Installer' . "\n";
		$value .= ' * @generated	' . date('Y-m-d H:i:s') . "\n";
		$value .= ' */' . "\n";
		$value .= "\n";

		// loop types
		foreach($types as $type)
		{
			// default module
			$modules = array('core');

			// continue output
			$value .= "\n";
			$value .= '// init var' . "\n";
			$value .= '$' . $type . ' = array();' . "\n";
			$value .= '$' . $type . '[\'core\'] = array();' . "\n";

			// loop locale
			foreach($locale as $i => $item)
			{
				// types match
				if($item['type'] == $type)
				{
					// new module
					if(!in_array($item['module'], $modules))
					{
						$value .= '$' . $type . '[\'' . $item['module'] . '\'] = array();' . "\n";
						$modules[] = $item['module'];
					}

					// parse
					if($application == 'backend') $value .= '$' . $type . '[\'' . $item['module'] . '\'][\'' . $item['name'] . '\'] = \'' . str_replace('\"', '"', addslashes($item['value'])) . '\';' . "\n";
					else $value .= '$' . $type . '[\'' . $item['name'] . '\'] = \'' . str_replace('\"', '"', addslashes($item['value'])) . '\';' . "\n";

					// unset
					unset($locale[$i]);
				}
			}
		}

		// close php
		$value .= "\n";
		$value .= '?>';

		// store
		SpoonFile::setContent(PATH_WWW . '/' . $application . '/cache/locale/' . $language . '.php', $value);
	}


	/**
	 * Creates the configuration files
	 *
	 * @return	void
	 */
	private function createConfigurationFiles()
	{
		// build variables
		$variables = array();
		$variables['<spoon-debug-email>'] = SpoonSession::get('email');
		$variables['<database-name>'] = SpoonSession::get('db_database');
		$variables['<database-hostname>'] = addslashes(SpoonSession::get('db_hostname'));
		$variables['<database-username>'] = addslashes(SpoonSession::get('db_username'));
		$variables['<database-password>'] = addslashes(SpoonSession::get('db_password'));
		$variables['<database-port>'] = (SpoonSession::exists('db_port') && SpoonSession::get('db_port') != '') ? addslashes(SpoonSession::get('db_port')) : 3306;
		$variables['<site-protocol>'] = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? 'http' : 'https';
		$variables['<site-domain>'] = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : 'forkcms.local';
		$variables['<site-relative-url>'] = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');
		$variables['<site-default-title>'] = 'Fork CMS';
		$variables['\'<site-multilanguage>\''] = SpoonSession::get('multiple_languages') ? 'true' : 'false';
		$variables['<path-www>'] = PATH_WWW;
		$variables['<path-library>'] = PATH_LIBRARY;
		$variables['<site-default-language>'] = SpoonSession::get('default_language');
		$variables['<action-group-tag>'] = '@actiongroup';
		$variables['<action-rights-level>'] = 7;

		// globals files
		$configurationFiles = array('globals.base.php' => 'globals.php',
									'globals_frontend.base.php' => 'globals_frontend.php',
									'globals_backend.base.php' => 'globals_backend.php');

		// loop files
		foreach($configurationFiles as $sourceFilename => $destinationFilename)
		{
			// grab content
			$globalsContent = SpoonFile::getContent(PATH_LIBRARY . '/' . $sourceFilename);

			// assign the variables
			$globalsContent = str_replace(array_keys($variables), array_values($variables), $globalsContent);

			// write the file
			SpoonFile::setContent(PATH_LIBRARY . '/' . $destinationFilename, $globalsContent);
		}

		// general configuration file
		$globalsContent = SpoonFile::getContent(PATH_LIBRARY . '/config.base.php');

		// assign the variables
		$globalsContent = str_replace(array_keys($variables), array_values($variables), $globalsContent);

		// write the file
		SpoonFile::setContent(PATH_WWW . '/backend/cache/config/config.php', $globalsContent);
		SpoonFile::setContent(PATH_WWW . '/frontend/cache/config/config.php', $globalsContent);
	}


	/**
	 * Create locale cache files
	 *
	 * @return	void
	 */
	private function createLocaleFiles()
	{
		// all available languages
		$languages = array_unique(array_merge(SpoonSession::get('languages'), SpoonSession::get('interface_languages')));

		// loop all the languages
		foreach($languages as $language)
		{
			// get applications
			$applications = $this->db->getColumn('SELECT DISTINCT application
													FROM locale
													WHERE language = ?',
													array((string) $language));

			// loop applications
			foreach((array) $applications as $application)
			{
				// build application locale cache
				$this->buildCache($this->db, $language, $application);
			}
		}
	}


	/**
	 * Delete the cached data
	 *
	 * @return	void
	 */
	private function deleteCachedData()
	{
		// init some vars
		$foldersToLoop = array('/backend/cache', '/frontend/cache');
		$foldersToIgnore = array('/backend/cache/navigation');
		$filesToIgnore = array('.gitignore');
		$filesToDelete = array();

		// loop folders
		foreach($foldersToLoop as $folder)
		{
			// get folderlisting
			$subfolders = (array) SpoonDirectory::getList(PATH_WWW . $folder, false, array('.svn', '.gitignore'));

			// loop folders
			foreach($subfolders as $subfolder)
			{
				// not in ignore list?
				if(!in_array($folder . '/' . $subfolder, $foldersToIgnore))
				{
					// get the filelisting
					$files = (array) SpoonFile::getList(PATH_WWW . $folder . '/' . $subfolder);

					// loop the files
					foreach($files as $file)
					{
						if(!in_array($file, $filesToIgnore))
						{
							$filesToDelete[] = PATH_WWW . $folder . '/' . $subfolder . '/' . $file;
						}
					}
				}
			}
		}

		// delete cached files
		if(!empty($filesToDelete))
		{
			// loop files and delete them
			foreach($filesToDelete as $file) SpoonFile::delete($file);
		}
	}


	/**
	 * Executes this step.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// validate all previous steps
		if(!$this->validateForm()) SpoonHTTP::redirect('index.php?step=1');

		// delete cached data
		$this->deleteCachedData();

		// create configuration files
		$this->createConfigurationFiles();

		// install modules
		$this->installModules();

		// create locale cache
		$this->createLocaleFiles();

		// already installed
		SpoonFile::setContent(dirname(__FILE__) . '/../cache/installed.txt', date('Y-m-d H:i:s'));

		// show success message
		$this->showSuccess();

		// clear session
		SpoonSession::destroy();

		// show output
		$this->tpl->display('layout/templates/step_7.tpl');
	}


	/**
	 * Installs the required and optional modules
	 *
	 * @return	void
	 */
	private function installModules()
	{
		// get port
		$port = (SpoonSession::exists('db_port') && SpoonSession::get('db_port') != '') ? SpoonSession::get('db_port') : 3306;

		// database instance
		$this->db = new SpoonDatabase('mysql', SpoonSession::get('db_hostname'), SpoonSession::get('db_username'), SpoonSession::get('db_password'), SpoonSession::get('db_database'), $port);

		// utf8 compliance & MySQL-timezone
		$this->db->execute('SET CHARACTER SET utf8, NAMES utf8, time_zone = "+0:00"');

		/**
		 * First we need to install the core. All the linked modules, settings and sql tables are
		 * being installed.
		 */
		require_once PATH_WWW . '/backend/core/installer/install.php';

		// install the core
		$install = new CoreInstall($this->db,
									SpoonSession::get('languages'),
									SpoonSession::get('interface_languages'),
									SpoonSession::get('example_data'),
									array('default_language' => SpoonSession::get('default_language'),
											'default_interface_language' => SpoonSession::get('default_interface_language'),
											'spoon_debug_email' => SpoonSession::get('email'),
											'api_email' => SpoonSession::get('email'),
											'site_domain' => (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : 'fork.local',
											'site_title' => 'Fork CMS',
											'smtp_server' => '',
											'smtp_port' => '',
											'smtp_username' => '',
											'smtp_password' => ''));

		// variables passed to module installers
		$variables = array();
		$variables['email'] = SpoonSession::get('email');
		$variables['default_interface_language'] = SpoonSession::get('default_interface_language');

		// loop required modules
		foreach($this->modules['required'] as $module)
		{
			// install exists
			if(SpoonFile::exists(PATH_WWW . '/backend/modules/' . $module . '/installer/install.php'))
			{
				// users module needs custom variables
				if($module == 'users')
				{
					$variables['password'] = SpoonSession::get('password');
				}

				// load file
				require_once PATH_WWW . '/backend/modules/' . $module . '/installer/install.php';

				// class name
				$class = SpoonFilter::toCamelCase($module) . 'Install';

				// execute installer
				$install = new $class($this->db, SpoonSession::get('languages'), SpoonSession::get('interface_languages'), SpoonSession::get('example_data'), $variables);
			}
		}

		// optional modules
		foreach(SpoonSession::get('modules') as $module)
		{
			if(!in_array($module, $this->modules['required']))
			{
				// install exists
				if(SpoonFile::exists(PATH_WWW . '/backend/modules/' . $module . '/installer/install.php'))
				{
					// load file
					require_once PATH_WWW . '/backend/modules/' . $module . '/installer/install.php';

					// class name
					$class = SpoonFilter::toCamelCase($module) . 'Install';

					// execute installer
					$install = new $class($this->db, SpoonSession::get('languages'), SpoonSession::get('interface_languages'), SpoonSession::get('example_data'), $variables);
				}
			}
		}
	}


	/**
	 * Is this step allowed.
	 *
	 * @return	bool
	 */
	public static function isAllowed()
	{
		return InstallerStep6::isAllowed() &&
				isset($_SESSION['email']) &&
				isset($_SESSION['password']);
	}


	/**
	 * Show the success message
	 *
	 * @return	void
	 */
	private function showSuccess()
	{
		// assign variables
		$this->tpl->assign('url', (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : 'fork.local');
		$this->tpl->assign('email', SpoonSession::get('email'));
		$this->tpl->assign('password', SpoonSession::get('password'));
	}


	/**
	 * Validates the previous steps
	 */
	private function validateForm()
	{
		return (InstallerStep2::isAllowed() && InstallerStep3::isAllowed() && InstallerStep4::isAllowed() && InstallerStep5::isAllowed());
	}
}

?>