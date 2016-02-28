<?php

namespace Backend\Modules\MailMotor\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CustomCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        // we must set these parameters to be usable
        $container->setParameter(
            'mailmotor.mail_engine',
            $container->get('fork.settings')->get('MailMotor', 'mail_engine')
        );
        $container->setParameter(
            'mailmotor.api_key',
            $container->get('fork.settings')->get('MailMotor', 'api_key')
        );
        $container->setParameter(
            'mailmotor.list_id',
            $container->get('fork.settings')->get('MailMotor', 'list_id')
        );
    }
}
