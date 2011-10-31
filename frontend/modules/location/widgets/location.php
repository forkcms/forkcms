<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the location-widget: 1 specific address
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class FrontendLocationWidgetLocation extends FrontendBaseWidget
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
		$this->tpl->assign('widgetLocationItems', FrontendLocationModel::get((int) $this->data['id']));

		// hide form
		$this->tpl->assign('widgetLocationSettings', FrontendModel::getModuleSettings('location'));
	}
}
