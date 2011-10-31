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
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class FrontendLocationIndex extends FrontendBaseBlock
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
	 * Parse the data into the template
	 */
	private function parse()
	{
		// show message
		$this->tpl->assign('locationItems', FrontendLocationModel::getAll());

		// hide form
		$this->tpl->assign('locationSettings', FrontendModel::getModuleSettings('location'));
	}
}
