<?php

/**
 * This class will be used to alter the footer-part of the HTML-document that will be created by the frontend.
 *
 * @package		frontend
 * @subpackage	core
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
		$footerLinks = (array) FrontendNavigation::getFooterLinks();

		// assign footer links
		$this->tpl->assign('footerLinks', $footerLinks);

		// assign site wide html
		$this->tpl->assign('siteHTMLFooter', (string) FrontendModel::getModuleSetting('core', 'site_html_footer', null));
	}
}

?>