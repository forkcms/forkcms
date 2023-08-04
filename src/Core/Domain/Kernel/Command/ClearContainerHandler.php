<?php

namespace ForkCMS\Core\Domain\Kernel\Command;

use ForkCMS\Core\Domain\Kernel\Kernel;
use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use Symfony\Component\Filesystem\Filesystem;

final class ClearContainerHandler implements CommandHandlerInterface
{
    public function __construct(private Kernel $kernel)
    {
    }

    public function __invoke(ClearContainerCache $clearContainerCache): void
    {
        $fileSystem = new Filesystem();
        $containerCachePath = $this->kernel->getCacheDir() . '/' . $this->kernel->getContainerClass() . '.php';

        if ($fileSystem->exists($containerCachePath)) {
            $fileSystem->remove($containerCachePath);
        }
    }
}
