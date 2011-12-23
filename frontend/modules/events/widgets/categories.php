<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with the events-categories
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendEventsWidgetCategories extends FrontendBaseWidget
{
	/**
	 * Execute the extra
	 */
	public function execute()
	{
		// call parent
		parent::execute();

		// load template
		$this->loadTemplate();

		// parse
		$this->parse();
	}

	/**
	 * Parse
	 */
	private function parse()
	{
		// get categories
		$categories = FrontendEventsModel::getAllCategories();

		// build link
		$link = FrontendNavigation::getURLForBlock('events', 'category');

		// any categories?
		if(!empty($categories))
		{
			// loop and reset url
			foreach($categories as &$row) $row['url'] = $link . '/' . $row['url'];
		}

		// assign comments
		$this->tpl->assign('widgetEventsCategories', $categories);
	}
}
