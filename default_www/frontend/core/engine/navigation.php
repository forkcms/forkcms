<?php

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 * @subpackage	navigation
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendNavigation extends FrontendBaseObject
{
	/**
	 * The keys
	 *
	 * @var	array
	 */
	private static $aKeys = array(), $aNavigation = array();


	/**
	 * Get all footerlinks
	 *
	 * @return	array
	 */
	public static function getFooterLinks()
	{
		// get footerlinks
		$aFooterLinks = array();
		$aLinks = (isset(self::$aNavigation[FRONTEND_LANGUAGE][-2][-2])) ? self::$aNavigation[FRONTEND_LANGUAGE][-2][-2] : array();

		// loop rows
		foreach ($aLinks as $pageId => $row)
		{
			// redefine data
			$aTemp['title'] = $row['navigation'];
			$aTemp['url'] = $row['url'];
			$aTemp['oIsCurrentPage'] = (bool) ($pageId == FrontendPage::getCurrentPageId());

			// add to footerlinks
			$aFooterLinks[] = $aTemp;
		}

		// return
		return (array) $aFooterLinks;
	}


	/**
	 * Get the menu-keys
	 *
	 * @return	array
	 */
	public static function getKeys($language = null)
	{
		// redefine
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// does the keys exists in the cache?
		if(!isset(self::$aKeys[$language]) || empty(self::$aKeys[$language]))
		{
			// validate file
			if(!SpoonFile::exists(FRONTEND_CACHE_PATH .'/navigation/keys_'. $language .'.php')) throw new FrontendException('No key-file (keys_'. $language .'.php) found.');

			// require file
			require FRONTEND_CACHE_PATH .'/navigation/keys_'. $language .'.php';

			// store
			self::$aKeys[$language] = $keys;
		}

		// return
		return self::$aKeys[$language];
	}


	/**
	 * Get a menuid for an
	 *
	 * @return	int
	 * @param 	string $url
	 * @param	string[optional] $language
	 */
	public static function getPageIdByUrl($url, $language = null)
	{
		// redefine
		$url = (string) $url;
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// get menu items array
		$aKeys = self::getKeys($language);

		// get key
		$key = array_search($url, $aKeys);

		// return
		if($key === false) return 404;
		return (int) $key;
	}


	/**
	 * Get the navigation-items
	 *
	 * @return	array
	 * @param	string[optional] $language
	 */
	public static function getNavigation($language = null)
	{
		// redefine
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// does the keys exists in the cache?
		if(!isset(self::$aNavigation[$language]) || empty(self::$aNavigation[$language]))
		{
			// validate file
			if(!SpoonFile::exists(FRONTEND_CACHE_PATH .'/navigation/navigation_'. $language .'.php')) throw new FrontendException('No navigation-file (navigation_'. $language .'.php) found.');

			// require file
			require FRONTEND_CACHE_PATH .'/navigation/navigation_'. $language .'.php';

			// store
			self::$aNavigation[$language] = $navigation;
		}

		// return
		return self::$aNavigation[$language];
	}


	/**
	 * Get more info about a page
	 *
	 * @return	mixed
	 * @param	int $pageId
	 */
	public static function getPageInfo($pageId)
	{
		// get navigation
		$aNavigation = self::getNavigation();

		// loop levels
		foreach ($aNavigation as $depth => $aLevel)
		{
			// loop parents
			foreach ($aLevel as $parentId => $aChilds)
			{
				// loop childs
				foreach ($aChilds as $itemId => $aItem)
				{
					if($pageId == $itemId)
					{
						// set return
						$aReturn = $aItem;
						$aReturn['page_id'] = $itemId;

						// return
						return $aReturn;
					}
				}
			}
		}

		// return
		return false;
	}

	/**
	 * Get parentId
	 *
	 * @return	mixed
	 * @param	string $url
	 * @param	string[optional] $language
	 */
	public static function getParentIdByUrl($url, $language = null)
	{
		// redefine
		$url = (string) $url;
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		$aNavigation = self::getNavigation($language);
		$pageId = self::getPageIdByUrl($url, $language);

		// loop levels
		foreach ($aNavigation as $depth => $aLevel)
		{
			// loop parents
			foreach ($aLevel as $parentId => $aChilds)
			{
				// loop childs
				foreach ($aChilds as $itemId => $aItem)
				{
					if($pageId == $itemId) return (int) $parentId;
				}
			}
		}

		// fallback
		return false;
	}


	/**
	 * Get url for a given pageId
	 *
	 * @return	string
	 * @param	int $pageId
	 * @param	string[optional] $language
	 */
	public static function getUrlByPageId($pageId, $language = null)
	{
		// redefine
		$pageId = (int) $pageId;
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		$url = (SITE_MULTILANGUAGE) ? '/'. $language .'/' : '/';

		// get the menuItems
		$aKeys = self::getKeys($language);

		// get the url, if it doens't exist return 404
		if(!isset($aKeys[$pageId])) return self::getUrlByPageId(404);

		// add url
		else $url .= $aKeys[$pageId];

		// return
		return $url;
	}


	/**
	 * @todo	Parse the navigation into the template
	 *
	 * @return	void
	 */
	public function parse()
	{
	}
}

?>