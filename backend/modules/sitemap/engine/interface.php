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
	/**
	 * This method is needed in the module to get the normal sitemap urls for that module
	 *
	 * @param  string $language
	 *
	 * @return array
	 */
	public static function getSitemap($language);

	/**
	 * This method is needed in the module to get the image sitemap urls for that module
	 * It could that the module doesn't use images so you have to implement this method and just
	 * return an empty array
	 *
	 * @param  string $language
	 *
	 * @return array
	 */
	public static function getImageSitemap($language);
}