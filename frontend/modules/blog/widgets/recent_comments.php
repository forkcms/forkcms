<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with recent comments on all blog-articles
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendBlogWidgetRecentComments extends FrontendBaseWidget
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
		$this->tpl->assign('widgetBlogRecentComments', FrontendBlogModel::getRecentComments(5));
	}
}
