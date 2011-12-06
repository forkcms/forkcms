<?php

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
	protected $sitemapAction, $sitemapUrl;

	/**
	 * The sitemap data from the url
	 *
	 * @var int
	 */
	protected $sitemapPage = 0;

	/**
	 * The sitemap page limit
	 *
	 * @var int
	 */
	protected $pageLimit = 10;

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
		$url = str_replace('.xml', '', $this->sitemapUrl);
		$this->urlData = explode('sitemap', $url);

		$this->sitemapAction = (isset($this->urlData[0]) && $this->urlData[0] != '') ? $this->urlData[0] : null;
		if(isset($this->urlData[1]) && $this->urlData[1] != '')
		{
			$page = (int) ltrim($this->urlData[1], '-');
			if($page > 0) $page--;
			$this->sitemapPage = $page;
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
		return (array) FrontendModel::getDB()->getRecords(
			'SELECT s.*
			 FROM meta_sitemap AS s
			 WHERE s.visible = ?
			 LIMIT ?, ?',
			array('Y', (int) $offset, (int) $limit)
		);
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

		if($this->sitemapAction == 'page')
		{
			$this->pageLimit = FrontendModel::getModuleSetting('core', 'sitemap_page_num_items', 1);
			$this->metaData = $this->getMetaData($this->pageLimit, $this->sitemapPage);
		}
	}

	/**
	 * Parse the sitemap contents and ouptut is as an xml file
	 */
	public function parse()
	{
		// the search engines expect a xml file, so act like one
		SpoonHTTP::setHeaders(array('Content-Type: application/xml'));

		$sitemapType = ($this->sitemapAction === null) ? 'sitemapindex' : 'urlset';

		$output = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<' . $sitemapType . ' xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		if($this->sitemapAction == 'page') $output .= $this->parsePage();
		if($this->sitemapAction === null) $output .= $this->parseIndex();

		$output .= '</' . $sitemapType . '>';
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
		$output .= "\t\t" . '<lastmod>' . $this->getLastModificationDate($this->getMetaDataCount(), 0) . '</lastmod>';
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
		if($this->getMetaDataCount() > $this->pageLimit)
		{
			$numPages = ceil($this->getMetaDataCount() / $this->pageLimit);

			for($i = 1; $i <= $numPages; $i++)
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
