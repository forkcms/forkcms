<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with recent comments on all events-articles
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendEventsWidgetRecentComments extends FrontendBaseWidget
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
		// assign comments
		$this->tpl->assign('widgetEventsRecentComments', FrontendEventsModel::getRecentComments(5));
	}
}
