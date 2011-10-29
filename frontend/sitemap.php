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

	public function __construct()
	{
		$this->loadData();
	}

	/**
	 * This function will fetch all the meta data that is used to generate a sitemap.
	 */
	protected function loadData()
	{
		$this->metaData = FrontendModel::getDB()->getRecords('SELECT s.*
			 FROM meta_sitemap AS s
			 WHERE s.visible = ?', array('Y'));
	}

	public function parse()
	{
		// the search engines expect a xml file, so act like one
		SpoonHTTP::setHeaders(array('Content-Type: application/xml'));

		$output = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		foreach($this->metaData as $page)
		{
			$output .= "\t" . '<sitemap>';
			$output .= "\t\t" . '<loc>' . $page['full_url'] . '</loc>';
			$output .= "\t\t" . '<lastmod>' . $page['edited_on'] . '</lastmod>';
			$output .= "\t\t" . '<changefreq>' . $page['change_frequency'] . '</changefreq>';
			$output .= "\t\t" . '<priority>' . $page['priority'] . '</priority>';
			$output .= "\t" . '</sitemap>';
		}

		$output .= '</sitemapindex>';

		echo $output;
	}
}
