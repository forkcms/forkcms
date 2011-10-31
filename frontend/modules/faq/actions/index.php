<?php

/**
 * This is the index-action
 *
 * @author Lester Lievens <lester@netlash.com>
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
	 */
	private function parse()
	{
		$this->tpl->assign('faqCategories', (array) $this->items);
	}
}
