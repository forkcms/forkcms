<?php

namespace ForkCMS\Bundle\CoreBundle\DependencyInjection\Compiler;

use ForkCMS\Utility\Module\CopyContentToOtherLocale\CopyContentFromModulesToOtherLocaleManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CopyModulesToOtherLocalePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // always first check if the primary service is defined
        if (!$container->has(CopyContentFromModulesToOtherLocaleManager::class)) {
            return;
        }

        $definition = $container->findDefinition(CopyContentFromModulesToOtherLocaleManager::class);

        // find all service IDs with the fork.copy_module_to_other_locale tag
        $taggedServices = $container->findTaggedServiceIds('fork.copy_module_to_other_locale');

        foreach ($taggedServices as $id => $tags) {
            // add the module service to the CopyModuleContentToOtherLocale service
            $definition->addMethodCall('addModule', array(new Reference($id)));
        }
    }
}
