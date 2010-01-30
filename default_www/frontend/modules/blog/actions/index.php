<?php

/**
 * FrontendBlogIndex
 * This is the overview-action
 *
 * @package		frontend
 * @subpackage	blog
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBlogIndex extends FrontendBaseBlock
{
	/**
	 * The articles
	 *
	 * @var	array
	 */
	private $articles;


	/**
	 * The pagination array.
	 * It will hold all needed parameters, some of them need initialization
	 *
	 * @var	array
	 */
	protected $pagination = array('limit' => 1, 'offset' => 0, 'requested_page' => 1, 'item_count' => null, 'pages_count' => null);


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
		$requestedPage = $this->URL->getParameter(0, 'int');

		// no page given
		if($requestedPage === null) $requestedPage = 1;

		// set url
		$this->pagination['url'] = FrontendNavigation::getURLForBlock('blog');

		// populate count fields in pagination
		$this->pagination['item_count'] = FrontendBlogModel::getAllCount();
		$this->pagination['pages_count'] = (int) ceil($this->pagination['item_count'] / $this->pagination['limit']);

		// redirect if the request page doesn't exists
		if($requestedPage > $this->pagination['pages_count'] || $requestedPage < 1) $this->redirect(FrontendNavigation::getURL(404));

		// populate calculated fields in pagination
		$this->pagination['requested_page'] = $requestedPage;
		$this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

		// get articles
		$this->articles = FrontendBlogModel::getAll($this->pagination['limit'], $this->pagination['offset']);

//		Spoon::dump($this->articles);
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// assign articles
		$this->tpl->assign('blogArticles', $this->articles);

		$this->parsePagination();
	}
}
?>