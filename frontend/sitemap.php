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
		$this->sitemapUrl = $sitemapUrl;
		$this->loadData();
	}

	/**
	 * Filter the data
	 */
	protected function filterData()
	{
		// seperate the url data
		$url = str_replace('.xml', '', $this->sitemapUrl);
		$this->urlData = explode('sitemap', $url);

		// set the sitemap data
		$this->sitemapAction = (isset($this->urlData[0]) && $this->urlData[0] != '') ? $this->urlData[0] : null;

		if(isset($this->urlData[1]))
		{
			// load the pagination data
			$this->loadPagination($this->urlData[0]);

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
			'SELECT s.*
			 FROM meta_sitemap AS s
			 WHERE s.visible = ?
			 LIMIT ?, ?',
			array('Y', (int) $offset, (int) $limit)
		);

		// go trough the data to assign the url
		foreach($data as $key => $sitemap)
		{
			$language = $sitemap['language'];
			$module = $sitemap['module'];
			$action = $sitemap['action'];

			if($sitemap['module'] !== 'pages')
			{
				$baseUrl = SITE_URL . FrontendNavigation::getURLForBlock($module, $action, $language);
			}
			else $baseUrl = SITE_URL . '/' . $language;

			$data[$key]['full_url'] =  $baseUrl . '/' . $sitemap['url'];
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

		$this->filterData();

		switch($this->sitemapAction)
		{
			case 'page':
				$this->metaData = $this->getMetaData($this->pageLimit, $this->sitemapPage);
			break;
			case '':
				// do nothing
			break;
			default:
				SpoonHTTP::redirect(SITE_URL);
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
				$this->pageLimit = FrontendModel::getModuleSetting('pages', 'sitemap_pages_items', 100);
			break;
			default:
				// do nothing
			break;
		}
		$this->numPages = ceil($this->getMetaDataCount() / $this->pageLimit) - 1;
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
		if($this->sitemapAction == 'page') $parsedData .= $this->parsePage();
		if($this->sitemapAction === null) $parsedData .= $this->parseIndex();

		// build the output
		$output = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<' . $this->sitemapType . ' xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$output .= $parsedData;
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
		$output = '';

		/*
		 * Parse the pages sitemap
		 */
		$output .= "\t" . '<sitemap>';
		$output .= "\t\t" . '<loc>' . SITE_URL . '/pagesitemap.xml</loc>';
		$output .= "\t\t" . '<lastmod>' . $this->getLastModificationDate($this->numPages, 0) . '</lastmod>';
		$output .= "\t" . '</sitemap>';

		return $output;
	}

	/**
	 * Parse the page data
	 *
	 * @return string
	 */
	public function parsePage()
	{
		$output = '';

		// if we exceed the maximum, we should show some sort of pagination
		if($this->numPages > 0 && $this->sitemapPage === null)
		{
			$this->sitemapType = 'sitemapindex';

			for($i = 1; $i <= $this->numPages; $i++)
			{
				$output .= "\t" . '<sitemap>';
				$output .= "\t\t" . '<loc>' . SITE_URL . '/pagesitemap-' . $i . '.xml</loc>';
				$output .= "\t\t" . '<lastmod>' . $this->getLastModificationDate($this->pageLimit, $i - 1) . '</lastmod>';
				$output .= "\t" . '</sitemap>';
			}
		}
		else
		{
			foreach($this->metaData as $page)
			{
				$output .= "\t" . '<url>';
				$output .= "\t\t" . '<loc>' . $page['full_url'] . '</loc>';
				$output .= "\t\t" . '<lastmod>' . $page['edited_on'] . '</lastmod>';
				$output .= "\t\t" . '<changefreq>' . $page['change_frequency'] . '</changefreq>';
				$output .= "\t\t" . '<priority>' . $page['priority'] . '</priority>';
				$output .= "\t" . '</url>';
			}
		}
		return $output;
	}
}
