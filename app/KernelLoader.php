<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class is used in several Fork applications to bubble down the AppKernel/Kernel object.
 *
 * @author Dave Lens <dave.lens@wijs.be>
 */
class KernelLoader
{
	/**
	 * @var Kernel
	 */
	protected $kernel;

	/**
	 * @return Kernel
	 */
	public function getKernel()
	{
		return $this->kernel;
	}

	/**
	 * @param Kernel $kernel
	 */
	public function setKernel($kernel = null)
	{
		$this->kernel = $kernel;
	}
}

