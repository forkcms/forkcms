<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\KernelInterface;

use Frontend\Core\Engine\Base\Object as FrontendBaseObject;

/**
 * This class will be used to alter the footer-part of the HTML-document that will be created by the frontend.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Footer extends FrontendBaseObject
{
    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $this->getContainer()->set('footer', $this);
    }

    /**
     * Parse the footer into the template
     */
    public function parse()
    {
        $footerLinks = (array) Navigation::getFooterLinks();
        $this->tpl->assign('footerLinks', $footerLinks);

        $siteHTMLFooter = (string) Model::getModuleSetting('Core', 'site_html_footer', null);

        $facebookAdminIds = Model::getModuleSetting('Core', 'facebook_admin_ids', null);
        $facebookAppId = Model::getModuleSetting('Core', 'facebook_app_id', null);

        // facebook admins given?
        if ($facebookAdminIds !== null || $facebookAppId !== null) {
            // build correct locale
            switch (FRONTEND_LANGUAGE) {
                case 'en':
                    $locale = 'en_US';
                    break;

                case 'zh':
                    $locale = 'zh_CN';
                    break;

                case 'cs':
                    $locale = 'cs_CZ';
                    break;

                case 'el':
                    $locale = 'el_GR';
                    break;

                case 'ja':
                    $locale = 'ja_JP';
                    break;

                case 'sv':
                    $locale = 'sv_SE';
                    break;

                case 'uk':
                    $locale = 'uk_UA';
                    break;

                default:
                    $locale = strtolower(FRONTEND_LANGUAGE) . '_' . strtoupper(FRONTEND_LANGUAGE);
            }

            // add Facebook container
            $siteHTMLFooter .= "\n" . '<div id="fb-root"></div>' . "\n";

            // add facebook JS
            $siteHTMLFooter .= '<script>' . "\n";
            if ($facebookAppId !== null) {
                $siteHTMLFooter .= '	window.fbAsyncInit = function() {' . "\n";
                $siteHTMLFooter .= '		FB.init({' . "\n";
                $siteHTMLFooter .= '			appId: "' . $facebookAppId . '",' . "\n";
                $siteHTMLFooter .= '			status: true,' . "\n";
                $siteHTMLFooter .= '			cookie: true,' . "\n";
                $siteHTMLFooter .= '			xfbml: true,' . "\n";
                $siteHTMLFooter .= '			oauth: true' . "\n";
                $siteHTMLFooter .= '		});' . "\n";
                $siteHTMLFooter .= '		jsFrontend.facebook.afterInit();' . "\n";
                $siteHTMLFooter .= '	};' . "\n";
            }

            $siteHTMLFooter .= '	(function(d, s, id){' . "\n";
            $siteHTMLFooter .= '		var js, fjs = d.getElementsByTagName(s)[0];' . "\n";
            $siteHTMLFooter .= '		if (d.getElementById(id)) {return;}' . "\n";
            $siteHTMLFooter .= '		js = d.createElement(s); js.id = id;' . "\n";
            $siteHTMLFooter .= '		js.src = "//connect.facebook.net/' . $locale . '/all.js";' . "\n";
            $siteHTMLFooter .= '		fjs.parentNode.insertBefore(js, fjs);' . "\n";
            $siteHTMLFooter .= '	}(document, \'script\', \'facebook-jssdk\'));' . "\n";
            $siteHTMLFooter .= '</script>';
        }

        // assign site wide html
        $this->tpl->assign('siteHTMLFooter', $siteHTMLFooter);
    }
}
