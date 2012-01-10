<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the configuration-object for the sitemap module
 *
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
final class BackendSitemapConfig extends BackendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'settings';

	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();
}
