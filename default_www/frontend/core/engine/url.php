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
class FrontendUrl
{
	/**
	 * The pages
	 *
	 * @var	array
	 */
	private $aPages = array();


	/**
	 * The parameters
	 *
	 * @var	array
	 */
	private $aParameters = array();


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
		if(isset($this->aPages[$index])) return $this->aPages[$index];

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
		return $this->aPages;
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
		if(isset($this->aParameters[$index])) return $this->aParameters[$index];

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
		return $this->aParameters;
	}


	/**
	 * Get the querystring
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
			// get key-value pairs
			$aGet = explode('&', $aGetChunks[1]);

			// remove from querystring
			$queryString = str_replace('?'. $aGetChunks[1], '', $this->getQueryString());

			// loop pairs
			foreach ($aGet as $get)
			{
				// get key and value
				$aGetChunks = explode('=', $get, 2);

				// set get
				if(isset($aGetChunks[0])) $_GET[$aGetChunks[0]] =  (isset($aGetChunks[1])) ? (string) $aGetChunks[1] : '';
			}
		}

		// split into chunks
		$aChunks = (array) explode('/', $queryString);

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
			$aPossibleLanguages = (array) FrontendLanguage::getActiveLanguages();
			$aRedirectLanguages = (array) FrontendLanguage::getRedirectLanguages();

			// the language is present in the url
			if(isset($aChunks[0]) && in_array($aChunks[0], $aPossibleLanguages))
			{
				$language = (string) $aChunks[0];

				// set cookie
				SpoonCookie::set('frontend_language', $language, (7 * 24 * 60 * 60), '/', '.'. $this->getDomain());

				// set sessions
				SpoonSession::set('frontend_language', $language);

				// remove the language part
				array_shift($aChunks);
			}

			// language set in the cookie
			elseif(SpoonCookie::exists('frontend_language') && in_array(SpoonCookie::get('frontend_language'), $aRedirectLanguages))
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

				// set cookie
				SpoonCookie::set('frontend_language', $language, (7 * 24 * 60 * 60), '/', '.'. $this->getDomain());

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
		$aKeys = FrontendNavigation::getKeys();

		// full url
		$url = implode('/', $aChunks);
		$startUrl = $url;

		// loop until we find the url in the list of pages
		while(!in_array($url, $aKeys))
		{
			// remove the last chunk
			array_pop($aChunks);

			// redefine the url
			$url = implode('/', $aChunks);
		}

		// remove language from querystring
		$queryString = trim(str_replace($language, '', $queryString), '/');

		// if it's the homepage AND parameters were given (not allowed!)
		if($url == '' && $queryString != '') SpoonHTTP::redirect(FrontendNavigation::getUrlByPageId(404), 404);

		// set pages
		$pages = trim($url, '/');
		if($pages != '')
		{
			$pages = explode('/', $url);
			$this->setPages($pages);
		}

		// set parameters
		$parameters = trim(str_replace($url, '', $startUrl), '/');
		if($parameters != '')
		{
			$parameters = explode('/', $parameters);
			$this->setParameters($parameters);
		}

		// structural array
		$aNavigation = FrontendNavigation::getNavigation();

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
	 * @param	array[optional] $aPages
	 */
	private function setPages($aPages = array())
	{
		$this->aPages = (array) $aPages;
	}


	/**
	 * Set the parameters
	 *
	 * @return	void
	 * @param	array[optional] $aParameters
	 */
	private function setParameters($aParameters = array())
	{
		$this->aParameters = (array) $aParameters;
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