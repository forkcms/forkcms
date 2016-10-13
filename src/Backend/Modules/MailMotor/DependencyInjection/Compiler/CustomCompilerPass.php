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
        // we have the service fork.settings
        if ($container->has('fork.settings')) {
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
        // when in fork cms installer, we don't have the service fork.settings
        // but we must set the parameters
        } else {
            // we must set these parameters to be usable
            $container->setParameter('mailmotor.mail_engine', null);
            $container->setParameter('mailmotor.api_key', null);
            $container->setParameter('mailmotor.list_id', null);
        }
    }
}
