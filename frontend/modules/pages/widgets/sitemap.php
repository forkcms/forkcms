<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget wherin the sitemap lives
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendPagesWidgetSitemap extends FrontendBaseWidget
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
