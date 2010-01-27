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
		$this->tpl->display($this->templatePath);
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

		// init var
		$redirect = true;

		// loop blocks, if all are empty we should redirect to the first child
		foreach($this->record['blocks'] as $block)
		{
			// HTML provided?
			if($block['html'] != '') $redirect = false;

			// an decent extra provided?
			if($block['extra_type'] == 'block') $redirect = false;
		}

		// should we redirect?
		if($redirect)
		{
			// get first child
			$firstChildId = FrontendNavigation::getFirstChildId($this->record['id']);

			// validate the child
			if($firstChildId !== false)
			{
				// build url
				$url = FrontendNavigation::getURL($firstChildId);

				// redirect (temporary)
				SpoonHTTP::redirect($url, 307);
			}
		}
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
		$this->templatePath = FRONTEND_PATH .'/'. $this->record['template_path'];

		// assign content
		$this->tpl->assignArray($this->record, 'pageData');

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
				if($block['extra_type'] == 'block')
				{
					// create new instance
					$extra = new FrontendBlockExtra($block['extra_module'], $block['extra_action'], $block['extra_data']);

					// execute
					$extra->execute();

					// overwrite the template
					if($extra->getOverwrite()) $this->templatePath = $extra->getTemplatePath();

					// assign
					else $this->tpl->assign($templateVariable, $extra->getTemplatePath());
				}

				else
				{
					// create new instance
					$widget = new FrontendBlockWidget($block['extra_module'], $block['extra_action'], $block['extra_data']);

					// execute
					$widget->execute();

					// assign
					$this->tpl->assign($templateVariable, $widget->getTemplatePath());
				}
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
	 * @later: implement me
	 *
	 * @return	void
	 */
	private function storeStatistics()
	{
	}
}

?>