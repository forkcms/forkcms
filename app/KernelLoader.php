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
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class KernelLoader
{
	/**
	 * @var Kernel
	 */
	protected $kernel;

	/**
	 * @param Kernel $kernel
	 */
	public function __construct($kernel)
	{
		$this->setKernel($kernel);
	}

	/**
	 * Gets a service by id.
	 * 
	 * @param string $id The service id
	 * @return object The service
	 */
	public function get($reference)
	{
		return $this->getKernel()->getContainer()->get($reference);
	}

	/**
	 * @return ContainerInterface
	 */
	public function getContainer()
	{
		return $this->getKernel()->getContainer();
	}

	/**
	 * @return Kernel
	 */
	public function getKernel()
	{
		return $this->kernel;
	}

	/**
	 * Returns true if the service id is defined.
	 *
	 * @param string $id The service id
	 * @return Boolean true if the service id is defined, false otherwise
	 */
	public static function has($reference)
	{
		return $this->getKernel()->getContainer()->has($reference);
	}

	/**
	 * This is fairly dirty, but so is having static method classes for models.
	 * Consider this a temporary solution until we have genuine models available.
	 */
	public function passContainerToModels()
	{
		require_once __DIR__ . '/../frontend/core/engine/model.php';
		FrontendModel::setContainer($this->getKernel()->getContainer());

		require_once __DIR__ . '/../backend/core/engine/model.php';
		BackendModel::setContainer($this->getKernel()->getContainer());
	}

	/**
	 * @param Kernel $kernel
	 */
	public function setKernel($kernel = null)
	{
		$this->kernel = $kernel;
	}
}

