<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

require_once FRONTEND_CORE_PATH . '/engine/language.php';

/**
 * This class will handle the sitemap for Fork. It will dynamicly create a sitemap
 *
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class FrontendSitemap
{
	/**
	 * The active language
	 *
	 * @var string
	 */
	protected $activeLanguage;

	/**
	 * The meta data that will be used to process the xml
	 *
	 * @var array
	 */
	protected $metaData = array();

	/**
	 * The sitemap data from the url
	 *
	 * @var string
	 */
	protected $sitemapAction, $sitemapType, $sitemapUrl;

	/**
	 * The sitemap data from the url
	 *
	 * @var int
	 */
	protected $sitemapPage = null;

	/**
	 * The sitemap page limit
	 *
	 * @var int
	 */
	protected $pageLimit = 10, $numPages = 1;

	/**
	 * The url data
	 *
	 * @var array
	 */
	protected $urlData = array();

	/**
	 * Start the sitemap
	 *
	 * @param string $sitemapUrl
	 */
	public function __construct($sitemapUrl)
	{
		$this->filterData($sitemapUrl);
		$this->loadData();
	}

	/**
	 * Converts an array to an xml string
	 *
	 * @param array $xmlData
	 * @param int $tab How much tabs do we need at this point?
	 * @return string
	 */
	protected function arrayToXml(array $xmlData, $tab = 0)
	{
		$returnString = '';

		// go trough the elements to parse them into an xml node
		foreach($xmlData as $nodeName => $nodeData)
		{
			// this should be cleaned up with an array match, jadajada
			if(is_int($nodeName)) $returnString .= $this->arrayToXml($nodeData);
			else
			{
				// add tabs
				for($i = 0; $i <= $tab; $i++) $returnString .= "\t";

				// start of the new node
				$returnString .= '<' . $nodeName . '>';

				// the node data
				if(is_array($nodeData)) $returnString .= $this->arrayToXml($nodeData, $tab + 1);
				else $returnString .= $nodeData;

				// end of the new node
				$returnString .= '</' . $nodeName . '>' . "\n";
			}
		}

		return $returnString;
	}

	/**
	 * Filter the data
	 *
	 * @param string $sitemapUrl
	 */
	protected function filterData($sitemapUrl)
	{
		// store the url
		$this->sitemapUrl = (string) $sitemapUrl;

		// seperate the url data
		$url = str_replace('.xml', '', $this->sitemapUrl);
		$this->urlData = explode('sitemap', $url);

		// set the default active language
		$this->activeLanguage = FrontendModel::getModuleSetting('core', 'default_language');

		// set the sitemap data
		if(isset($this->urlData[0]) && $this->urlData[0] != '')
		{
			// check if we have a language specified
			$prefixChunks = explode('-', $this->urlData[0]);
			$activeLanguages = FL::getActiveLanguages();

			// we have selected a language
			if(count($prefixChunks) > 1)
			{
				$action = $prefixChunks[1];

				// set the active language
				if(in_array($prefixChunks[0], $activeLanguages)) $this->activeLanguage = $prefixChunks[0];
				else throw new Exception('This(' . $prefixChunks[0] . ') is an invalid language');
			}
			else $action = $prefixChunks[0];
			$this->sitemapAction = $action;
		}

		if(isset($this->urlData[1]))
		{
			// load the pagination data
			$this->loadPagination($this->sitemapAction);

			if($this->urlData[1] != '')
			{
				// get the current page
				$page = (int) ltrim($this->urlData[1], '-');
				if($page > 0) $page--;
				if($page > $this->numPages) $page = $this->numPages;
				$this->sitemapPage = $page;
			}
		}
	}

	/**
	 * Fetch the last modification date for a range of items
	 *
	 * @param int $limit
	 * @param int $offset
	 * @return string
	 */
	protected function getLastModificationDate($limit, $offset)
	{
		$data = (array) FrontendModel::getDB()->getPairs(
			'SELECT s.id, UNIX_TIMESTAMP(s.edited_on)
			 FROM meta_sitemap AS s
			 WHERE s.visible = ?
			 LIMIT ?, ?',
			array('Y', (int) $offset, (int) $limit)
		);

		// get the latest modification
		$lastModDate = 0;
		foreach($data as $sitemap) if($sitemap > $lastModDate) $lastModDate = (int) $sitemap;

		return FrontendModel::getUTCDate('Y-m-d\TH:i:sP', $lastModDate);
	}

	/**
	 * Fetch the meta data
	 *
	 * @param int[optional] $limit
	 * @param int[optional] $offset
	 * @return array
	 */
	protected function getMetaData($limit = 200, $offset = 0)
	{
		$data = (array) FrontendModel::getDB()->getRecords(
			'SELECT s.*, UNIX_TIMESTAMP(s.edited_on) AS edited_on
			 FROM meta_sitemap AS s
			 WHERE s.visible = ? AND s.language = ?
			 LIMIT ?, ?',
			array('Y', $this->activeLanguage, (int) $offset, (int) $limit)
		);

		// go trough the data to assign the url
		foreach($data as $key => $sitemap)
		{
			$language = $sitemap['language'];
			$module = $sitemap['module'];
			$action = $sitemap['action'];

			// load the locale for the current language
			FL::setLocale($language);

			// check if the module  has the sitemap function
			$callbackClass = 'Frontend' . SpoonFilter::toCamelCase($module) . 'Model';
			if(is_callable(array($callbackClass, 'sitemap')))
			{
				$data[$key] = call_user_func(array($callbackClass, 'sitemap'), $sitemap, $language);
			}
			else
			{
				$baseUrl = SITE_URL . FrontendNavigation::getURLForBlock($module, $action, $language);
				$data[$key]['full_url'] =  $baseUrl . '/' . $sitemap['url'];
			}

			$data[$key]['edited_on'] = FrontendModel::getUTCDate('Y-m-d\TH:i:sP', $sitemap['edited_on']);
		}
		return $data;
	}

	/**
	 * Count the number of records
	 *
	 * @return int
	 */
	protected function getMetaDataCount()
	{
		return (int) FrontendModel::getDB()->getNumRows(
			'SELECT s.id
			 FROM meta_sitemap AS s
			 WHERE s.visible = ?',
			array('Y')
		);
	}

	/**
	 * This function will fetch all the meta data that is used to generate a sitemap.
	 */
	protected function loadData()
	{
		// load the data according to the provided action
		switch($this->sitemapAction)
		{
			case 'page':
				// show the page items
				$this->metaData = $this->getMetaData($this->pageLimit, $this->sitemapPage);
			break;
			case '':
				// do nothing
			break;
			default:
				SpoonHTTP::redirect(FrontendNavigation::getURL(404, $this->activeLanguage));
			break;
		}
	}

	/**
	 * Load the pagination
	 *
	 * @param string $action
	 */
	protected function loadPagination($action)
	{
		$action = strtolower((string) $action);
		switch($action)
		{
			case 'page':
				// get the page limit for the pages sitemap
				$this->pageLimit = FrontendModel::getModuleSetting('pages', 'sitemap_pages_items', 100);
			break;
			default:
				// do nothing
			break;
		}

		// set the number of pages
		$this->numPages = ceil($this->getMetaDataCount() / $this->pageLimit);
	}

	/**
	 * Parse the sitemap contents and ouptut is as an xml file
	 */
	public function parse()
	{
		// the search engines expect a xml file, so act like one
		SpoonHTTP::setHeaders(array('Content-Type: application/xml'));

		// get the parsed data to show
		$parsedData = '';
		$this->sitemapType = ($this->sitemapAction === null) ? 'sitemapindex' : 'urlset';
		if($this->sitemapAction == 'page') $parsedData = $this->parsePage();
		if($this->sitemapAction === null) $parsedData = $this->parseIndex();

		// build the output
		$output = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<' . $this->sitemapType . ' xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$output .= $this->arrayToXml($parsedData);
		$output .= '</' . $this->sitemapType . '>';
		echo $output;
	}

	/**
	 * Parse the index
	 *
	 * @return string
	 */
	public function parseIndex()
	{
		$output = array();

		// build the pages sitemap
		if(SITE_MULTILANGUAGE)
		{
			foreach(FL::getActiveLanguages() as $language)
			{
				$output[]['sitemap'] = array(
					'loc' => SITE_URL . '/' . $language . '-pagesitemap.xml',
					'lastmod' => $this->getLastModificationDate($this->numPages, 0)
				);
			}
		}
		else
		{
			$output[]['sitemap'] = array(
				'loc' => SITE_URL . '/' . $this->activeLanguage . '-pagesitemap.xml',
				'lastmod' => $this->getLastModificationDate($this->numPages, 0)
			);
		}

		return $output;
	}

	/**
	 * Parse the page data
	 *
	 * @return string
	 */
	public function parsePage()
	{
		$output = array();

		// if we exceed the maximum, we should show some sort of pagination
		if($this->numPages > 1 && $this->sitemapPage === null)
		{
			$this->sitemapType = 'sitemapindex';

			// build the number of sitemaps equal to the number of pages
			for($i = 1; $i <= $this->numPages; $i++)
			{
				$output[]['sitemap'] = array(
					'loc' => SITE_URL . '/' . $this->activeLanguage . '-pagesitemap-' . $i . '.xml',
					'lastmod' => $this->getLastModificationDate($this->pageLimit, $i - 1)
				);
			}
		}
		else
		{
			// parse all the elements into a decent array
			foreach($this->metaData as $page)
			{
				$output[]['url'] = array(
					'loc' => $page['full_url'],
					'lastmod' => $page['edited_on'],
					'changefreq' => $page['change_frequency'],
					'priority' => $page['priority']
				);
			}
		}
		return $output;
	}
}
