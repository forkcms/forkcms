<?php

/**
 * This is a widget with the events-categories
 *
 * @package		frontend
 * @subpackage	events
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class FrontendEventsWidgetCategories extends FrontendBaseWidget
{
	/**
	 * Execute the extra
	 *
	 * @return	void
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
	 *
	 * @return	void
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

?>