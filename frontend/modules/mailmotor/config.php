<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use \Symfony\Component\HttpKernel\KernelInterface;

/**
 * This is the configuration-object
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class FrontendMailmotorConfig extends FrontendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'index';

	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();

	/**
	 * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
	 * @param string $module
	 */
	public function __construct(KernelInterface $kernel, $module)
	{
		parent::__construct($kernel, $module);

		$this->loadEngineFiles();
	}

	/**
	 * Loads additional engine files and helpers
	 */
	protected function loadEngineFiles()
	{
		require_once 'engine/helper.php';
		require_once 'engine/mailing_bodybuilder.php';
	}
}
