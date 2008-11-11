<?php

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 * @subpackage	breadcrumb
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBreadcrumb extends FrontendBaseObject
{
	/**
	 * The items
	 *
	 * @var	array
	 */
	private $aItems = array();


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// call parent
		parent::__construct();

		$homeInfo = FrontendNavigation::getPageInfo(1);
		$this->addElement($homeInfo['navigation'], (SITE_MULTILANGUAGE) ? '/'. FRONTEND_LANGUAGE : '/');

		// get other pages
		$aPages = $this->url->getPages();

		// init var
		$aItems = array();
		$errorUrl = FrontendNavigation::getUrlByPageId(404);

		while(!empty($aPages))
		{
			// init vars
			$url = implode('/', $aPages);
			$menuId = FrontendNavigation::getPageIdByUrl($url);
			$pageInfo = FrontendNavigation::getPageInfo($menuId);

			// do we know something about the page
			if($pageInfo !== false && isset($pageInfo['navigation']))
			{
				$pageUrl = FrontendNavigation::getUrlByPageId($menuId);
				if($pageUrl == $errorUrl) $pageUrl = null;
				$aItems[] = array('title' => $pageInfo['navigation'], 'url' => $pageUrl);
			}

			// remove element
			array_pop($aPages);
		}

		// reverse
		krsort($aItems);

		// add elements
		foreach($aItems as $row) $this->addElement($row['title'], $row['url']);
	}


	/**
	 * Add an element
	 *
	 * @return	void
	 * @param	string $title
	 * @param	string[optional] $url
	 */
	public function addElement($title, $url = null)
	{
		$this->aItems[] = array('title' => (string) $title, 'url' => (string) $url);
	}


	/**
	 * Parse the breadcrumb into the template
	 *
	 * @return	void
	 */
	public function parse()
	{
		// init vars
		$aItems = array();
		$first = true;
		$count = count($this->aItems);

		// loop items and add the seperator
		foreach($this->aItems as $i => $row)
		{
			// remove url from last element
			if($i >= $count - 1) $row['url'] = null;

			// options
			$row['oHasUrl'] = ($row['url'] !== null);
			$row['oSeparator'] = !$first;

			// add
			$aItems[] = $row;

			// no more first
			$first = false;
		}

		// assign
		$this->tpl->assign('iBreadcrumb', $aItems);
	}
}
?>