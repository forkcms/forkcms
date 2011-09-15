<?php

/**
 * This is the overview-action
 *
 * @package		frontend
 * @subpackage	events
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @author		Sam Tubbax <sam@sumocoders.be>
 * @since		2.0
 */
class FrontendEventsIndex extends FrontendBaseBlock
{
	/**
	 * The articles
	 *
	 * @var	array
	 */
	private $items;


	/**
	 * The pagination array
	 * It will hold all needed parameters, some of them need initialization.
	 *
	 * @var	array
	 */
	protected $pagination = array('limit' => 10, 'offset' => 0, 'requested_page' => 1, 'num_items' => null, 'num_pages' => null);


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

		// load the data
		$this->getData();

		// parse
		$this->parse();
	}


	/**
	 * Load the data, don't forget to validate the incoming data
	 *
	 * @return	void
	 */
	private function getData()
	{
		// requested page
		$requestedPage = $this->URL->getParameter('page', 'int', 1);

		// set URL and limit
		$this->pagination['url'] = FrontendNavigation::getURLForBlock('events');
		$this->pagination['limit'] = FrontendModel::getModuleSetting('events', 'overview_num_items', 10);

		// populate count fields in pagination
		$this->pagination['num_items'] = FrontendEventsModel::getAllCount();
		$this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

		// num pages is always equal to at least 1
		if($this->pagination['num_pages'] == 0) $this->pagination['num_pages'] = 1;

		// redirect if the request page doesn't exist
		if($requestedPage > $this->pagination['num_pages'] || $requestedPage < 1) $this->redirect(FrontendNavigation::getURL(404));

		// populate calculated fields in pagination
		$this->pagination['requested_page'] = $requestedPage;
		$this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

		//Spoon::dump(FrontendModel::getDB()->getRecords('SELECT * FROM events'));

		// get articles
		$this->items = FrontendEventsModel::getAll($this->pagination['limit'], $this->pagination['offset']);
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// get RSS-link
		$rssLink = FrontendModel::getModuleSetting('events', 'feedburner_url_' . FRONTEND_LANGUAGE);
		if($rssLink == '') $rssLink = FrontendNavigation::getURLForBlock('events', 'rss');

		// add RSS-feed into the metaCustom
		$this->header->addLink(array('rel' => 'alternate', 'type' => 'application/rss+xml',
									 'title' => FrontendModel::getModuleSetting('events', 'rss_title_' . FRONTEND_LANGUAGE),
									'href' => $rssLink));

		// assign articles
		$this->tpl->assign('items', $this->items);

		// parse the pagination
		$this->parsePagination();
	}
}

?>