<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will be used to manage the breadcrumb
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Dieter Vanden Eynde <dieter@netlash.com>
 */
class FrontendBreadcrumb extends FrontendBaseObject
{
	/**
	 * The items in the breadcrumb
	 *
	 * @var	array
	 */
	private $items = array();

	/**
	 * Default constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// add into the reference
		Spoon::set('breadcrumb', $this);

		// get more information for the homepage
		$homeInfo = FrontendNavigation::getPageInfo(1);

		// add homepage as first item (with correct element)
		$this->addElement($homeInfo['navigation_title'], FrontendNavigation::getURL(1));

		// get other pages
		$pages = $this->URL->getPages();

		// init vars
		$items = array();
		$errorURL = FrontendNavigation::getUrl(404);

		// loop pages
		while(!empty($pages))
		{
			// init vars
			$URL = implode('/', $pages);
			$menuId = FrontendNavigation::getPageId($URL);
			$pageInfo = FrontendNavigation::getPageInfo($menuId);

			// do we know something about the page
			if($pageInfo !== false && isset($pageInfo['navigation_title']))
			{
				// only add pages that aren't direct actions
				if($pageInfo['tree_type'] != 'direct_action')
				{
					// get URL
					$pageURL = FrontendNavigation::getUrl($menuId);

					// if this is the error-page, so we won't show an URL.
					if($pageURL == $errorURL) $pageURL = null;

					// add to the items
					$items[] = array('title' => $pageInfo['navigation_title'], 'url' => $pageURL);
				}
			}

			// remove element
			array_pop($pages);
		}

		// reverse so everything is in place
		krsort($items);

		// loop and add elements
		foreach($items as $row) $this->addElement($row['title'], $row['url']);
	}

	/**
	 * Add an element
	 *
	 * @param string $title The label that will be used in the breadcrumb.
	 * @param string[optional] $URL The URL for this item.
	 */
	public function addElement($title, $URL = null)
	{
		$this->items[] = array('title' => (string) $title, 'url' => $URL);
	}

	/**
	 * Clear all (or a specific) elements in the breadcrumb
	 *
	 * @param int[optional] $key If the key is provided it will be removed from the array, otherwise the whole array will be cleared.
	 */
	public function clear($key = null)
	{
		// key given?
		if($key !== null)
		{
			// remove specific key
			unset($this->items[(int) $key]);

			// resort, to avoid shit when parsing
			$this->items = SpoonFilter::arraySortKeys($this->items);
		}

		// clear all
		else $this->items = array();
	}

	/**
	 * Count number of breadcrumbs that are already added.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->items);
	}

	/**
	 * Get all elements
	 *
	 * @return array
	 */
	public function get()
	{
		return $this->items;
	}

	/**
	 * Parse the breadcrumb into the template
	 */
	public function parse()
	{
		// init vars
		$items = array();
		$numItems = count($this->items);

		// loop items and add the seperator
		foreach($this->items as $i => $row)
		{
			// remove URL from last element
			if($i >= $numItems - 1)
			{
				// remove URL for last object
				$row['url'] = null;
			}

			// add item
			$items[] = $row;
		}

		// assign
		$this->tpl->assign('breadcrumb', $items);
	}
}
