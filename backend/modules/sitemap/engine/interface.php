<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the interface a module needs to implement so we the sitemap can fetch
 * pages for that specific module
 *
 * @author Jonas Goderis <jonas.goderis@wijs.be>
 */
interface BackendSitemapInterface
{
	public static function getSitemap($language);
	public static function getImageSitemap($language);
}