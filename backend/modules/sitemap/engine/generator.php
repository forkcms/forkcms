<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the sitemap generator
 *
 * @author Jonas Goderis <jonas.goderis@wijs.be>
 */
class BackendSitemapGenerator
{
	/**
	 * @var array
	 */
	protected $indexes;
	protected $languages;
	protected $navigations;
	protected $types = array('page', 'meta', 'footer');
	protected $pageTypesToSkip = array('error', 'direct_action', 'hidden');
	protected $nonImplementedModules = array();

	/**
	 * The constructor of this class
	 */
	public function __construct()
	{
		$this->loadData();
		$this->createIndexes();
	}

	/**
	 * Adds a module to an index
	 *
	 * @param array $page
	 * @param string $language
	 *
	 * @return void
	 */
	protected function addModuleToIndex(array $page, $language)
	{
		// get some vars
		$module = $this->getModuleExtra($page);
		$modulePages = array();

		// continue only if a module has been found
		if($module)
		{
			// get correct class name for that module
			$class = $this->getClassName($module);

			// check if class exists
			if(class_exists($class))
			{
				$refClass = new ReflectionClass($class);

				// check if the class implements an interface so we can fetch the
				// pages for that module
				if($refClass->implementsInterface('BackendSitemapInterface'))
				{
					// get normal sitemap for this module
					$refMethod = new ReflectionMethod($class, 'getSitemap');
					$modulePages = $refMethod->invoke(null, $language);

					// add pages to a new index when we got some pages
					if(!empty($modulePages))
					{
						$this->indexes[$module . '-' . $language] = $modulePages;
					}

					// get image sitemap for this module
					$refMethod = new ReflectionMethod($class, 'getImageSitemap');
					$moduleImages = $refMethod->invoke(null, $language);

					// add pages to a new index when we got some pages
					if(!empty($moduleImages))
					{
						$this->indexes[$module . '-images-' . $language] = $moduleImages;
					}
				}
				else
				{
					// save module so we can show wich modules doesn't implement the interface
					$this->addNonImplementedModule($module);
				}
			}
		}

		// if module didn't provide any pages we add the page to the pages (default) index
		if(empty($modulePages))
		{
			$this->indexes['pages-' . $language][]['loc'] = SITE_URL . $page['full_url'];
		}
	}

	/**
	 * Add non implemented module
	 *
	 * @param string $module
	 */
	protected function addNonImplementedModule($module)
	{
		// redefine incoming params
		$module = array('name' => (string) $module);

		// add module to array if it doesn't exist yes, this
		// is needed because we're handling multi languages
		if(!in_array($module, $this->nonImplementedModules))
		{
			$this->nonImplementedModules[] = $module;
		}
	}

	/**
	 * Adds a page to an index
	 *
	 * @param array $page
	 * @param string $language
	 *
	 * @return void
	 */
	protected function addPageToIndex(array $page, $language)
	{
		// Check if we're dealing with an extra
		if($page['has_extra'])
		{
			// load the urls for a specific module
			$this->addModuleToIndex($page, $language);
		}
		else
		{
			// no extra found so we're dealing with a normal page
			$this->indexes['pages-' . $language][]['loc'] = SITE_URL . $page['full_url'];
		}

		// fetch the children for the current page
		if(isset($this->navigations[$language]['page'][$page['page_id']]))
		{
			// loop through the child pages
			foreach($this->navigations[$language]['page'][$page['page_id']] as $childPage)
			{
				// skip some tree types
				if(in_array($childPage['tree_type'], $this->pageTypesToSkip)) continue;

				$this->addPageToIndex($childPage, $language);
			}
		}
	}

	/**
	 * Create all the indexes for the index sitemap
	 *
	 * @return void
	 */
	protected function createIndexes()
	{
		// loop through all the types we should get the pages from
		foreach($this->types as $type)
		{
			// loop through the navigation for each language
			foreach($this->navigations as $language => $navigation)
			{
				// check if the type exists for that language
				if(isset($navigation[$type][0]))
				{
					// loop through all the pages for a specific type for a certain language
					foreach($navigation[$type][0] as $page)
					{
						// skip some tree types
						if(in_array($page['tree_type'], $this->pageTypesToSkip)) continue;

						$this->addPageToIndex($page, $language);
					}
				}
			}
		}
	}

	/**
	 * Generate xml for an index
	 *
	 * @return string $index
	 *
	 * @return void
	 */
	protected function generateXml($index)
	{
		// create new xml file for that specific index
		$xml = new DOMDocument('1.0', 'utf-8');
		$xml->formatOutput = true;
		$rootElement = $xml->createElementNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'urlset');
		$xml->appendChild($rootElement);

