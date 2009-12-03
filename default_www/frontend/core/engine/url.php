<?php

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package			frontend
 * @subpackage		url
 *
 * @author 			Tijs Verkoyen <tijs@netlash.com>
 * @since			2.0
 */
class FrontendURL
{
	/**
	 * The pages
	 *
	 * @var	array
	 */
	private $pages = array();


	/**
	 * The parameters
	 *
	 * @var	array
	 */
	private $parameters = array();


	/**
	 * The host, will be used for cookies
	 *
	 * @var	string
	 */
	private $host;


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
		// set query-string
		$this->setQueryString($_SERVER['REQUEST_URI']);

		// set host
		$this->setHost($_SERVER['HTTP_HOST']);

		// process url
		$this->processQueryString();
	}


	/**
	 * Get the domain
	 *
	 * @return	string
	 */
	public function getDomain()
	{
		// get host
		$host = $this->getHost();

		// replace
		return str_replace('www.', '', $host);
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
	 * Get a page specified by the given index
	 *
	 * @return	mixed
	 * @param	int $index
	 */
	public function getPage($index)
	{
		// redefine
		$index = (int) $index;

		// does the index exists
		if(isset($this->pages[$index])) return $this->pages[$index];

		// fallback
		return null;
	}


	/**
	 * Return all the pages
	 *
	 * @return	array
	 */
	public function getPages()
	{
		return $this->pages;
	}


	/**
	 * Get a parameter specified by the given index
	 *
	 * @return	mixed
	 * @param	int $index
	 */
	public function getParameter($index)
	{
		// redefine
		$index = (int) $index;

		// does the index exists
		if(isset($this->parameters[$index])) return $this->parameters[$index];

		// fallback
		return null;
	}


	/**
	 * Return all the parameters
	 *
	 * @return	array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}


	/**
	 * Get the querystring
	 *
	 * @return	string
	 */
	private function getQueryString()
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

		// fix GET-parameters
		$getChunks = explode('?', $queryString);

		// are there GET-parameters
		if(isset($getChunks[1]))
		{
			// get key-value pairs
			$get = explode('&', $getChunks[1]);

			// remove from querystring
			$queryString = str_replace('?'. $getChunks[1], '', $this->getQueryString());

			// loop pairs
			foreach($get as $getItem)
			{
				// get key and value
				$getChunks = explode('=', $getItem, 2);

				// set get
				if(isset($getChunks[0])) $_GET[$getChunks[0]] =  (isset($getChunks[1])) ? (string) $getChunks[1] : '';
			}
		}

		// split into chunks
		$chunks = (array) explode('/', $queryString);

		// single language
		if(!SITE_MULTILANGUAGE)
		{
			// set language id
			$language = FrontendLanguage::DEFAULT_LANGUAGE;
		}

		// multiple languages
		else
		{
			// default value
			$mustRedirect = false;

			// get possible languages
			$possibleLanguages = (array) FrontendLanguage::getActiveLanguages();
			$redirectLanguages = (array) FrontendLanguage::getRedirectLanguages();

			// the language is present in the url
			if(isset($chunks[0]) && in_array($chunks[0], $possibleLanguages))
			{
				// define language
				$language = (string) $chunks[0];

				// try to set a cookie with the language
				try
				{
					// set cookie
					SpoonCookie::set('frontend_language', $language, (7 * 24 * 60 * 60), '/', '.'. $this->getDomain());
				}

				// fetch failed cookie
				catch (Exception $e)
				{
					if(substr_count($e->getMessage(), 'could not be set.') == 0) throw $e; // @todo moet aangepast worden naar numerieke exception.
				}

				// set sessions
				SpoonSession::set('frontend_language', $language);

				// remove the language part
				array_shift($chunks);
			}

			// language set in the cookie
			elseif(SpoonCookie::exists('frontend_language') && in_array(SpoonCookie::get('frontend_language'), $redirectLanguages))
			{
				// set languageId
				$language = (string) SpoonCookie::get('frontend_language');

				// redirect is needed
				$mustRedirect = true;
			}

			// default browser language
			else
			{
				// set languageId & abbreviation
				$language = FrontendLanguage::getBrowserLanguage();

				// try to set a cookie with the language
				try
				{
					// set cookie
					SpoonCookie::set('frontend_language', $language, (7 * 24 * 60 * 60), '/', '.'. $this->getDomain());
				}

				// fetch failed cookie
				catch (Exception $e)
				{
					if(substr_count($e->getMessage(), 'could not be set.') == 0) throw $e; // @todo moet aangepast worden naar numerieke exception
				}

				// redirect is needed
				$mustRedirect = true;
			}

			// redirect is required
			if($mustRedirect)
			{
				// build url
				$url = '/'. $language .'/'. $this->getQueryString();

				// set header & redirect
				SpoonHTTP::redirect($url, 301);
			}
		}

		// define the language
		define('FRONTEND_LANGUAGE', $language);

		// sets the localefile
		FrontendLanguage::setLocale($language);

		// list of pageIds & their full url
		$keys = FrontendNavigation::getKeys();

		// full url
		$url = implode('/', $chunks);
		$startURL = $url;

		// loop until we find the url in the list of pages
		while(!in_array($url, $keys))
		{
			// remove the last chunk
			array_pop($chunks);

			// redefine the url
			$url = implode('/', $chunks);
		}

		// remove language from querystring
		$queryString = trim(substr($queryString, strlen($language)), '/');

		// if it's the homepage AND parameters were given (not allowed!)
		if($url == '' && $queryString != '') SpoonHTTP::redirect(FrontendNavigation::getUrlByPageId(404), 404);

		// set pages
		$pages = trim($url, '/');

		// currently not in the homepage
		if($pages != '')
		{
			$pages = explode('/', $url);
			$this->setPages($pages);
		}

		// set parameters
		$parameters = trim(substr($startURL, strlen($url)), '/');

		// has at least one parameter
		if($parameters != '')
		{
			$parameters = explode('/', $parameters);
			$this->setParameters($parameters);
		}

		return; // @todo hieronder nog controleren.

		// structural array
		$navigation = FrontendNavigation::getNavigation();

		// pageId, parentId & depth
		$pageId = FrontendNavigation::getPageIdByUrl(implode('/', $this->getPages()));
		$parentId = FrontendNavigation::getParentIdByUrl(implode('/', $this->getPages()));
		$depth = ($parentId < 0) ? $parentId : count($this->getPages());

		// depth 0 doesn't exists
		if($depth == 0) $depth = 1;

		// this page has no extra linked, but parameters were still given => 404!
		if($aNavigation[$depth][$parentId][$pageId]['extra_id'] == 0 && !empty($this->aParameters))
		{
			SpoonHTTP::redirect(FrontendNavigation::getUrlByPageId(404), 404);
		}
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
	 * Set the pages
	 *
	 * @return	void
	 * @param	array[optional] $pages
	 */
	private function setPages(array $pages = array())
	{
		$this->pages = (array) $pages;
	}


	/**
	 * Set the parameters
	 *
	 * @return	void
	 * @param	array[optional] $parameters
	 */
	private function setParameters(array $parameters = array())
	{
		$this->parameters = (array) $parameters;
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