<?php

namespace Backend\Modules\Mailmotor\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MailmotorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        try {
            // We have the service forkcms.settings and it's not empty
            if ($container->has('forkcms.settings') && !is_a($container->get('forkcms.settings'), 'stdClass')) {
                // we must set these parameters to be usable
                $container->setParameter(
                    'mailmotor.mail_engine',
                    $container->get('forkcms.settings')->get('Mailmotor', 'mail_engine', 'not_implemented')
                );
                $container->setParameter(
                    'mailmotor.api_key',
                    $container->get('forkcms.settings')->get('Mailmotor', 'api_key')
                );
                $container->setParameter(
                    'mailmotor.list_id',
                    $container->get('forkcms.settings')->get('Mailmotor', 'list_id')
                );
            } else {
                // When in fork cms installer, we don't have the service forkcms.settings
                // but we must set the parameters
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
