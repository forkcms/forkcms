<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This cronjob will create the sitemap
 *
 * @author Jonas Goderis <jonas.goderis@wijs.be>
 */
class BackendSitemapCronjobCreateSitemap extends BackendBaseCronjob
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// create new sitemap generator
		$sitemap = new BackendSitemapGenerator();
		$sitemap->saveXml();
	}
}
