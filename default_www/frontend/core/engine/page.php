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
class FrontendPage extends FrontendBaseObject
{
	/**
	 * Content of the page
	 *
	 * @var	array
	 */
	private $record = array();


	/**
	 * Breadcrumb instance
	 *
	 * @var	FrontendBreadcrumb
	 */
	private $breadcrumb;


	/**
	 * Current page id
	 *
	 * @var	int
	 */
	private static $currentPageId;


	/**
	 * Blocks
	 *
	 * @var	FrontendBlock
	 *
	 * @var unknown_type
	 */
	private $blocks;


	/**
	 * Body instance
	 *
	 * @var	FrontendBody
	 */
	private $body;


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
	 * The pages statuscode
	 *
	 * @var	int
	 */
	private $statusCode = 200;


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// call parent
		parent::__construct();

		// get menu id for requested url
		$this->pageId = FrontendNavigation::getPageIdByURL(implode('/', $this->url->getPages()));

		// make the pageId accessible through a static method
		self::$currentPageId = $this->pageId;

		// set headers if this is a 404 page
		if($this->pageId == 404) $this->statusCode = 404;

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
		// set headers
		if($this->statusCode == 404) SpoonHTTP::setHeadersByCode(404);

		// store statistics
		$this->storeStatistics();

		// parse footer
//		$this->footer->parse();

		// parse body if needed
		if($this->body) $this->body->parse();

		// parse extra if needed
		/*if($this->extra)
		{
			$this->extra->parse();
			$this->tpl->assign('oHasExtra', true);
		}*/

		// parse breadcrumb
//		$this->breadcrumb->parse();

		// parse header
//		$this->header->parse();

		// aaaaa
//		$this->tpl->display(FRONTEND_PATH .'/'. $templatePath);
	}


	/**
	 * Get the current pageid
	 *
	 * @return	int
	 */
	public static function getCurrentPageId()
	{
		return self::$currentPageId;
	}


	/**
	 * Get page content
	 *
	 * @return	void
	 */
	public function getPageContent()
	{
		// get page record
		$this->record = (array) FrontendModel::getPage($this->pageId);

		Spoon::dump($this->record);

		// empty record (pageId doesn't exists)
		if(count($this->record) == 0 && $this->pageId != 404) SpoonHTTP::redirect(FrontendNavigation::getURLByPageId(404), 404);

		// @todo indien er geen inhoud is, doorverwijzen naar eerste child. Wordt wel lastig om te verififeren bij 1ste block.
		// redirect to first child
		/*if(empty($this->record['content']) && $this->aPageRecord['extra_id'] == 0)
		{
			// get first child
			$childId = FrontendNavigation::getFirstChildIdByPageId($this->pageId);

			// redirect if possible
			if($childId !== false) SpoonHTTP::redirect(FrontendNavigation::getUrlByPageId($childId));
		}*/
	}


	/**
	 * Processes the page
	 *
	 * @return	void
	 */
	private function processPage()
	{
		// create and set header instance
		$this->header = new FrontendHeader();
		Spoon::setObjectReference('header', $this->header);

		// add css
		$this->header->addCssFile(FRONTEND_CORE_URL .'/layout/css/screen.css');
		$this->header->addCssFile(FRONTEND_CORE_URL .'/layout/css/print.css', 'print');
		$this->header->addCssFile(FRONTEND_CORE_URL .'/layout/css/ie6.css', 'screen', 'lte IE 6');
		$this->header->addCssFile(FRONTEND_CORE_URL .'/layout/css/ie7.css', 'screen', 'IE 7');

		// add jQuery (this is default)
		$this->header->addJsFile(FRONTEND_CORE_URL .'/js/jquery/jquery-1.2.6.min.js', false);

		// add meta information
		$this->header->setPageTitle($this->aPageRecord['meta_pagetitle'], ($this->aPageRecord['meta_pagetitle_overwrite'] == 'Y') ? true : false);
		$this->header->setMetaKeywords($this->aPageRecord['meta_keywords'], ($this->aPageRecord['meta_keywords_overwrite'] == 'Y') ? true : false);
		$this->header->setMetaDescription($this->aPageRecord['meta_description'], ($this->aPageRecord['meta_description_overwrite'] == 'Y') ? true : false);
		$this->header->setMetaCustom($this->aPageRecord['meta_custom']);

		// create and set breadcrumb instance
		$this->breadcrumb = new FrontendBreadcrumb();
		Spoon::setObjectReference('breadcrumb', $this->breadcrumb);

		// create navigation instance
		$this->navigation = new FrontendNavigation();

		// create footer instance
		$this->footer = new FrontendFooter();

		// create body instance
		$this->body = new FrontendBody();

		// set body properties
		$this->body->setTitle($this->aPageRecord['title']);
		$this->body->setContent($this->aPageRecord['content']);

		// create PageExtra instance if needed
		if($this->aPageRecord['extra_module'] != '')
		{
			// create extra instance
			$this->extra = new FrontendExtra($this->aPageRecord['extra_action'], $this->aPageRecord['extra_module'], $this->aPageRecord['extra_parameters']);
			Spoon::setObjectReference('extra', $this->extra);
		}
	}


	/**
	 * Store the temporary statistics
	 *
	 * @return	void
	 */
	private function storeStatistics()
	{
		// get cookieId
		if(SpoonCookie::exists('cookie_id')) $cookieId = SpoonCookie::get('cookie_id');

		// cookie doesnt exist
		else
		{
			// create cookieId
			$cookieId = md5(SpoonSession::getSessionId());

			// attempt to set cookie
			try
			{
				SpoonCookie::set('cookie_id', $cookieId, (7 * 24 * 60 * 60), '/', '.'. $this->url->getDomain());
			}

			// failed setting cookie
			catch (Exception $e)
			{
				if(substr_count($e->getMessage(), 'could not be set.') == 0) throw $e;
			}
		}

		// create array
		$aStatistics['status_code'] = (int) $this->statusCode;
		$aStatistics['date'] = date('Y-m-d H:i:s');
		$aStatistics['ip'] =  SpoonHTTP::getIp();
		$aStatistics['session_id'] = SpoonSession::getSessionId();
		$aStatistics['cookie_id'] = $cookieId;
		$aStatistics['browser_name'] = 'unknown';
		$aStatistics['browser_version'] = 0;
		$aStatistics['platform'] = 'unknown';

		// override browser info if browscap is available
		if(ini_get('browscap') !== false)
		{
			$aBrowserInfo = get_browser(null, true);
			$aStatistics['browser_name'] = isset($aBrowserInfo['browser']) ? $aBrowserInfo['browser'] : 'unknown';
			$aStatistics['browser_version'] = isset($aBrowserInfo['version']) ? $aBrowserInfo['version'] : 0;
			$aStatistics['platform'] = isset($aBrowserInfo['platform']) ? $aBrowserInfo['platform'] : 'unknown';
		}

		// url info
		$aStatistics['referrer_url'] = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : null;
		$aStatistics['url'] = trim('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], '/');

		// log to file
		SpoonFile::setContent(FRONTEND_CACHE_PATH .'/statistics/temp.txt', serialize($aStatistics) ."\n", true);
	}
}

?>