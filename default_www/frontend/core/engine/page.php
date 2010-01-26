<?php

/**
 * FrontendTemplate, this is our extended version of SpoonTemplate
 *
 * This class will handle a lot of stuff for you, for example:
 * 	- it will assign all labels
 *	- it will map some modifiers
 *  - it will assign a lot of constants
 * 	- ...
 *
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
	 * The path of the template to show
	 *
	 * @var	string
	 */
	private $templatePath;


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
		$this->pageId = FrontendNavigation::getPageId(implode('/', $this->url->getPages()));

		// make the pageId accessible through a static method
		self::$currentPageId = $this->pageId;

		// set headers if this is a 404 page @todo	check me
		if($this->pageId == 404) $this->statusCode = 404;

		// get pagecontent
		$this->getPageContent();

		// process page
		$this->processPage();

		// display
		$this->display();
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

		// parse header
		$this->header->parse();

		// parse breadcrumb
		$this->breadcrumb->parse();

		// parse languages
		$this->parseLanguages();

		// parse footer
		$this->footer->parse();

		// output
		$this->tpl->display(FRONTEND_PATH .'/'. $this->templatePath);
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

		// empty record (pageId doesn't exists)
		if(empty($this->record) && $this->pageId != 404) SpoonHTTP::redirect(FrontendNavigation::getURL(404), 404); // @todo we don't want a redirect

		// @todo indien er geen inhoud is, doorverwijzen naar eerste child. Wordt wel lastig om te verififeren bij 1ste block.
		// redirect als alles leeg is en geen extras
	}


	/**
	 * Parse the languages
	 *
	 * @return	void
	 */
	private function parseLanguages()
	{
		// just execute if the site is multi-language
		if(SITE_MULTILANGUAGE)
		{
			// get languages
			$activeLanguages = FrontendLanguage::getActiveLanguages();

			// init var
			$languages = array();

			// loop active languages
			foreach($activeLanguages as $language)
			{
				// build temp array
				$temp = array();
				$temp['url'] = '/'. $language;
				$temp['label'] = $language;
				$temp['current'] = (bool) ($language == FRONTEND_LANGUAGE);

				// add
				$languages[] = $temp;
			}

			// assign
			$this->tpl->assign('languages', $languages);
		}
	}


	/**
	 * Processes the page
	 *
	 * @return	void
	 */
	private function processPage()
	{
		// create navigation instance
		$this->navigation = new FrontendNavigation();

		// create header instance
		$this->header = new FrontendHeader();

		// set pageTitle
		$this->header->setPageTitle($this->record['meta_title'], (bool) ($this->record['meta_title_overwrite'] == 'Y'));
		$this->header->setMetaDescription($this->record['meta_description'], (bool) ($this->record['meta_description_overwrite'] == 'Y'));
		$this->header->setMetaKeywords($this->record['meta_keywords'], (bool) ($this->record['meta_keywords_overwrite'] == 'Y'));
		$this->header->setMetaCustom($this->record['meta_custom']);

		// create breadcrumb instance
		$this->breadcrumb = new FrontendBreadcrumb();

		// new footer instance
		$this->footer = new FrontendFooter();

		// set template path
		$this->templatePath = $this->record['template_path'];

		// loop blocks
		foreach($this->record['blocks'] as $index => $block)
		{
			// get blockName
			$blockName = (isset($this->record['template_data']['names'][$index])) ? $this->record['template_data']['names'][$index] : null;

			// unknown blockname? skip it
			if($blockName === null) continue;

			// build templateVariable
			$templateVariable = 'block'. SpoonFilter::toCamelCase($blockName, ' ');

			// an extra
			if($block['extra_id'] !== null)
			{
//				Spoon::dump($block);

				throw new FrontendException('Implement me'); // @todo mekker
			}

			// the block only contains HTML
			else
			{
				// assign option
				$this->tpl->assign($templateVariable .'IsHTML', true);

				// assign HTML
				$this->tpl->assign($templateVariable, $block['html']);
			}
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
				if(substr_count($e->getMessage(), 'could not be set.') == 0) throw new FrontendException($e->getMessage());
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
		SpoonFile::setContent(FRONTEND_CACHE_PATH .'/statistics/temp.txt', serialize($aStatistics) ."\n", true, true);
	}
}

?>