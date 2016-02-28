<?php

namespace Backend\Modules\MailMotor;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Backend\Modules\MailMotor\DependencyInjection\Compiler\CustomCompilerPass;

class MailMotor extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CustomCompilerPass());
    }
}
