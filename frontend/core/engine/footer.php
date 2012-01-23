<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will be used to alter the footer-part of the HTML-document that will be created by the frontend.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendFooter extends FrontendBaseObject
{
	public function __construct()
	{
		parent::__construct();

		// store in reference
		Spoon::set('footer', $this);
	}

	/**
	 * Parse the footer into the template
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
		if(FrontendModel::getModuleSetting('core', 'facebook_admin_ids', null) !== null || FrontendModel::getModuleSetting('core', 'facebook_app_id', null) !== null)
		{
			// build correct locale
			switch(FRONTEND_LANGUAGE)
			{
				case 'en':
					$locale = 'en_US';
					break;

				case 'cn':
					$locale = 'zh-CN';
					break;

				default:
					$locale = strtolower(FRONTEND_LANGUAGE) . '_' . strtoupper(FRONTEND_LANGUAGE);
			}

			// add Facebook container
			$siteHTMLFooter .= "\n" . '<div id="fb-root"></div>' . "\n";

			// add facebook JS
			$siteHTMLFooter .= '<script>' . "\n";
			if(FrontendModel::getModuleSetting('core', 'facebook_app_id', null) !== null)
			{
				$siteHTMLFooter .= '	window.fbAsyncInit = function() {' . "\n";
				$siteHTMLFooter .= '		FB.init({ appId: \'' . FrontendModel::getModuleSetting('core', 'facebook_app_id', null) . '\', status: true, cookie: true, xfbml: true, oauth: true });' . "\n";
				$siteHTMLFooter .= '		jsFrontend.facebook.afterInit();' . "\n";
				$siteHTMLFooter .= '	};' . "\n";
			}
			$siteHTMLFooter .= '	(function() {' . "\n";
			$siteHTMLFooter .= '		var e = document.createElement(\'script\'); e.async = true; e.src = document.location.protocol + "//connect.facebook.net/' . $locale . '/all.js#xfbml=1";' . "\n";
			$siteHTMLFooter .= '		document.getElementById(\'fb-root\').appendChild(e);' . "\n";
			$siteHTMLFooter .= '	}());' . "\n";
			$siteHTMLFooter .= '</script>';
		}

		// assign site wide html
		$this->tpl->assign('siteHTMLFooter', $siteHTMLFooter);
	}
}
