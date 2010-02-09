<?php

/**
 * FrontendSitemapIndex
 *
 * This is the index-action
 *
 * @package		frontend
 * @subpackage	sitemap
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendSitemapIndex extends FrontendBaseBlock
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
	}
}

?>