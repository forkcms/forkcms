<?php

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 * @subpackage	core
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
	 * Breadcrumb instance
	 *
	 * @var	FrontendBreadcrumb
	 */
	private $breadcrumb;


	/**
	 * Body instance
	 *
	 * @var	FrontendBody
	 */
	private $body;


	/**
	 * Extra instance
	 *
	 * @var	FrontendExtra
	 */
	private $extra;


	/**
	 * Footer instance
	 *
	 * @var	FrontendFooter
	 */
	private $footer;


	/**
	 * Header instance
	 *
	 * @var	FrontendHeader
	 */
	private $header;


	/**
	 * Navigation instance
	 *
	 * @var	FrontendNavigation
	 */
	private $navigation;


	/**
	 * The current pageId
	 *
	 * @var	int
	 */
	private $pageId;


	/**
	 * Template instance
	 *
	 * @var	SpoonTemplate
	 */
	private $tpl;


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
		$this->pageId = FrontendNavigation::getPageIdByUrl(implode('/', $this->url->getPages()));

		// set headers if this is a 404 page
		if($this->pageId == 404) SpoonHTTP::setHeadersByCode(404);

		// get pagecontent
		$this->getPageContent();

		// process page
		$this->processPage();

	}


	/**
	 * Display the page
	 *
	 * @return	void
	 */
	public function display()
	{
		$this->tpl->display(FRONTEND_CORE_PATH .'/layout/templates/index.tpl');

		$this->tpl->assign('FRONTEND_CORE_PATH', FRONTEND_CORE_PATH);

		Spoon::dump($this->tpl);
	}


	/**
	 * Get page content
	 *
	 * @return	void
	 */
	public function getPageContent()
	{
		// get page record
		$this->aPageRecord = (array) CoreModel::getPageRecordByPageId($this->pageId);

		// empty record (pageId doesn't exists)
		if(count($this->aPageRecord) == 0 && $this->pageId != 404) SpoonHTTP::redirect(FrontendNavigation::getUrlByPageId(404), 404);

		// redirect to first child
		if(empty($this->aPageRecord['content']) && $this->aPageRecord['extra_id'] == 0)
		{
			$childId = FrontendNavigation::getFirstChildIdByPageId($this->pageId);
			if($childId != '') SpoonHTTP::redirect(FrontendNavigation::getUrlByPageId($childId));
		}

	}


	/**
	 * Processes the page
	 *
	 * @return	void
	 */
	private function processPage()
	{
		// create template instance
		$this->tpl = new ForkTemplate();
		Spoon::setObjectReference('template', $this->tpl);

		// create and set header instance
		$this->header = new FrontendHeader();
		Spoon::setObjectReference('header', $this->header);

		// set meta information
		$this->header->setPageTitle($this->aPageRecord['meta_pagetitle'], ($this->aPageRecord['meta_pagetitle_overwrite'] == 'Y') ? true : false);
		$this->header->setMetaKeywords($this->aPageRecord['meta_keywords'], ($this->aPageRecord['meta_keywords_overwrite'] == 'Y') ? true : false);
		$this->header->setMetaDescription($this->aPageRecord['meta_description'], ($this->aPageRecord['meta_description_overwrite'] == 'Y') ? true : false);
		$this->header->setMetaCustom($this->aPageRecord['meta_custom']);

		// @todo	create and set breadcrumb instance
		$this->breadcrumb = new FrontendBreadcrumb();
		Spoon::setObjectReference('breadcrumb', $this->breadcrumb);

		// @todo	create navigation instance
		$this->navigation = new FrontendNavigation();

		// @todo	create footer instance
		$this->footer = new FrontendFooter();

		// create body instance
		$this->body = new FrontendBody();

		// set content title
		$this->body->setTitle($this->aPageRecord['title']);

		// set content
		$this->body->setContent($this->aPageRecord['content']);

		// @todo	create PageExtra instance if needed
		if($this->aPageRecord['extra_location'] != '')
		{
			// create extra instance
			$this->extra = new PageExtra($this->aPageRecord['extra_location'], $this->aPageRecord['extra_module_id'], $this->aPageRecord['extra_parameters']);
			Spoon::setObjectReference('extra', $this->extra);
		}
	}
}

?>