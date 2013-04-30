<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with most read faq-questions
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class FrontendFaqWidgetMostRead extends FrontendBaseWidget
{
	/**
	 * Execute the extra
	 */
	public function execute()
	{
		// call parent
		parent::execute();

		$this->loadTemplate();
		$this->parse();
	}

	/**
	 * Parse
	 */
	private function parse()
	{
		$this->tpl->assign('widgetFaqMostRead', FrontendFaqModel::getMostRead(FrontendModel::getModuleSetting('faq', 'most_read_num_items', 10)));
	}
}
