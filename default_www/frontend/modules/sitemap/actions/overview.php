<?php

/**
 * Sitemap
 *
 * This is the overview-action
 *
 * @package		frontend
 * @subpackage	sitemap
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class SitemapOverview extends FrontendBaseAction
{
	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// load template
		$this->loadTemplate();

		// @todo	implement real code
		$this->tpl->assign('sitemap', '&lt;sitemap-goes-here&gt;');
	}
}
?>