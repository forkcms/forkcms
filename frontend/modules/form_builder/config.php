<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the configuration-object
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class FrontendFormBuilderConfig extends FrontendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'form';

	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();
}
