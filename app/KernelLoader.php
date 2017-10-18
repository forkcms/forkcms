<?php

namespace ForkCMS\App;

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
     * @return mixed The service
     */
    public function get(string $reference)
    {
        return $this->getKernel()->getContainer()->get($reference);
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->getKernel()->getContainer();
    }

    /**
     * @return KernelInterface
     */
    public function getKernel(): KernelInterface
    {
        return $this->kernel;
    }

    /**
     * Returns true if the service id is defined.
     *
     * @param string $reference The service id
     *
     * @return bool true if the service id is defined, false otherwise
     */
    public function has(string $reference): bool
    {
        return $this->getKernel()->getContainer()->has($reference);
    }

    /**
     * This is fairly dirty, but so is having static method classes for models.
     * Consider this a temporary solution until we have genuine models available.
     */
    public function passContainerToModels(): void
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
