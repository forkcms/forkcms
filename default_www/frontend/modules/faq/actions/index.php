<?php

/**
 * This is the index-action
 *
 * @package		frontend
 * @subpackage	faq
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @since		2.1
 */
class FrontendFaqIndex extends FrontendBaseBlock
{
	/**
	 * The questions
	 *
	 * @var	array
	 */
	private $items;


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
		// get questions
		$this->items = FrontendFaqModel::getCategories();

		// go over categories
		foreach($this->items as &$item)
		{
			// add questions info to array
			$item['questions'] = FrontendFaqModel::getQuestions($item['id']);
		}
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// assign questions
		$this->tpl->assign('faqCategories', (array) $this->items);
	}
}

?>