<?php

/**
 * FrontendSearchIndex
 * This is the overview-action
 *
 * @package		frontend
 * @subpackage	search
 *
 * @author 		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class FrontendSearchIndex extends FrontendBaseBlock
{
	/**
	 * The items
	 *
	 * @var	array
	 */
	private $items = array();


	/**
	 * The pagination array
	 * It will hold all needed parameters, some of them need initialization.
	 *
	 * @var	array
	 */
	protected $pagination = array('limit' => 20, 'offset' => 0, 'requested_page' => 1, 'num_items' => null, 'num_pages' => null);


	/**
	 * The search term
	 *
	 * @var String
	 */
	private $term = '';


	/**
	 * Search statistics
	 *
	 * @var	array
	 */
	private $stats = array();


	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call the parent
		parent::execute();

		// load template
		$this->loadTemplate();

		// load form
		$this->loadForm();

		// validate form
		$this->validate();

		// display
		$this->display();

		// save statistics
		$this->saveStats();
	}


	/**
	 * Display
	 *
	 * @return	void
	 */
	private function display()
	{
		// requested page
		$this->requestedPage = $this->URL->getParameter('page', 'int');

		// no page given
		if($this->requestedPage === null) $this->requestedPage = 1;

		// cache name
		$cacheName = FRONTEND_LANGUAGE .'_searchTermCache_'. md5($this->term) . '_'. $this->requestedPage;

		// assign cache
		$this->tpl->assign('cacheName', $cacheName);

		// set cache directory
		$this->tpl->setCacheDirectory(FRONTEND_CACHE_PATH .'/cached_templates');

		// we will cache this result for 15 minutes
		$this->tpl->cache($cacheName, SPOON_DEBUG ? 10 : (15 * 60));

		// if the widget isn't cached, assign the variables
		if(!$this->tpl->isCached($cacheName))
		{
			// load the data
			$this->getData();

			// parse
			$this->parse();
		}
	}


	/**
	 * Load the data, don't forget to validate the incoming data
	 *
	 * @return	void
	 */
	private function getData()
	{
		// no search term = no search
		if(!$this->term) return;

		// get amount of results
		$this->stats['num_results'] = FrontendSearchModel::getTotal($this->term);

		// set url
		$this->pagination['url'] = FrontendNavigation::getURLForBlock('search') . '?form=search&q=test';
		$this->pagination['limit'] = FrontendModel::getModuleSetting('search', 'overview_num_items', 20);

		// populate count fields in pagination
		$this->pagination['num_items'] = $this->stats['num_results'];
		$this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

		// num pages is always equal to at least 1
		if($this->pagination['num_pages'] == 0) $this->pagination['num_pages'] = 1;

		// redirect if the request page doesn't exist
		if($this->requestedPage > $this->pagination['num_pages'] || $this->requestedPage < 1) $this->redirect(FrontendNavigation::getURL(404));

		// populate calculated fields in pagination
		$this->pagination['requested_page'] = $this->requestedPage;
		$this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

		// get articles
		$this->items = FrontendSearchModel::search($this->term, $this->pagination['limit'], $this->pagination['offset']);
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new FrontendForm('search', null, 'get', null, false);

		// create elements
		$this->frm->addText('q');
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse the form
		$this->frm->parse($this->tpl);

		// no search term = no search
		if(!$this->term) return;

		// loop items
		foreach($this->items as &$item)
		{
			// full url is set?
			if(!isset($item['full_url'])) continue;

			// build utm array
			$utm['utm_source'] = SpoonFilter::urlise(FrontendModel::getModuleSetting('core', 'site_title_'. FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE));
			$utm['utm_medium'] = 'fork-search';
			$utm['utm_term'] = $this->term;

			// get parameters in url already
			if(strpos($item['full_url'], '?') !== false) $glue = '&amp;';
			else $glue = '?';

			// add utm to url
			$item['full_url'] .= $glue . http_build_query($utm, '', '&amp;');
		}

		// assign articles
		$this->tpl->assign('searchResults', $this->items);
		$this->tpl->assign('searchTerm', $this->term);

		// parse the pagination
		$this->parsePagination();
	}


	/**
	 * Save statistics
	 *
	 * @return	void
	 */
	private function saveStats()
	{
		// don't save?
		if(!isset($this->stats['term'])) return;

		// save data
		FrontendSearchModel::save($this->stats);
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validate()
	{
		// previous search result
		$previousTerm = SpoonSession::exists('searchTerm') ? SpoonSession::get('searchTerm') : '';
		SpoonSession::set('searchTerm', '');

		// is the form submitted
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate required fields
			$this->frm->getField('q')->isFilled(FL::err('TermIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// get search term
				$this->term = $this->frm->getField('q')->getValue();

				// save this term?
				if($previousTerm != $this->term)
				{
					// format data
					$this->stats['term'] = $this->term;
					$this->stats['language'] = FRONTEND_LANGUAGE;
					$this->stats['time'] = FrontendModel::getUTCDate();
					$this->stats['data'] = serialize(array('server' => $_SERVER));
				}

				// save in cookie
				SpoonSession::set('searchTerm', $this->term);
			}
		}
	}
}

?>