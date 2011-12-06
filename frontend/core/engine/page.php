<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Frontend page class, this class will handle everything on a page
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <matthias@mullie.eu>
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

	public function __construct()
	{
		parent::__construct();

		// add reference
		Spoon::set('page', $this);

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
		$this->tpl->assign('isPage' . $this->pageId, true);

		// fetch variables from main template
		$mainVariables = $this->tpl->getAssignedVariables();

		// loop all positions
		foreach($this->record['positions'] as $position => &$blocks)
		{
			// loop all blocks in this position
			foreach($blocks as &$block)
			{
				// check for extra's that need to be reparsed
				if(isset($block['extra']))
				{
					// fetch extra-specific variables
					$extraVariables = $block['extra']->getTemplate()->getAssignedVariables();

					// assign all main variables
					$block['extra']->getTemplate()->assignArray($mainVariables);

					// overwrite with all specific variables
					$block['extra']->getTemplate()->assignArray($extraVariables);

					// parse extra
					$block = array('blockIsHTML' => false,
									'blockContent' => $block['extra']->getContent());
				}
			}

			// assign position to template
			$this->tpl->assign('position' . ucfirst($position), $blocks);
		}

		// assign empty positions
		$unusedPositions = array_diff($this->record['template_data']['names'], array_keys($this->record['positions']));
		foreach($unusedPositions as $position) $this->tpl->assign('position' . ucfirst($position), array());

		// only overwrite when status code is 404
		if($this->statusCode == 404) SpoonHTTP::setHeadersByCode(404);

		// output
		$this->tpl->display($this->templatePath, false, true);
	}

	/**
	 * Get the current pageid
	 *
	 * @return int
	 */
	public static function getCurrentPageId()
	{
		return self::$currentPageId;
	}

	/**
	 * Get page content
	 */
	public function getPageContent()
	{
		// load revision
		if($this->URL->getParameter('page_revision', 'int') != 0)
		{
			// get data
			$this->record = FrontendModel::getPageRevision($this->URL->getParameter('page_revision', 'int'));

			// add no-index to meta-custom, so the draft won't get accidentally indexed
			$this->header->addMetaData(array('name' => 'robots', 'content' => 'noindex, nofollow'), true);
		}

		// get page record
		else $this->record = (array) FrontendModel::getPage($this->pageId);

		// empty record (pageId doesn't exists, hope this line is never used)
		if(empty($this->record) && $this->pageId != 404) SpoonHTTP::redirect(FrontendNavigation::getURL(404), 404);

		// init var
		$redirect = true;

		// loop blocks, if all are empty we should redirect to the first child
		foreach($this->record['positions'] as $position => $blocks)
		{
			// loop blocks in position
			foreach($blocks as $block)
			{
				// HTML provided?
				if($block['html'] != '') $redirect = false;

				// an decent extra provided?
				if($block['extra_type'] == 'block') $redirect = false;

				// a widget provided
				if($block['extra_type'] == 'widget') $redirect = false;
			}
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
	 * Fetch the statuscode for the current page.
	 *
	 * @return int
	 */
	public function getStatusCode()
	{
		return $this->statusCode;
	}

	/**
	 * Parse the languages
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
	 */
	private function processPage()
	{
		// set pageTitle
		$this->header->setPageTitle($this->record['meta_title'], (bool) ($this->record['meta_title_overwrite'] == 'Y'));

		// set meta-data
		$this->header->addMetaDescription($this->record['meta_description'], (bool) ($this->record['meta_description_overwrite'] == 'Y'));
		$this->header->addMetaKeywords($this->record['meta_keywords'], ($this->record['meta_keywords_overwrite'] == 'Y'));
		$this->header->setMetaCustom($this->record['meta_custom']);

		// advanced SEO-attributes
		if(isset($this->record['meta_data']['seo_index'])) $this->header->addMetaData(array('name' => 'robots', 'content' => $this->record['meta_data']['seo_index']));
		if(isset($this->record['meta_data']['seo_follow'])) $this->header->addMetaData(array('name' => 'robots', 'content' => $this->record['meta_data']['seo_follow']));

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
		foreach($this->record['positions'] as $position => &$blocks)
		{
			// position not known in template = skip it
			if(!in_array($position, $this->record['template_data']['names'])) continue;

			// loop blocks in position
			foreach($blocks as $index => &$block)
			{
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
						else $block = array('extra' => $extra);

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

						// add to list of blocks
						$block = array('extra' => $extra);

						// assign the variables from this widget to the main template
						$this->tpl->assignArray((array) $extra->getTemplate()->getAssignedVariables());
					}
				}

				// the block only contains HTML
				else
				{
					$block = array(
						'blockIsHTML' => true,
						'blockContent' => $block['html']
					);
				}
			}
		}
	}

	/**
	 * Store the data for statistics
	 */
	private function storeStatistics()
	{
		// @later save temp statistics data here.
	}
}
