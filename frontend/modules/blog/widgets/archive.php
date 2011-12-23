<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with the link to the archive
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendBlogWidgetArchive extends FrontendBaseWidget
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
		// we will cache this widget for 15minutes
		$this->tpl->cache(FRONTEND_LANGUAGE . '_blogWidgetArchiveCache', (24 * 60 * 60));

		// if the widget isn't cached, assign the variables
		if(!$this->tpl->isCached(FRONTEND_LANGUAGE . '_blogWidgetArchiveCache'))
		{
			// get the numbers
			$this->tpl->assign('widgetBlogArchive', FrontendBlogModel::getArchiveNumbers());
		}
	}
}
