<?php

/** Require Model */
require_once FRONTEND_CORE_PATH .'/engine/model.php';

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package		Frontend
 * @subpackage	Core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class Frontend
{
	/**
	 * Database instance
	 *
	 * @var	SpoonDatabase
	 */
	private $db;


	/**
	 * Page instance
	 *
	 * @var	FrontendPage
	 */
	private $page;


	/**
	 * Url instance
	 *
	 * @var	FrontendUrl
	 */
	private $url;


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// create database-object
		$this->db = CoreModel::getDB();

		// create url-object
		$this->url = new FrontendUrl();

		// add to reference
		Spoon::setObjectReference('url', $this->url);

		// create and set page reference
		$this->page = new FrontendPage();
		Spoon::setObjectReference('page', $this->page);

		// display page
//		$this->page->display();
	}
}
?>