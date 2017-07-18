<?php

namespace Backend\Modules\Mailmotor\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MailmotorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        try {
            // We have the service fork.settings and it's not empty
            if ($container->has('fork.settings') && !is_a($container->get('fork.settings'), 'stdClass')) {
                // we must set these parameters to be usable
                $container->setParameter(
                    'mailmotor.mail_engine',
                    $container->get('fork.settings')->get('Mailmotor', 'mail_engine', 'not_implemented')
                );
                $container->setParameter(
                    'mailmotor.api_key',
                    $container->get('fork.settings')->get('Mailmotor', 'api_key')
                );
                $container->setParameter(
                    'mailmotor.list_id',
                    $container->get('fork.settings')->get('Mailmotor', 'list_id')
                );
                // When in fork cms installer, we don't have the service fork.settings
                // but we must set the parameters
            } else {
                // we must set these parameters to be usable
                $container->setParameter('mailmotor.mail_engine', 'not_implemented');
                $container->setParameter('mailmotor.api_key', null);
                $container->setParameter('mailmotor.list_id', null);
            }
        } catch (\Exception $e) {
            // this might fail in the test so we have this as fallback
            // we must set these parameters to be usable
            $container->setParameter('mailmotor.mail_engine', 'not_implemented');
            $container->setParameter('mailmotor.api_key', null);
            $container->setParameter('mailmotor.list_id', null);
        }
    }
}
