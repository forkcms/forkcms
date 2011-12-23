<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the livesuggest-action, it will output a list of results for a certain search
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class FrontendSearchAjaxLivesuggest extends FrontendBaseAJAXAction
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
	 * Display
	 */
	private function display()
	{
		// set variables
		$this->requestedPage = 1;
		$this->limit = (int) FrontendModel::getModuleSetting('search', 'overview_num_items', 20);
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

		// output
		$this->output(self::OK, $this->tpl->getContent(FRONTEND_PATH . '/modules/search/layout/templates/results.tpl', false, true));
	}

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->validateForm();
		$this->display();
	}

	/**
	 * Load the cached data
	 * @todo	refactor me
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

		// set info
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
	 * Load the template
	 */
	protected function loadTemplate()
	{
		// create template
		$this->tpl = new FrontendTemplate(false);
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
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
	 * Parse pagination
	 */
	protected function parsePagination()
	{
		// init var
		$pagination = null;
		$showFirstPages = false;
		$showLastPages = false;
		$useQuestionMark = true;

		// validate pagination array
		if(!isset($this->pagination['limit'])) throw new FrontendException('no limit in the pagination-property.');
		if(!isset($this->pagination['offset'])) throw new FrontendException('no offset in the pagination-property.');
		if(!isset($this->pagination['requested_page'])) throw new FrontendException('no requested_page available in the pagination-property.');
		if(!isset($this->pagination['num_items'])) throw new FrontendException('no num_items available in the pagination-property.');
		if(!isset($this->pagination['num_pages'])) throw new FrontendException('no num_pages available in the pagination-property.');
		if(!isset($this->pagination['url'])) throw new FrontendException('no URL available in the pagination-property.');

		// should we use a questionmark or an ampersand
		if(mb_strpos($this->pagination['url'], '?') > 0) $useQuestionMark = false;

		// no pagination needed
		if($this->pagination['num_pages'] < 1) return;

		// populate count fields
		$pagination['num_pages'] = $this->pagination['num_pages'];
		$pagination['current_page'] = $this->pagination['requested_page'];

		// as long as we are below page 5 we should show all pages starting from 1
		if($this->pagination['requested_page'] < 6)
		{
			// init vars
			$pagesStart = 1;
			$pagesEnd = ($this->pagination['num_pages'] >= 6) ? 6 : $this->pagination['num_pages'];

			// show last pages
			if($this->pagination['num_pages'] > 5) $showLastPages = true;
		}

		// as long as we are 5 pages from the end we should show all pages till the end
		elseif($this->pagination['requested_page'] >= ($this->pagination['num_pages'] - 4))
		{
			// init vars
			$pagesStart = ($this->pagination['num_pages'] - 5);
			$pagesEnd = $this->pagination['num_pages'];

			// show first pages
			if($this->pagination['num_pages'] > 5) $showFirstPages = true;
		}

		// page 7
		else
		{
			// init vars
			$pagesStart = $this->pagination['requested_page'] - 2;
			$pagesEnd = $this->pagination['requested_page'] + 2;
			$showFirstPages = true;
			$showLastPages = true;
		}

		// show previous
		if($this->pagination['requested_page'] > 1)
		{
			// build URL
			if($useQuestionMark) $URL = $this->pagination['url'] . '?page=' . ($this->pagination['requested_page'] - 1);
			else $URL = $this->pagination['url'] . '&amp;page=' . ($this->pagination['requested_page'] - 1);

			// set
			$pagination['show_previous'] = true;
			$pagination['previous_url'] = $URL;
		}

		// show first pages?
		if($showFirstPages)
		{
			// init var
			$pagesFirstStart = 1;
			$pagesFirstEnd = 1;

			// loop pages
			for($i = $pagesFirstStart; $i <= $pagesFirstEnd; $i++)
			{
				// build URL
				if($useQuestionMark) $URL = $this->pagination['url'] . '?page=' . $i;
				else $URL = $this->pagination['url'] . '&amp;page=' . $i;

				// add
				$pagination['first'][] = array('url' => $URL, 'label' => $i);
			}
		}

		// build array
		for($i = $pagesStart; $i <= $pagesEnd; $i++)
		{
			// init var
			$current = ($i == $this->pagination['requested_page']);

			// build URL
			if($useQuestionMark) $URL = $this->pagination['url'] . '?page=' . $i;
			else $URL = $this->pagination['url'] . '&amp;page=' . $i;

			// add
			$pagination['pages'][] = array('url' => $URL, 'label' => $i, 'current' => $current);
		}

		// show last pages?
		if($showLastPages)
		{
			// init var
			$pagesLastStart = $this->pagination['num_pages'];
			$pagesLastEnd = $this->pagination['num_pages'];

			// loop pages
			for($i = $pagesLastStart; $i <= $pagesLastEnd; $i++)
			{
				// build URL
				if($useQuestionMark) $URL = $this->pagination['url'] . '?page=' . $i;
				else $URL = $this->pagination['url'] . '&amp;page=' . $i;

				// add
				$pagination['last'][] = array('url' => $URL, 'label' => $i);
			}
		}

		// show next
		if($this->pagination['requested_page'] < $this->pagination['num_pages'])
		{
			// build URL
			if($useQuestionMark) $URL = $this->pagination['url'] . '?page=' . ($this->pagination['requested_page'] + 1);
			else $URL = $this->pagination['url'] . '&amp;page=' . ($this->pagination['requested_page'] + 1);

			// set
			$pagination['show_next'] = true;
			$pagination['next_url'] = $URL;
		}

		// multiple pages
		$pagination['multiple_pages'] = ($pagination['num_pages'] == 1) ? false : true;

		// assign pagination
		$this->tpl->assign('pagination', $pagination);
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		// set search term
		$this->term = SpoonFilter::getPostValue('term', null, '');

		// validate
		if($this->term == '') $this->output(self::BAD_REQUEST, null, 'term-parameter is missing.');
	}
}
