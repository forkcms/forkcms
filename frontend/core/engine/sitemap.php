<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

require_once FRONTEND_CORE_PATH . '/engine/language.php';

/**
 * This class will handle the sitemap for Fork. It will dynamicly create a sitemap for the pages
 * and images. It will also create sitemaps with pagination.
 *
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class FrontendSitemap
{
	/**
	 * The time format
	 *
	 * @var string
	 */
	const TIME_FORMAT = 'Y-m-d\TH:i:sP';

	/**
	 * The active language
	 *
	 * @var string
	 */
	protected $activeLanguage;

	/**
	 * Extra sitemap namespaces, for example for images
	 *
	 * @var array
	 */
	protected $extraSitemapNamespaces = array();

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
	 * The parsed xml data
	 *
	 * @var string
	 */
	protected $parsedData = array();

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
		$this->routeData($sitemapUrl);
		$this->loadData();
	}

	/**
	 * This will add an extra namespace for the xml data
	 *
	 * @param string $namespace
	 */
	protected function addExtraNamespace($namespace)
	{
		$this->extraSitemapNamespaces[] = (string) $namespace;
	}

	/**
	 * Converts an array to a valid xml string.
	 *
	 * @param array $xmlData
	 * @param int $tab How much tabs do we need at this point?
	 * @return string
	 */
	protected function arrayToXml(array $xmlData, $indent = 0)
	{
		$returnString = '';

		// go trough the elements to parse them into an xml node
		foreach($xmlData as $nodeName => $nodeData)
		{
			// this should be cleaned up with a whitelist, jadajada
			if(is_int($nodeName)) $returnString .= $this->arrayToXml($nodeData);
			else
			{
				// add tabs
				for($i = 0; $i <= $indent; $i++) $returnString .= "\t";

				// start of the new node
				$returnString .= '<' . $nodeName . '>';

				// the node data
				if(is_array($nodeData)) $returnString .= $this->arrayToXml($nodeData, $indent + 1);
				else $returnString .= $nodeData;

				// end of the new node
				$returnString .= '</' . $nodeName . '>' . "\n";
			}
		}

		return $returnString;
	}

	/**
	 * Fetches all the required images for the sitemap.
	 *
	 * @return array
	 */
	protected function getImageData()
	{
		$allModules = FrontendModel::getModules();
		$returnData = array();

		// go trough all the modules to see where we can find some images
		foreach($allModules as $module)
		{
			// do we have any image data available?
			$modelClass = 'Frontend' . SpoonFilter::toCamelCase($module) . 'Model';
			if(!is_callable(array($modelClass, 'sitemapImages'))) continue;

			$moduleData = call_user_func(array($modelClass, 'sitemapImages'));
			$parsedData = array();

			foreach($moduleData as $image)
			{
				// if there are no images, we don't need to add this to the sitemap
				$images = (isset($image['images'])) ? $image['images'] : array();
				if(empty($images)) continue;

				// get the default values
				$language = (isset($image['language'])) ? $image['language'] : $this->activeLanguage;
				$action = (isset($image['action'])) ? $image['action'] : null;

				// get the url for the page the image is on
				FL::setLocale($language);
				$url = SITE_URL;
				$imagePageUrl = FrontendNavigation::getURLForBlock($module, $action, $language);
				if(isset($image['url'])) $url .= $imagePageUrl . '/' . $image['url'];
				elseif(isset($image['full_url'])) $url .= '/' . ltrim($image['full_url'], '/');

				$tmpData = array('loc' => $url);

				// go trough the images to assign the image information
				foreach($images as $pageImage)
				{
					$imageData = array('image:loc' => $pageImage['src']);

					// if there is an alt attribute, assign it to the title
					if(isset($pageImage['alt'])) $imageData['image:title'] = $this->truncate($pageImage['alt']);

					// truncate the description
					$description = (isset($pageImage['description'])) ? $pageImage['description'] : null;
					$imageData['image:caption'] = $this->truncate($description);

					$tmpData[]['image:image'] = $imageData;
				}

				$returnData[]['url'] = $tmpData;
			}
		}

		return $returnData;
	}

	/**
	 * Fetch the last modification date for a range of items
	 *
	 * @param int $limit
	 * @param int $offset
	 * @param string[optional] $language
	 * @return string
	 */
	protected function getLastModificationDate($limit, $offset, $language = null)
	{
		$language = ($language === null) ? $this->activeLanguage : $language;
		$data = $this->getRawData($limit, $offset, $language);

		// get the latest modification
		$lastModDate = 0;
		foreach($data as $sitemap)
		{
			if($sitemap['edited_on'] > $lastModDate) $lastModDate = (int) $sitemap['edited_on'];
		}

		return FrontendModel::getUTCDate(self::TIME_FORMAT, $lastModDate);
	}

	/**
	 * Fetch the meta data so we can display the right information.
	 *
	 * @param int[optional] $limit
	 * @param int[optional] $offset
	 * @return array
	 */
	protected function getPageData($limit = 200, $offset = 0)
	{
		$data = $this->getRawData($limit, $offset);

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

			$data[$key]['edited_on'] = FrontendModel::getUTCDate(self::TIME_FORMAT, $sitemap['edited_on']);
		}
		return $data;
	}

	/**
	 * Count the number of records that should be shown for a specific language.
	 *
	 * @return int
	 */
	protected function getPageDataCount()
	{
		return (int) FrontendModel::getDB()->getNumRows(
			'SELECT s.id
			 FROM meta_sitemap AS s
			 WHERE s.visible = ? AND s.language = ?',
			array('Y', $this->activeLanguage)
		);
	}

	/**
	 * This will fetch the raw sitemap data for a specific range. This has a seperate function
	 * so we can easily adjust this without overhead.
	 *
	 * @param int[optional] $limit
	 * @param int[optional] $offset
	 * @param string[optional] $language
	 * @return array
	 */
	protected function getRawData($limit = 200, $offset = 0, $language = null)
	{
		$language = ($language === null) ? $this->activeLanguage : $language;

		return (array) FrontendModel::getDB()->getRecords(
			'SELECT s.*, UNIX_TIMESTAMP(s.edited_on) AS edited_on
			 FROM meta_sitemap AS s
			 WHERE s.visible = ? AND s.language = ?
			 ORDER BY s.edited_on DESC
			 LIMIT ?, ?',
			array('Y', $language, (int) $offset, (int) $limit)
		);
	}

	/**
	 * This function will fetch all the meta data that is used to generate a sitemap according to
	 * chosen action.
	 */
	protected function loadData()
	{
		// load the data according to the provided action
		switch($this->sitemapAction)
		{
			case 'page':
				$this->metaData = $this->getPageData($this->pageLimit, $this->sitemapPage);
				$this->numPages = ceil($this->getPageDataCount() / $this->pageLimit);
			break;
			case 'image':
				$this->metaData = $this->getImageData($this->pageLimit, $this->sitemapPage);
				$this->numPages = ceil(count($this->metaData) / $this->pageLimit);
			break;
			case '':
				// do nothing
			break;
			default:
				SpoonHTTP::redirect(FrontendNavigation::getURL(404, $this->activeLanguage));
			break;
		}

		$this->setPage();
	}

	/**
	 * Load the pagination according to the selected action.
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
			case 'image':
				$this->pageLimit = FrontendModel::getModuleSetting('pages', 'sitemap_images_items', 100);
			break;
			default:
				// do nothing
			break;
		}
	}

	/**
	 * Parse the sitemap content and ouptut is as an xml document.
	 */
	public function parse()
	{
		// the search engines expect a xml file, so act like one
		SpoonHTTP::setHeaders(array('Content-Type: application/xml'));

		// this is the override switch, if there is a sitemap uploaded, this will be used
		if(SpoonFile::exists(FRONTEND_PATH . '/' . $this->sitemapUrl)) $this->parseFile();

		// get the data to display
		$this->sitemapType = ($this->sitemapAction === null) ? 'sitemapindex' : 'urlset';
		if($this->sitemapAction == 'page') $this->parsedData = $this->parsePage();
		if($this->sitemapAction == 'image') $this->parsedData = $this->parseImage();
		if($this->sitemapAction === null) $this->parsedData = $this->parseIndex();
		$this->parseContent();
	}

	/**
	 * This will parse the automatic generated content
	 */
	protected function parseContent()
	{
		$output = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<' . $this->sitemapType . ' xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
		foreach($this->extraSitemapNamespaces as $namespace) $output .= ' ' . $namespace;
		$output .= '>';
		$output .= $this->arrayToXml($this->parsedData);
		$output .= '</' . $this->sitemapType . '>';
		echo $output;
		exit;
	}

	/**
	 * This will parse the existing file as a sitemap.
	 */
	protected function parseFile()
	{
		$filePath = FRONTEND_PATH . '/' . $this->sitemapUrl;
		$fileContents = SpoonFile::getContent($filePath);
		echo $fileContents;
		exit;
	}

	/**
	 * Parse the images. This will load parse the images into a sexy sitemap.
	 *
	 * @return array
	 */
	protected function parseImage()
	{
		$output = array();

		if($this->numPages > 1 && $this->sitemapPage === null)
		{
			$this->sitemapType = 'sitemapindex';

			// build the number of sitemaps equal to the number of image pages
			for($i = 1; $i <= $this->numPages; $i++)
			{
				$output[]['sitemap'] = array(
					'loc' => SITE_URL . '/imagesitemap-' . $i . '.xml'
				);
			}
		}
		else
		{
			// since we want multiple images we should use the namespace from google schematics
			$this->addExtraNamespace('xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"');
			$output = array_slice($this->metaData, $this->sitemapPage, $this->pageLimit);
		}

		return $output;
	}

	/**
	 * Parse the sitemapindex. This will show an overview with sitemaps available.
	 *
	 * @return array
	 */
	protected function parseIndex()
	{
		$output = array();

		// build the pages sitemap
		if(SITE_MULTILANGUAGE)
		{
			// create a sitemap for each language
			foreach(FL::getActiveLanguages() as $language)
			{
				// add the data so we can parse this in the sitemap
				$output[]['sitemap'] = array(
					'loc' => SITE_URL . '/' . $language . '-pagesitemap.xml',
					'lastmod' => $this->getLastModificationDate($this->getPageDataCount(), 0, $language)
				);
			}
		}
		else
		{
			// add the data so we can parse this in the sitemap
			$output[]['sitemap'] = array(
				'loc' => SITE_URL . '/pagesitemap.xml',
				'lastmod' => $this->getLastModificationDate($this->getPageDataCount(), 0)
			);
		}

		// build the image sitemap
		$output[]['sitemap'] = array(
			'loc' => SITE_URL . '/imagesitemap.xml'
		);

		return $output;
	}

	/**
	 * Parse the page data. This will create either another sitemap or a urlset.
	 *
	 * @return array
	 */
	protected function parsePage()
	{
		$output = array();

		// if we exceed the maximum, we should show some sort of pagination
		if($this->numPages > 1 && $this->sitemapPage === null)
		{
			$this->sitemapType = 'sitemapindex';
			$prefix = (SITE_MULTILANGUAGE) ? $this->activeLanguage . '-' : '';

			// build the number of sitemaps equal to the number of pages
			for($i = 1; $i <= $this->numPages; $i++)
			{
				// add the data so we can parse this in the sitemap
				$output[]['sitemap'] = array(
					'loc' => SITE_URL . '/' . $prefix . 'pagesitemap-' . $i . '.xml',
					'lastmod' => $this->getLastModificationDate($this->pageLimit, $i - 1)
				);
			}
		}
		else
		{
			// parse all the elements into a decent array
			foreach($this->metaData as $page)
			{
				// add the data so we can parse this in the sitemap
				$output[]['url'] = array(
					'loc' => $page['full_url'],
					'lastmod' => $page['edited_on'],
				);
			}
		}
		return $output;
	}

	/**
	 * This is the router for the sitemap.
	 *
	 * @param string $sitemapUrl
	 */
	protected function routeData($sitemapUrl)
	{
		// store the url
		$this->sitemapUrl = (string) $sitemapUrl;

		// seperate the url data
		preg_match_all('/(.*?)sitemap(.*?)\.xml/', $this->sitemapUrl, $urlData);
		$this->urlData = (array) array_merge($urlData[1], $urlData[2]);

		// set the default active language
		$this->activeLanguage = FrontendModel::getModuleSetting('core', 'default_language');
		$this->sitemapAction = ($this->urlData[0] == '') ? null : $this->urlData[0];

		// set the sitemap data
		if($this->urlData[0] != '')
		{
			// check if we have a language specified
			$prefixChunks = explode('-', $this->urlData[0]);
			$activeLanguages = FL::getActiveLanguages();

			// we have selected a language
			if(count($prefixChunks) > 1)
			{
				// set the active language
				if(in_array($prefixChunks[0], $activeLanguages)) $this->activeLanguage = $prefixChunks[0];
				else throw new Exception('This(' . $prefixChunks[0] . ') is an invalid language');

				// set the sitemap action
				$this->sitemapAction = $prefixChunks[1];
			}
		}

		// load the pagination data
		$this->loadPagination($this->sitemapAction);
	}

	/**
	 * This will set the current page for the pagination
	 */
	protected function setPage()
	{
		if($this->urlData[1] != '')
		{
			// get the current page
			$page = (int) ltrim($this->urlData[1], '-');
			if($page > 0) $page--;
			if($page > $this->numPages) $page = $this->numPages;

			$this->sitemapPage = $page;
		}
	}

	/**
	 * This will truncate a text after x number of characters on the first occurance of a whitespace
	 *
	 * @param string $content
	 * @param int[optional] $offset
	 * @return string
	 */
	protected function truncate($content, $offset = 140)
	{
		$content = SpoonFilter::stripHTML($content);
		if($content != null && strlen($content) > $offset)
		{
			$content = substr($content, 0, strpos($content, ' ', 140));
		}

		return $content;
	}
}
