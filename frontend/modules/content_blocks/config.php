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
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendContentBlocksConfig extends FrontendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'detail';

	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();
}
