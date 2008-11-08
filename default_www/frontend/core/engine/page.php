<?php
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
class FrontendPage
{
	/**
	 * Content of the page
	 *
	 * @var	array
	 */
	private $aPageRecord = array();


	/**
	 * The current menuId
	 *
	 * @var	int
	 */
	private $menuId;


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
		// set url instance
		$this->url = Spoon::getObjectReference('url');

		// get menu id for requested url
		$this->menuId = FrontendNavigation::getMenuIdByUrl(implode('/', $this->url->getPages()));

		// set headers if this is a 404 page
		if($this->menuId == 404) SpoonHTTP::setHeadersByCode(404);

		// get pagecontent
		$this->getPageContent();

		// @todo till here
		Spoon::dump($this->aPageRecord);
	}


	/**
	 * Get page content
	 *
	 * @return	void
	 */
	public function getPageContent()
	{
		// get page record
		$this->aPageRecord = (array) CoreModel::getPageRecordByMenuId($this->menuId);

		// empty record (menuid doesn't exists)
		if(count($this->aPageRecord) == 0 && $this->menuId != 404) SpoonHTTP::redirect(FrontendNavigation::getUrlByMenuId(404), 404);

		// redirect to first child
		if(empty($this->aPageRecord['content']) && $this->aPageRecord['extra_id'] == 0)
		{
			$childId = FrontendNavigation::getFirstChildMenuIdByMenuId($this->menuId);
			if($childId != '') SpoonHTTP::redirect(FrontendNavigation::getUrlByMenuId($childId));
		}

	}
}
?>