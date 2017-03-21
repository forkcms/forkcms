<?php

namespace ForkCMS\App;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Backend\Core\Engine\Model as BackendModel;
use Frontend\Core\Engine\Model as FrontendModel;

/**
 * This class is used in several Fork applications to bubble down the AppKernel/Kernel object.
 */
class KernelLoader
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->setKernel($kernel);
    }

    /**
     * Gets a service by id.
     *
     * @param string $reference The service id
     *
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
     * @param string $reference The service id
     *
     * @return Boolean true if the service id is defined, false otherwise
     */
    public function has($reference)
    {
        return $this->getKernel()->getContainer()->has($reference);
    }

    /**
     * This is fairly dirty, but so is having static method classes for models.
     * Consider this a temporary solution until we have genuine models available.
     */
    public function passContainerToModels()
    {
        FrontendModel::setContainer($this->getKernel()->getContainer());

        BackendModel::setContainer($this->getKernel()->getContainer());
    }

    /**
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel = null)
    {
        $this->kernel = $kernel;
    }
}
