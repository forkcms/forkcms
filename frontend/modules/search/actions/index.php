<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will display a form to search
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class FrontendSearchIndex extends FrontendBaseBlock
{
	/**
	 * Name of the cachefile
	 *
	 * @var	string
	 */
	private $cacheFile;

	/**
	 * The items
	 *
	 * @var	array
	 */
	private $items;

	/**
	 * Limit of data to fetch
	 *
	 * @var	int
	 */
	private $limit;

	/**
	 * Offset of data to fetch
	 *
	 * @var	int
	 */
	private $offset;

	/**
	 * The pagination array
	 * It will hold all needed parameters, some of them need initialization.
	 *
	 * @var	array
	 */
	protected $pagination = array('limit' => 20, 'offset' => 0, 'requested_page' => 1, 'num_items' => null, 'num_pages' => null);

	/**
	 * The requested page
	 *
	 * @var	int
	 */
	private $requestedPage;

	/**
	 * The search term
	 *
	 * @var string
	 */
	private $term = '';

	/**
	 * Search statistics
	 *
	 * @var	array
	 */
	private $statistics;

	/**
	 * Display
	 */
	private function display()
	{
		// set variables
		$this->requestedPage = $this->URL->getParameter('page', 'int', 1);
		$this->limit = FrontendModel::getModuleSetting('search', 'overview_num_items', 20);
		$this->offset = ($this->requestedPage * $this->limit) - $this->limit;
		$this->cacheFile = FRONTEND_CACHE_PATH . '/' . $this->getModule() . '/' . FRONTEND_LANGUAGE . '_' . md5($this->term) . '_' . $this->offset . '_' . $this->limit . '.php';

		// load the cached data
		if(!$this->getCachedData())
		{
			// ... or load the real data
			$this->getRealData();
		}

		// parse
		$this->parse();
	}

	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->loadForm();
		$this->validateForm();
		$this->display();
		$this->saveStatistics();
	}

	/**
	 * Load the cached data
	 *
	 * @return bool
	 */
	private function getCachedData()
	{
		// no search term = no search
		if(!$this->term) return false;

		// debug mode = no cache
		if(SPOON_DEBUG) return false;

		// check if cachefile exists
		if(!SpoonFile::exists($this->cacheFile)) return false;

		// get cachefile modification time
		$cacheInfo = @filemtime($this->cacheFile);

		// check if cache file is recent enough (1 hour)
		if(!$cacheInfo || $cacheInfo < strtotime('-1 hour')) return false;

		// include cache file
		require_once $this->cacheFile;

		// set info (received from cache)
		$this->pagination = $pagination;
		$this->items = $items;

		return true;
	}

	/**
	 * Load the data
	 */
	private function getRealData()
	{
		// no search term = no search
		if(!$this->term) return;

		// set url
		$this->pagination['url'] = FrontendNavigation::getURLForBlock('search') . '?form=search&q=' . $this->term;

		// populate calculated fields in pagination
		$this->pagination['limit'] = $this->limit;
		$this->pagination['offset'] = $this->offset;
		$this->pagination['requested_page'] = $this->requestedPage;

		// get items
		$this->items = FrontendSearchModel::search($this->term, $this->pagination['limit'], $this->pagination['offset']);

		// populate count fields in pagination
		// this is done after actual search because some items might be activated/deactivated (getTotal only does rough checking)
		$this->pagination['num_items'] = FrontendSearchModel::getTotal($this->term);
		$this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

		// num pages is always equal to at least 1
		if($this->pagination['num_pages'] == 0) $this->pagination['num_pages'] = 1;

		// redirect if the request page doesn't exist
		if($this->requestedPage > $this->pagination['num_pages'] || $this->requestedPage < 1) $this->redirect(FrontendNavigation::getURL(404));

		// debug mode = no cache
		if(!SPOON_DEBUG)
		{
			// set cache content
			SpoonFile::setContent($this->cacheFile, "<?php\n" . '$pagination = ' . var_export($this->pagination, true) . ";\n" . '$items = ' . var_export($this->items, true) . ";\n?>");
		}
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new FrontendForm('search', null, 'get', null, false);

		// could also have been submitted by our widget
		if(!SpoonFilter::getGetValue('q', null, '')) $_GET['q'] = SpoonFilter::getGetValue('q_widget', null, '');

		// create elements
		$this->frm->addText('q', null, 255, 'inputText liveSuggest autoComplete', 'inputTextError liveSuggest autoComplete');

		// since we know the term just here we should set the canonical url here
		$canonicalUrl = SITE_URL . FrontendNavigation::getURLForBlock('search');
		if(isset($_GET['q']) && $_GET['q'] != '') $canonicalUrl .= '?q=' . $_GET['q'];
		$this->header->setCanonicalUrl($canonicalUrl);
	}

	/**
	 * Parse the data into the template
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
			$utm['utm_source'] = SpoonFilter::urlise(FrontendModel::getModuleSetting('core', 'site_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE));
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
	 */
	private function saveStatistics()
	{
		// no search term = no search
		if(!$this->term) return;

		// previous search result
		$previousTerm = SpoonSession::exists('searchTerm') ? SpoonSession::get('searchTerm') : '';
		SpoonSession::set('searchTerm', '');

		// save this term?
		if($previousTerm != $this->term)
		{
			// format data
			$this->statistics = array();
			$this->statistics['term'] = $this->term;
			$this->statistics['language'] = FRONTEND_LANGUAGE;
			$this->statistics['time'] = FrontendModel::getUTCDate();
			$this->statistics['data'] = serialize(array('server' => $_SERVER));
			$this->statistics['num_results'] = $this->pagination['num_items'];

			// save data
			FrontendSearchModel::save($this->statistics);
		}

		// save current search term in cookie
		SpoonSession::set('searchTerm', $this->term);
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
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
			}
		}
	}
}
