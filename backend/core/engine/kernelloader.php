<?php

class BackendKernelLoader
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

