<?php

/**
 * This is a widget with the tags
 *
 * @package		frontend
 * @subpackage	tags
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class FrontendTagsWidgetTagcloud extends FrontendBaseWidget
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
		$tags = FrontendTagsModel::getAll();

		// we just need the 10 first items
		$tags = array_slice($tags, 0, 10);

		// build link
		$link = FrontendNavigation::getURLForBlock('tags', 'detail');

		// any tags?
		if(!empty($tags))
		{
			// loop and reset url
			foreach($tags as &$row) $row['url'] = $link . '/' . $row['url'];
		}

		// assign comments
		$this->tpl->assign('widgetTagsTagCloud', $tags);
	}
}

?>