		// create xml elements
		foreach($this->indexes[$index] as $key => $value)
		{
			// create an url element
			$urlElement = $xml->createElement('url');
			$rootElement->appendChild($urlElement);

			// create location element
			$locElement = $xml->createElement('loc', $value['loc']);
			$urlElement->appendChild($locElement);

			// if set add the lastmod element
			if(!empty($value['lastmod']))
			{
				// create last modification date element
				$lastModElement = $xml->createElement('lastmod', $value['lastmod']);
				$urlElement->appendChild($lastModElement);
			}
		}

		// save the xml to a file
		$xml->save(PATH_WWW . '/sitemap-' . $index . '.xml');
	}

	/**
	 * Generate xml for an index
	 *
	 * @return string $index
	 *
	 * @return void
	 */
	protected function generateXmlImages($index)
	{
		// create new xml file for that specific index
		$xml = new DOMDocument('1.0', 'utf-8');
		$xml->formatOutput = true;
		$rootElement = $xml->createElementNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'urlset');
		$xml->appendChild($rootElement);
		$rootElement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');

		// create xml elements
		foreach($this->indexes[$index] as $key => $value)
		{
			// to be sure check if there are realy images set
			if(!empty($value['images']))
			{
				// create an url element
				$urlElement = $xml->createElement('url');
				$rootElement->appendChild($urlElement);

				// create location element
				$locElement = $xml->createElement('loc', $value['loc']);
				$urlElement->appendChild($locElement);

				// add all the images
				foreach((array) $value['images'] as $image)
				{
					// create an image element
					$imageElement = $xml->createElement('image:image');
					$urlElement->appendChild($imageElement);

					// create image loc element
					$imageLocElement = $xml->createElement('image:loc', $image);
					$imageElement->appendChild($imageLocElement);
				}
			}
		}

		// save the xml to a file
		$xml->save(PATH_WWW . '/sitemap-' . $index . '.xml');
	}

	/**
	 * Get the real class name
	 *
	 * @param string $module
	 *
	 * @return string
	 */
	protected function getClassName($module)
	{
		$moduleParts = explode('_', (string) $module);

		// start all parts with capital
		foreach($moduleParts as &$part) $part = ucfirst($part);

		// get real class name
		$moduleName = implode('', $moduleParts);
		$class = 'Backend' . $moduleName . 'Model';

		return $class;
	}

	/**
	 * Get all the indexes
	 *
	 * @return array
	 */
	public function getIndexes()
	{
		return $this->indexes;
	}

	/**
	 * Get all the indexes
	 *
	 * @return array
	 */
	public function getIndexesNames()
	{
		$names = array();

		// loop through all indexes and get the name
		foreach($this->indexes as $name => $value)
		{
			$names[] = array('name' => 'sitemap-' . $name . '.xml');
		}

		return $names;
	}

	/**
	 * A page can contain multiple extras. This method will loop over all these
	 * extra's and will check if there is a module extra set
	 *
	 * @param $page
	 *
	 * @return array
	 */
	protected function getModuleExtra(array $page)
	{
		$module = false;

		// loop through all the extra's
		foreach($page['extra_blocks'] as $block)
		{
			if(!empty($block['module']) && $block['action'] == null)
			{
				$module = $block['module'];
				break;
			}
		}

		return $module;
	}

	/**
	 * Get the non implemented modules
	 *
	 * @return array
	 */
	public function getNonImplementedModules()
	{
		return $this->nonImplementedModules;
	}

	/**
	 * Load all the data so we can generatre our xml sitemap
	 *
	 * @return void
	 */
	protected function loadData()
	{
		// get the active languages
		$this->languages = BackendLanguage::getActiveLanguages();

		// get the navigation for the frontend and for each language
		foreach($this->languages as $language)
		{
			$this->navigations[$language] = BackendModel::getNavigation($language);
		}
	}

	/**
	 * Save xml
	 *
	 * @return void
	 */
	public function saveXml()
	{
		// create new xml (the real sitemap.xml (indexed))
		$xml = new DOMDocument('1.0', 'utf-8');
		$xml->formatOutput = true;
		$rootElement = $xml->createElementNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'sitemapindex');
		$xml->appendChild($rootElement);

		// loop through all indexes
		foreach($this->indexes as $key => $index)
		{
			// create new sitemap element (for each index)
			$sitemapElement = $xml->createElement('sitemap');
			$rootElement->appendChild($sitemapElement);

			// create location element
			$locElement = $xml->createElement('loc', SITE_URL . '/sitemap-' . $key . '.xml');
			$sitemapElement->appendChild($locElement);

			// generate the xml for this index
			if(strpos($key, 'images') == false)
			{
				// generate normal xml sitemap
				$this->generateXml($key);
			}
			else
			{
				// generate image xml sitemap
				$this->generateXmlImages($key);
			}

		}

		// save the xml to a file
		$xml->save(PATH_WWW . '/sitemap.xml');
	}
}