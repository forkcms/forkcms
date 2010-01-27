<?php

/**
 * BackendURL
 *
 * This class will handle the incomming url.
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
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
		Spoon::setObjectReference('url', $this);

		// set query-string for later use
		$this->setQueryString($_SERVER['REQUEST_URI']);

		// set host for later use
		$this->setHost($_SERVER['HTTP_HOST']);

		// process url
		$this->processQueryString();
	}


	/**
	 * Get the current action found in the url
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
	 * Get the current module found in the url
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
	private function processQueryString($useGet = false)
	{
		// store the querystring local, so we don't alter it.
		$queryString = $this->getQueryString();

		// fix GET-parameters
		$getChunksFromUrl = explode('?', $queryString);

		// are there GET-parameters
		if(isset($getChunksFromUrl[1]))
		{
			// remove from querystring
			$queryString = str_replace('?'. $getChunksFromUrl[1], '', $this->getQueryString());

			// get key-value pairs
			$get = explode('&', $getChunksFromUrl[1]);

			// loop pairs
			foreach($get as $getParameter)
			{
				// get key and value
				$getChunks = explode('=', $getParameter, 2);

				// store in the real GET
				if(isset($getChunks[0])) $_GET[$getChunks[0]] =  (isset($getChunks[1])) ? (string) $getChunks[1] : '';
			}
		}

		// split into chunks, a Fork CMS url will always look like /<lang>/<module>/<action>(?GET)
		$chunks = (array) explode('/', trim($queryString, '/'));

		// get the language, this will always be in front
		$language = (isset($chunks[1]) && $chunks[1] != '') ? SpoonFilter::getValue($chunks[1], FrontendLanguage::getActiveLanguages(), FrontendLanguage::DEFAULT_LANGUAGE) : FrontendLanguage::DEFAULT_LANGUAGE;

		// get the module, null will be the default
		$module = (isset($chunks[2]) && $chunks[2] != '') ? $chunks[2] : 'dashboard';

		// get the requested action, index will be our default action
		$action = (isset($chunks[3]) && $chunks[3] != '') ? $chunks[3] : 'index';

		// check if this is a request for a JS-file
		$isJS = (isset($chunks[1]) && $chunks[1] == 'js.php');

		// if it is an request for a JS-file we only need the module
		if($isJS)
		{
			// set the working language, this is not the interface language
			BackendLanguage::setWorkingLanguage(SpoonFilter::getGetValue('language', null, FrontendLanguage::DEFAULT_LANGUAGE));

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
				SpoonHTTP::redirect('/'. NAMED_APPLICATION .'/'. $language .'/authentication/?querystring=/'. $this->getQueryString());
			}

			// the person is logged in
			else
			{
				// does our user has access to this module?
				if(!BackendAuthentication::isAllowedModule($module))
				{
					// the user doesn't have access, redirect to error page
					SpoonHTTP::redirect('/'. NAMED_APPLICATION .'/'. $language .'/error?type=not-allowed-module&querystring='. urlencode($this->queryString));
				}

				// we have access
				else
				{
					// can our user execute the requested action?
					if(!BackendAuthentication::isAllowedAction($action, $module))
					{
						// the user hasn't access, redirect to error page
						SpoonHTTP::redirect('/'. NAMED_APPLICATION .'/'. $language .'/error?type=not-allowed-action&querystring='. urlencode($this->queryString));
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
							BackendLanguage::setLocale(BackendAuthentication::getUser()->getSetting('backend_interface_language', 'nl'));
						}

						// no authenticated user
						else
						{
							// init var
							$interfaceLanguage = 'nl';

							// override with cookie value if that exists
							if(SpoonCookie::exists('backend_interface_language')) $interfaceLanguage = SpoonCookie::get('backend_interface_language');

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
	 * @param	string $action
	 */
	private function setAction($action)
	{
		$this->action = (string) $action;
	}


	/**
	 * Set the host
	 *
	 * @return	void
	 * @param	string $host
	 */
	private function setHost($host)
	{
		$this->host = (string) $host;
	}


	/**
	 * Set the current module
	 *
	 * @return	void
	 * @param	string $module
	 */
	public function setModule($module)
	{
		$this->module = (string) $module;
	}


	/**
	 * Set the querystring
	 *
	 * @return	void
	 * @param	string $queryString
	 */
	private function setQueryString($queryString)
	{
		$this->queryString = trim((string) $queryString, '/');
	}
}

?>