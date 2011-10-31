<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with the tags
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendTagsWidgetTagcloud extends FrontendBaseWidget
{
	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->parse();
	}

	/**
	 * Parse
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
