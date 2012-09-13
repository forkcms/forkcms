<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action
 *
 * @author Lester Lievens <lester.lievens@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class FrontendFaqIndex extends FrontendBaseBlock
{
	/**
	 * @var	array
	 */
	private $items = array();

	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();

		$this->getData();
		$this->loadTemplate();
		$this->parse();
	}

	/**
	 * Load the data, don't forget to validate the incoming data
	 */
	private function getData()
	{
		$categories = FrontendFaqModel::getCategories();
		$limit = FrontendModel::getModuleSetting('faq', 'overview_num_items_per_category', 10);

		foreach($categories as $item)
		{
			$item['questions'] = FrontendFaqModel::getAllForCategory($item['id'], $limit);

			// no questions? next!
			if(empty($item['questions'])) continue;

			// add the category item including the questions
			$this->items[] = $item;
		}
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		$this->tpl->assign('faqCategories', (array) $this->items);
		$this->tpl->assign('allowMultipleCategories', FrontendModel::getModuleSetting('faq', 'allow_multiple_categories', true));
	}
}
