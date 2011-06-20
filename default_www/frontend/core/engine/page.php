<?php

/**
 * Frontend page class, this class will handle everything on a page
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendPage extends FrontendBaseObject
{
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
	 * Footer instance
	 *
	 * @var	FrontendFooter
	 */
	private $footer;


	/**
	 * Extra instances
	 *
	 * @var	array
	 */
	private $extras;


	/**
	 * Header instance
	 *
	 * @var	FrontendHeader
	 */
	private $header;


	/**
	 * The current pageId
	 *
	 * @var	int
	 */
	private $pageId;


	/**
	 * Content of the page
	 *
	 * @var	array
	 */
	private $record = array();


	/**
	 * The path of the template to show
	 *
	 * @var	string
	 */
	private $templatePath;


	/**
	 * The statuscode
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

		// get pageId for requested URL
		$this->pageId = FrontendNavigation::getPageId(implode('/', $this->URL->getPages()));

		// make the pageId accessible through a static method
		self::$currentPageId = $this->pageId;

		// set headers if this is a 404 page
		if($this->pageId == 404) $this->statusCode = 404;

		// create header instance
		$this->header = new FrontendHeader();

		// get pagecontent
		$this->getPageContent();

		// process page
		$this->processPage();

		// store statistics
		$this->storeStatistics();

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
		// parse header
		$this->header->parse();

		// parse breadcrumb
		$this->breadcrumb->parse();

		// parse languages
		$this->parseLanguages();

		// parse footer
		$this->footer->parse();

		// assign the id so we can use it as an option
		$this->tpl->assign('page' . $this->pageId, true);

		// fetch variables from main template
		$mainVariables = $this->tpl->getAssignedVariables();

		// loop all extras
		foreach((array) $this->extras as $templateVariable => $extra)
		{
			// fetch extra-specific variables
			$extraVariables = $extra->getTemplate()->getAssignedVariables();

			// assign all main variables
			$extra->getTemplate()->assignArray($mainVariables);

			// overwrite with all specific variables
			$extra->getTemplate()->assignArray($extraVariables);

			// parse extra and assign to main template
			$this->tpl->assign($templateVariable, $extra->getContent());
		}

		// only overwrite when status code is 404
		if($this->statusCode == 404) SpoonHTTP::setHeadersByCode(404);

		// output
		$this->tpl->display($this->templatePath, false, true);
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
		// load revision
		if($this->URL->getParameter('page_revision', 'int') != 0)
		{
			// get data
			$this->record = FrontendModel::getPageRevision($this->URL->getParameter('page_revision', 'int'));

			// add no-index to meta-custom, so the draft won't get accidentally indexed
			$this->header->addMetaCustom('<meta name="robots" content="noindex" />');
		}

		// get page record
		else $this->record = (array) FrontendModel::getPage($this->pageId);

		// empty record (pageId doesn't exists, hope this line is never used)
		if((empty($this->record) && $this->pageId != 404) || (!isset($this->record['blocks']))) SpoonHTTP::redirect(FrontendNavigation::getURL(404), 404);

		// init var
		$redirect = true;

		// loop blocks, if all are empty we should redirect to the first child
		foreach($this->record['blocks'] as $block)
		{
			// HTML provided?
			if($block['html'] != '') $redirect = false;

			// an decent extra provided?
			if($block['extra_type'] == 'block') $redirect = false;

			// a widget provided
			if($block['extra_type'] == 'widget') $redirect = false;
		}

		// should we redirect?
		if($redirect)
		{
			// get first child
			$firstChildId = FrontendNavigation::getFirstChildId($this->record['id']);

			// validate the child
			if($firstChildId !== false)
			{
				// build URL
				$URL = FrontendNavigation::getURL($firstChildId);

				// redirect
				SpoonHTTP::redirect($URL, 301);
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
				$temp['url'] = '/' . $language;
				$temp['label'] = $language;
				$temp['name'] = FL::msg(strtoupper($language));
				$temp['current'] = (bool) ($language == FRONTEND_LANGUAGE);

				// add
				$languages[] = $temp;
			}

			// assign
			if(count($languages) > 1) $this->tpl->assign('languages', $languages);
		}
	}


	/**
	 * Processes the page
	 *
	 * @return	void
	 */
	private function processPage()
	{
		// set pageTitle
		$this->header->setPageTitle($this->record['meta_title'], (bool) ($this->record['meta_title_overwrite'] == 'Y'));

		// set meta-data
		$this->header->setMetaDescription($this->record['meta_description'], (bool) ($this->record['meta_description_overwrite'] == 'Y'));
		$this->header->setMetaKeywords($this->record['meta_keywords'], (bool) ($this->record['meta_keywords_overwrite'] == 'Y'));
		$this->header->setMetaCustom($this->record['meta_custom']);

		// create breadcrumb instance
		$this->breadcrumb = new FrontendBreadcrumb();

		// create navigation instance
		new FrontendNavigation();

		// new footer instance
		$this->footer = new FrontendFooter();

		// assign content
		$this->tpl->assign('page', $this->record);

		// set template path
		$this->templatePath = FRONTEND_PATH . '/' . $this->record['template_path'];

		// loop blocks
		foreach($this->record['blocks'] as $index => $block)
		{
			// get blockName
			$blockName = (isset($this->record['template_data']['names'][$index])) ? $this->record['template_data']['names'][$index] : null;

			// unknown blockname? skip it
			if($blockName === null) continue;

			// build templateVariable
			$templateVariable = 'block' . ($index + 1);

			// an extra
			if($block['extra_id'] !== null)
			{
				// block
				if($block['extra_type'] == 'block')
				{
					// create new instance
					$extra = new FrontendBlockExtra($block['extra_module'], $block['extra_action'], $block['extra_data']);

					// execute
					$extra->execute();

					// overwrite the template
					if($extra->getOverwrite()) $this->templatePath = $extra->getTemplatePath();

					// add to list of extras
					else $this->extras[$templateVariable] = $extra;

					// assign the variables from this block to the main template
					$this->tpl->assignArray((array) $extra->getTemplate()->getAssignedVariables());
				}

				// widget
				else
				{
					// create new instance
					$extra = new FrontendBlockWidget($block['extra_module'], $block['extra_action'], $block['extra_data']);

					// fetch data (if available)
					$extra->execute();

					// assign the variables from this widget to the main template
					$this->tpl->assignArray((array) $extra->getTemplate()->getAssignedVariables());

					// add to list of blocks
					$this->extras[$templateVariable] = $extra;
				}
			}

			// the block only contains HTML
			else
			{
				// assign option
				$this->tpl->assign($templateVariable . 'IsHTML', true);

				// assign HTML
				$this->tpl->assign($templateVariable, $block['html']);
			}
		}
	}


	/**
	 * Store the data for statistics
	 *
	 * @return	void
	 */
	private function storeStatistics()
	{
		// @later	save temp statistics data here.
	}
}

?>