<?php

/**
 * This is the index-action
 *
 * @package		frontend
 * @subpackage	faq
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.1
 */
class FrontendFaqIndex extends FrontendBaseBlock
{
	/**
	 * The questions
	 *
	 * @var	array
	 */
	private $items = array();


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
		// get categories
		$categories = FrontendFaqModel::getCategories();

		// get limit per category
		$limit = FrontendModel::getModuleSetting('faq', 'overview_num_items_per_category', 10);

		// loop categories
		foreach($categories as $item)
		{
			// get the questions
			$item['questions'] = FrontendFaqModel::getAllForCategory($item['id'], $limit);

			// no questions? next!
			if(empty($item['questions'])) continue;

			// add the category item including the questions
			$this->items[] = $item;
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