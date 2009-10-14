<?php

/**
 * BackendPagesModel
 *
 * In this file we store all generic functions that we will be using in the PagesModule
 *
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendPagesModel
{
	public static function addNumber($string)
	{
		// split
		$chunks = explode('-', $string);

		// count the chunks
		$count = count($chunks);

		// get last chunk
		$last = $chunks[$count - 1];

		// is nummeric
		if(SpoonFilter::isNumeric($last))
		{
			// remove last chunk
			array_pop($chunks);

			// join together
			$string = implode('-', $chunks ) .'-'. ((int) $last + 1);
		}

		// not numeric
		else $string .= '-2';

		// return
		return $string;
	}


	/**
	 * @todo	Get all the available extra's
	 *
	 * @return	array
	 */
	public static function getExtras()
	{
		return array('html' => BL::getLabel('Editor'));
	}


	public static function getFullURL($menuId)
	{
		// @todo	this method should use a genious caching-system
		// @todo fix me, das bugge code ;)

		// redefine
		$menuId = (int) $menuId;

		// get db
		$db = BackendModel::getDB();

		if($menuId = 0) return '/';

		$url = (string) $db->getVar('SELECT m.url
										FROM pages AS p
										INNER JOIN meta AS m ON p.meta_id = m.id
										WHERE p.id = ? AND p.status = ?
										LIMIT 1;',
										array($menuId, 'active'));
	}


	/**
	 * Get the maximum unique id for blocks
	 *
	 * @return	int
	 */
	public static function getMaximumBlockId()
	{
		// get db
		$db = BackendModel::getDB();

		// get the maximum id
		return (int) $db->getVar('SELECT MAX(pb.id)
									FROM pages_blocks AS pb;');
	}


	/**
	 * Get the maximum unique id for pages
	 *
	 * @return	int
	 * @param	string[optional] $language
	 */
	public static function getMaximumMenuId($language = null)
	{
		// redefine
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// get db
		$db = BackendModel::getDB();

		// get the maximum id
		$maximumMenuId = (int) $db->getVar('SELECT MAX(p.id)
											FROM pages AS p
											WHERE p.language = ?;',
											array($language));

		// pages created by a user should have an id higher then 1000
		// with this hack we can easily find pages added by a user
		if($maximumMenuId < 1000 && !BackendAuthentication::getUser()->isGod()) return $maximumMenuId + 1000;

		// fallback
		return $maximumMenuId;
	}



	/**
	 * Get the maximum sequence inside a leaf
	 *
	 * @return	int
	 * @param	int $parentId
	 * @param	int[optional] $language
	 */
	public static function getMaximumSequence($parentId, $language = null)
	{
		// redefine
		$parentId = (int) $parentId;
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// get db
		$db = BackendModel::getDB();

		// get the maximum sequence inside a certain leaf
		return (int) $db->getVar('SELECT MAX(p.sequence)
									FROM pages AS p
									WHERE p.language = ? AND p.parent_id = ?;',
									array($language, $parentId));
	}


	/**
	 * Get templates
	 *
	 * @return unknown
	 */
	public static function getTemplates()
	{
		// get db
		$db = BackendModel::getDB();

		// get templates
		$templates = (array) $db->retrieve('SELECT t.id, t.label, t.path, t.number_of_blocks, t.is_default, t.data
											FROM pages_templates AS t
											WHERE t.active = ?;',
											array('Y'), 'id');

		// loop templates to unserialize the data
		foreach($templates as $key => $row)
		{
			// unserialize
			$templates[$key]['data'] = unserialize($row['data']);

			// add names into the properties
			$templates[$key]['namesString'] = '"' . implode('", "', $templates[$key]['data']['names']) .'"';
		}

		// return
		return (array) $templates;
	}


	/**
	 * @todo	fix me...
	 *
	 * @param unknown_type $url
	 * @param unknown_type $id
	 * @return unknown
	 */
	public static function getUrl($url, $id = null, $parentId = 0)
	{
		// redefine
		$url = (string) $url;
		$parentId = (int) $parentId;

		// get db
		$db = BackendModel::getDB();

		// no specific id
		if($id === null)
		{
			// get number of childs within this parent with the specified url
			$number = (int) $db->getNumRows('SELECT p.id
												FROM pages AS p
												INNER JOIN meta AS m ON p.meta_id = m.id
												WHERE p.parent_id = ? AND  p.status = ? AND m.url = ?;',
												array($parentId, 'active', $url));

			// no items?
			if($number == 0) $url = $url;

			// there are items so, call this method again.
			else
			{
				// add a number
				$url = self::addNumber($url);

				// recall this method, but with a new url
				return self::getUrl($url, $id, $parentId);
			}
		}

		// one item should be ignored
		else
		{
			// @todo
			Spoon::dump('NOT IMPLEMENTED');
		}

		// get full url
		$fullUrl = self::getFullUrl($parentId) .'/'. $url;

		// check if folder exists
		if(SpoonDirectory::exists(PATH_WWW .'/'. $fullUrl))
		{
			// add a number
			$url = self::addNumber($url);

			// recall this method, but with a new url
			return self::getUrl($url, $id, $parentId);
		}

		// check if it is an appliation
		if(in_array(trim($fullUrl, '/'), array_keys(ApplicationRouting::getRoutes())))
		{
			// add a number
			$url = self::addNumber($url);

			// recall this method, but with a new url
			return self::getUrl($url, $id, $parentId);
		}

		// return the unique url!
		return $url;
	}


	/**
	 * Insert a page
	 *
	 * @return	int
	 * @param	array $page
	 */
	public static function insert(array $page)
	{
		// get db
		$db = BackendModel::getDB();

		// insert
		$id = (int) $db->insert('pages', $page);

		// return the new revision id
		return $id;
	}


	/**
	 * Insert multiple blocks at once
	 *
	 * @return	void
	 * @param	array $blocks
	 */
	public static function insertBlocks(array $blocks)
	{
		// get db
		$db = BackendModel::getDB();

		// insert
		$db->insert('pages_blocks', $blocks);
	}
}

?>