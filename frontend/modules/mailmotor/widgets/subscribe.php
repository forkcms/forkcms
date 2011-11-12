<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with the subscribe form
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class FrontendMailmotorWidgetSubscribe extends FrontendBaseWidget
{
	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
	}
}
