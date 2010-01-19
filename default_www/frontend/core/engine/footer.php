<?php

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 * @subpackage	footer
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendFooter extends FrontendBaseObject
{
	/**
	 * Parse the footer into the template
	 *
	 * @return	void
	 */
	public function parse()
	{
		// get footer links
		$aFooterLinks = (array) FrontendNavigation::getFooterLinks();

		// assign footer links
		$this->tpl->assign('iFooterLinks', $aFooterLinks);

		// get site wide html
		$siteWideHtml = (string) FrontendModel::getModuleSetting('core', 'site_wide_html', '');

		// assign site wide html
		$this->tpl->assign('siteWideHtml', $siteWideHtml);
	}
}

?>