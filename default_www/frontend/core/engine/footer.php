<?php

/**
 * This class will be used to alter the footer-part of the HTML-document that will be created by the frontend.
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
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

		// initial value for footer HTML
		$siteHTMLFooter = (string) FrontendModel::getModuleSetting('core', 'site_html_footer', null);

		// facebook admins given?
		if(FrontendModel::getModuleSetting('core', 'facebook_admin_ids', null) !== null)
		{
			// add Facebook container
			$siteHTMLFooter .= "\n" . '<div id="fb-root"></div>';
			// add facebook JS
			$siteHTMLFooter .= "\n" . '<script src="http://connect.facebook.net/'. strtolower(FRONTEND_LANGUAGE) .'_'. strtoupper(FRONTEND_LANGUAGE) .'/all.js"></script>';
		}

		// assign site wide html
		$this->tpl->assign('siteHTMLFooter', $siteHTMLFooter);
	}
}

?>