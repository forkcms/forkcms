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
 * @author Matthias Mullie <matthias.mullie@netlash.com>
 * @author Dave Lens <dave.lens@netlash.com>
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

		/*
		 * A bit dirty this; we overwrite the navigation template path of the FrontendNavigation
		 * by a separate template for the sitemap.
		 */
		$widgetLayoutPath = FRONTEND_MODULES_PATH . '/pages/layout';
		$originalTemplatePath = FrontendNavigation::getTemplatePath();
		FrontendNavigation::setTemplatePath(FrontendTheme::getPath($widgetLayoutPath . '/templates/sitemap.tpl'));

		/*
		 * Because the scope of the template is now changed to the new sitemap.tpl, we can
		 * store the HTML of the new, parsed scope. Afterwards we reset to the original
		 * template (FrontendNavigation might be used again after this).
		 */
		$sitemapNavigationHTML = $this->tpl->getContent(FrontendTheme::getPath($widgetLayoutPath . '/widgets/sitemap.tpl'));
		FrontendNavigation::setTemplatePath($originalTemplatePath);

		return $sitemapNavigationHTML;
	}
}
