<?php

/**
 * This is the category-action
 *
 * @package		frontend
 * @subpackage	faq
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @since		2.1
 */
class FrontendFaqCategory extends FrontendBaseBlock
{
	/**
	 * The questions
	 *
	 * @var	array
	 */
	private $questions;


	/**
	 * The category
	 *
	 * @var	array
	 */
	private $record;


	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call the parent
		parent::execute();

		// hide contenTitle, in the template the title is wrapped with an inverse-option
		$this->tpl->assign('hideContentTitle', true);

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
		// validate incoming parameters
		if($this->URL->getParameter(1) === null) $this->redirect(FrontendNavigation::getURL(404));

		// get by URL
		$this->record = FrontendFaqModel::getCategory($this->URL->getParameter(1));

		// anything found?
		if(empty($this->record)) $this->redirect(FrontendNavigation::getURL(404));

		// overwrite URLs
		$this->record['full_url'] = FrontendNavigation::getURLForBlock('faq', 'category') . '/' . $this->record['url'];

		// get questions
		$this->questions = FrontendFaqModel::getAllForCategory($this->record['id']);
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// add into breadcrumb
		$this->breadcrumb->addElement($this->record['title']);

		// set meta
		$this->header->setPageTitle($this->record['title']);

		// assign category and questions
		$this->tpl->assign('category', $this->record);
		$this->tpl->assign('questions', $this->questions);
	}
}

?>