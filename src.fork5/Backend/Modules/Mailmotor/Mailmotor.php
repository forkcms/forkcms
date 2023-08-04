<?php

namespace Backend\Modules\Mailmotor;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Backend\Modules\Mailmotor\DependencyInjection\Compiler\MailmotorCompilerPass;

class Mailmotor extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new MailmotorCompilerPass());
    }
}
