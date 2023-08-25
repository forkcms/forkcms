<?php

namespace ForkCMS\Core\Domain\Kernel\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Core\Domain\Kernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Filesystem\Filesystem;

final class ClearContainerCacheHandler implements CommandHandlerInterface
{
    public function __construct(
        private Kernel $kernel
    ) {
    }

    public function __invoke(ClearContainerCache $clearContainerCache): void
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $command = $application->find('cache:pool:clear');
        $command->run(new ArrayInput([
            'pools' => ['cache.global_clearer']
        ]), new NullOutput());

        $fileSystem = new Filesystem();
        $containerCachePath = $this->kernel->getCacheDir() . '/' . $this->kernel->getContainerClass() . '.php';

        if ($fileSystem->exists($containerCachePath)) {
            $fileSystem->remove($containerCachePath);
        }
    }
}
