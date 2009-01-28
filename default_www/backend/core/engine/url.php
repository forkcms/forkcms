<?php

/**
 * BackendURL
 *
 * This class will handle the incomming url.
 *
 * @package		backend
 * @subpackage	url
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
	private function getQueryString()
	{
		return (string) $this->queryString;
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

		// fix GET-parameters
		$aGetChunks = explode('?', $queryString);

		// are there GET-parameters
		if(isset($aGetChunks[1]))
		{
			// remove from querystring
			$queryString = str_replace('?'. $aGetChunks[1], '', $this->getQueryString());

			// get key-value pairs
			$aGet = explode('&', $aGetChunks[1]);

			// loop pairs
			foreach ($aGet as $get)
			{
				// get key and value
				$aGetChunks = explode('=', $get, 2);

				// store in the real GET
				if(isset($aGetChunks[0])) $_GET[$aGetChunks[0]] =  (isset($aGetChunks[1])) ? (string) $aGetChunks[1] : '';
			}
		}

		// split into chunks, a Fork CMS url will always look like /<lang>/<module>/<action>(?GET)
		$aChunks = (array) explode('/', trim($queryString, '/'));

		// get the language, this will always be in front
		$language = (isset($aChunks[1]) && $aChunks[1] != '') ? SpoonFilter::getValue($aChunks[1], FrontendLanguage::getActiveLanguages(), FrontendLanguage::DEFAULT_LANGUAGE) : FrontendLanguage::DEFAULT_LANGUAGE;

		// get the module, null will be the default
		$module = (isset($aChunks[2]) && $aChunks[2] != '') ? $aChunks[2] : 'dashboard';

		// get the requested action, index will be our default action
		$action = (isset($aChunks[3]) && $aChunks[3] != '') ? $aChunks[3] : 'index';

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

					// @todo set user prefered interface language
					BackendLanguage::setLocale('nl');

					// set current module
					$this->setModule($module);
					$this->setAction($action);
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
	private function setModule($module)
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