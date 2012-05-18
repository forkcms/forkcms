<?php

/**
 * Displays a list of articles of a specific author.
 *
 * @author Jeroen Van den Bossche <jeroen.vandenbossche@wijs.be>
 */
class FrontendBlogAuthor extends FrontendBaseBlock
{
	/**
	 * The articles.
	 *
	 * @var array
	 */
	private $articles;

	/**
	 * The author.
	 *
	 * @var array
	 */
	private $author;

	/**
	 * The pagination array
	 * It will hold all needed parameters, some of them need initialization
	 *
	 * @var array
	 */
	protected $pagination = array('limit' => 10, 'offset' => 0, 'requested_page' => 1, 'num_items' => null, 'num_pages' => null);

	/**
	 * Execute the action.
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->loadData();
		$this->parse();
	}

	/**
	 * Load the data and pagination.
	 */
	private function loadData()
	{
		$slug = $this->URL->getParameter(1, 'string', null);
		$requestedPage = $this->URL->getParameter('page', 'int', 1);

		if($slug === null || !FrontendBlogModel::existsAuthor($slug))
		{
			$this->redirect(FrontendNavigation::getURL(404));
		}

		// set URL and limit
		$this->pagination['url'] = FrontendNavigation::getURLForBlock('blog');
		$this->pagination['limit'] = FrontendModel::getModuleSetting('blog', 'overview_num_items', 10);

		// populate calculated fields in pagination
		$this->pagination['requested_page'] = $requestedPage;
		$this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

		// get articles and the number of articles found.
		list($this->articles, $this->pagination['num_items']) = FrontendBlogModel::getAllForAuthor(
			$slug,
			$this->pagination['limit'],
			$this->pagination['offset']
		);

		// populate count fields in pagination
		$this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

		// num pages is always equal to at least 1
		if($this->pagination['num_pages'] == 0) $this->pagination['num_pages'] = 1;

		// redirect if the request page doesn't exist
		if($requestedPage > $this->pagination['num_pages'] || $requestedPage < 1) $this->redirect(FrontendNavigation::getURL(404));

		$this->author = FrontendBlogModel::getAuthor($slug);
	}

	/**
	 * Parse the page.
	 */
	private function parse()
	{
		// add into breadcrumb
		$this->breadcrumb->addElement(SpoonFilter::ucfirst(FL::lbl('Author')));
		$this->breadcrumb->addElement($this->author->getSetting('nickname'));

		// set pageTitle
		$this->header->setPageTitle(SpoonFilter::ucfirst(FL::lbl('Author')));
		$this->header->setPageTitle($this->author->getSetting('nickname'));

		$this->tpl->assign('articles', $this->articles);
		$this->tpl->assign('authorId', $this->author->getUserId());
		$this->tpl->assign('hideContentTitle', true);
		$this->parsePagination();
	}
}
