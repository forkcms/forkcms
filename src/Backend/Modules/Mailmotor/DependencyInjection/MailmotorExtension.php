<?php

namespace Backend\Modules\Mailmotor\DependencyInjection;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MailmotorExtension extends Extension implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // we have the service fork.settings and it's not empty
        if ($container->has('fork.settings') && !is_a($container->get('fork.settings'), 'stdClass')) {
            // we must set these parameters to be usable
            $container->setParameter(
                'mailmotor.mail_engine',
                $container->get('fork.settings')->get('Mailmotor', 'mail_engine')
            );
            $container->setParameter(
                'mailmotor.api_key',
                $container->get('fork.settings')->get('Mailmotor', 'api_key')
            );
            $container->setParameter(
                'mailmotor.list_id',
                $container->get('fork.settings')->get('Mailmotor', 'list_id')
            );
        // when in fork cms installer, we don't have the service fork.settings
        // but we must set the parameters
        } else {
            // we must set these parameters to be usable
            $container->setParameter('mailmotor.mail_engine', 'not_implemented');
            $container->setParameter('mailmotor.api_key', null);
            $container->setParameter('mailmotor.list_id', null);
        }
    }
}
