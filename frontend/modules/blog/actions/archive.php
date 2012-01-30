<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the archive-action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendBlogArchive extends FrontendBaseBlock
{
	/**
	 * The articles
	 *
	 * @var	array
	 */
	private $items;

	/**
	 * The dates for the archive
	 *
	 * @var	int
	 */
	private $startDate, $endDate;

	/**
	 * The pagination array
	 * It will hold all needed parameters, some of them need initialization
	 *
	 * @var	array
	 */
	protected $pagination = array('limit' => 10, 'offset' => 0, 'requested_page' => 1, 'num_items' => null, 'num_pages' => null);

	/**
	 * The requested year and month
	 *
	 * @var	int
	 */
	private $year, $month;

	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->getData();
		$this->parse();
	}

	/**
	 * Load the data, don't forget to validate the incoming data
	 */
	private function getData()
	{
		// get parameters
		$this->year = $this->URL->getParameter(1);
		$this->month = $this->URL->getParameter(2);

		// redirect /2010/6 to /2010/06 to avoid duplicate content
		if($this->month !== null && mb_strlen($this->month) != 2)
		{
			$queryString = isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
			$this->redirect(FrontendNavigation::getURLForBlock('knowledgebase', 'archive') . '/' . $this->year . '/' . str_pad($this->month, 2, '0', STR_PAD_LEFT) . $queryString, 301);
		}
		if(mb_strlen($this->year) != 4) $this->redirect(FrontendNavigation::getURL(404));

		// redefine
		$this->year = (int) $this->year;
		if($this->month !== null) $this->month = (int) $this->month;

		// validate parameters
		if($this->year == 0 || $this->month === 0) $this->redirect(FrontendNavigation::getURL(404));

		// requested page
		$requestedPage = $this->URL->getParameter('page', 'int', 1);

		// rebuild url
		$url = $this->year;

		// build timestamp
		if($this->month !== null)
		{
			$this->startDate = gmmktime(00, 00, 00, $this->month, 01, $this->year);
			$this->endDate = gmmktime(23, 59, 59, $this->month, gmdate('t', $this->startDate), $this->year);
			$url .= '/' . str_pad($this->month, 2, '0', STR_PAD_LEFT);
		}

		// year
		else
		{
			$this->startDate = gmmktime(00, 00, 00, 01, 01, $this->year);
			$this->endDate = gmmktime(23, 59, 59, 12, 31, $this->year);
		}

		// set URL and limit
		$this->pagination['url'] = FrontendNavigation::getURLForBlock('blog', 'archive') . '/' . $url;
		$this->pagination['limit'] = FrontendModel::getModuleSetting('blog', 'overview_num_items', 10);

		// populate count fields in pagination
		$this->pagination['num_items'] = FrontendBlogModel::getAllForDateRangeCount($this->startDate, $this->endDate);
		$this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

		// redirect if the request page doesn't exists
		if($requestedPage > $this->pagination['num_pages'] || $requestedPage < 1) $this->redirect(FrontendNavigation::getURL(404));

		// populate calculated fields in pagination
		$this->pagination['requested_page'] = $requestedPage;
		$this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

		// get articles
		$this->items = FrontendBlogModel::getAllForDateRange($this->startDate, $this->endDate, $this->pagination['limit'], $this->pagination['offset']);
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		// get RSS-link
		$rssLink = FrontendModel::getModuleSetting('blog', 'feedburner_url_' . FRONTEND_LANGUAGE);
		if($rssLink == '') $rssLink = FrontendNavigation::getURLForBlock('blog', 'rss');

		// add RSS-feed
		$this->header->addLink(array('rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => FrontendModel::getModuleSetting('blog', 'rss_title_' . FRONTEND_LANGUAGE), 'href' => $rssLink), true);

		// add into breadcrumb
		$this->breadcrumb->addElement(SpoonFilter::ucfirst(FL::lbl('Archive')));
		$this->breadcrumb->addElement($this->year);
		if($this->month !== null) $this->breadcrumb->addElement(SpoonDate::getDate('F', $this->startDate, FRONTEND_LANGUAGE, true));

		// set pageTitle
		$this->header->setPageTitle(SpoonFilter::ucfirst(FL::lbl('Archive')));
		$this->header->setPageTitle($this->year);
		if($this->month !== null) $this->header->setPageTitle(SpoonDate::getDate('F', $this->startDate, FRONTEND_LANGUAGE, true));

		// assign category
		$this->tpl->assign('archive', array('start_date' => $this->startDate, 'end_date' => $this->endDate, 'year' => $this->year, 'month' => $this->month));

		// assign items
		$this->tpl->assign('items', $this->items);

		// parse the pagination
		$this->parsePagination();
	}
}
