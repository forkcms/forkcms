<?php

/**
 * This class will handle the incoming URL.
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendURL
{
	/**
	 * The current action
	 *
	 * @var	string
	 */
	private $action;


	/**
	 * The host, will be used for cookies
	 *
	 * @var	string
	 */
	private $host;


	/**
	 * The current module
	 *
	 * @var	string
	 */
	private $module;


	/**
	 * The querystring
	 *
	 * @var	string
	 */
	private $queryString;


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// add ourself to the reference so other classes can retrieve us
		Spoon::set('url', $this);

		// set query-string for later use
		$this->setQueryString($_SERVER['REQUEST_URI']);

		// set host for later use
		$this->setHost($_SERVER['HTTP_HOST']);

		// process URL
		$this->processQueryString();
	}


	/**
	 * Get the current action found in the URL
	 *
	 * @return	string
	 */
	public function getAction()
	{
		return $this->action;
	}


	/**
	 * Get the host
	 *
	 * @return	string
	 */
	public function getHost()
	{
		return $this->host;
	}


	/**
	 * Get the current module found in the URL
	 *
	 * @return	string
	 */
	public function getModule()
	{
		return $this->module;
	}


	/**
	 * Get the full querystring
	 *
	 * @return	string
	 */
	public function getQueryString()
	{
		return $this->queryString;
	}


	/**
	 * Process the querystring
	 *
	 * @return	void
	 */
	private function processQueryString()
	{
		// store the querystring local, so we don't alter it.
		$queryString = $this->getQueryString();

		// find the position of ? (which seperates real URL and GET-parameters)
		$positionQuestionMark = strpos($queryString, '?');

		// remove the GET-chunk from the parameters
		$processedQueryString = ($positionQuestionMark === false) ? $queryString : substr($queryString, 0, $positionQuestionMark);

		// split into chunks, a Backend URL will always look like /<lang>/<module>/<action>(?GET)
		$chunks = (array) explode('/', trim($processedQueryString, '/'));

		// check if this is a request for a JS-file
		$isJS = (isset($chunks[1]) && $chunks[1] == 'js.php');

		// check if this is a request for a AJAX-file
		$isAJAX = (isset($chunks[1]) && $chunks[1] == 'ajax.php');

		// get the language, this will always be in front
		$language = (isset($chunks[1]) && $chunks[1] != '') ? SpoonFilter::getValue($chunks[1], BackendLanguage::getActiveLanguages(), '') : '';

		// no language provided?
		if($language == '' && !$isJS && !$isAJAX)
		{
			// remove first element
			array_shift($chunks);

			// redirect to login
			SpoonHTTP::redirect('/' . NAMED_APPLICATION . '/' . SITE_DEFAULT_LANGUAGE . '/' . implode('/', $chunks));
		}

		// get the module, null will be the default
		$module = (isset($chunks[2]) && $chunks[2] != '') ? $chunks[2] : 'dashboard';

		// get the requested action, if it is passed
		if(isset($chunks[3]) && $chunks[3] != '') $action = $chunks[3];

		// no action passed through URL
		elseif(!$isJS && !$isAJAX)
		{
			// build path to the module and define it. This is a constant because we can use this in templates.
			if(!defined('BACKEND_MODULE_PATH')) define('BACKEND_MODULE_PATH', BACKEND_MODULES_PATH . '/' . $module);

			// check if the config is present? If it isn't present there is a huge problem, so we will stop our code by throwing an error
			if(!SpoonFile::exists(BACKEND_MODULE_PATH . '/config.php')) throw new BackendException('The configfile for the module (' . $module . ') can\'t be found.');

			// build config-object-name
			$configClassName = 'Backend' . SpoonFilter::toCamelCase($module . '_config');

			// require the config file, we validated before for existence.
			require_once BACKEND_MODULE_PATH . '/config.php';

			// validate if class exists (aka has correct name)
			if(!class_exists($configClassName)) throw new BackendException('The config file is present, but the classname should be: ' . $configClassName . '.');

			// create config-object, the constructor will do some magic
			$config = new $configClassName($module);

			// set action
			$action = ($config->getDefaultAction() !== null) ? $config->getDefaultAction() : 'index';
		}

		// if it is an request for a JS-file or an AJAX-file we only need the module
		if($isJS || $isAJAX)
		{
			// set the working language, this is not the interface language
			BackendLanguage::setWorkingLanguage(SpoonFilter::getGetValue('language', null, SITE_DEFAULT_LANGUAGE));

			// set current module
			$this->setModule(SpoonFilter::getGetValue('module', null, null));

			// set action
			$this->setAction('index');
		}

		// regular request
		else
		{
			// the person isn't logged in? or the module doesn't require authentication
			if(!BackendAuthentication::isLoggedIn() && !BackendAuthentication::isAllowedModule($module))
			{
				// redirect to login
				SpoonHTTP::redirect('/' . NAMED_APPLICATION . '/' . $language . '/authentication/?querystring=' . urlencode('/' . $this->getQueryString()));
			}

			// the person is logged in
			else
			{
				// does our user has access to this module?
				if(!BackendAuthentication::isAllowedModule($module))
				{
					// the user doesn't have access, redirect to error page
					SpoonHTTP::redirect('/' . NAMED_APPLICATION . '/' . $language . '/error?type=module-not-allowed&querystring=' . urlencode('/' . $this->getQueryString()));
				}

				// we have access
				else
				{
					// can our user execute the requested action?
					if(!BackendAuthentication::isAllowedAction($action, $module))
					{
						// the user hasn't access, redirect to error page
						SpoonHTTP::redirect('/' . NAMED_APPLICATION . '/' . $language . '/error?type=action-not-allowed&querystring=' . urlencode('/' . $this->getQueryString()));
					}

					// let's do it
					else
					{
						// set the working language, this is not the interface language
						BackendLanguage::setWorkingLanguage($language);

						// is the user authenticated
						if(BackendAuthentication::getUser()->isAuthenticated())
						{
							// set interface language based on the user preferences
							BackendLanguage::setLocale(BackendAuthentication::getUser()->getSetting('interface_language', 'nl'));
						}

						// no authenticated user
						else
						{
							// init var
							$interfaceLanguage = BackendModel::getModuleSetting('core', 'default_interface_language');

							// override with cookie value if that exists
							if(SpoonCookie::exists('interface_language') && in_array(SpoonCookie::get('interface_language'), array_keys(BackendLanguage::getInterfaceLanguages())))
							{
								// set interface language based on the perons' cookies
								$interfaceLanguage = SpoonCookie::get('interface_language');
							}

							// set interface language
							BackendLanguage::setLocale($interfaceLanguage);
						}

						// set current module
						$this->setModule($module);
						$this->setAction($action);
					}
				}
			}
		}
	}


	/**
	 * Set the current action
	 *
	 * @return	void
	 * @param	string $action	The action to set.
	 */
	private function setAction($action)
	{
		$this->action = (string) $action;
	}


	/**
	 * Set the host
	 *
	 * @return	void
	 * @param	string $host	The host.
	 */
	private function setHost($host)
	{
		$this->host = (string) $host;
	}


	/**
	 * Set the current module
	 *
	 * @return	void
	 * @param	string $module	The module to set.
	 */
	public function setModule($module)
	{
		$this->module = (string) $module;
	}


	/**
	 * Set the querystring
	 *
	 * @return	void
	 * @param	string $queryString		The full query-string.
	 */
	private function setQueryString($queryString)
	{
		$this->queryString = trim((string) $queryString, '/');
	}
}

?>