<?php

namespace ForkCMS\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use ForkCMS\Utility\Module\CopyModulesToOtherLocale;

class CopyModulesToOtherLocalePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('fork.manager.copy_modules_to_other_locale')) {
            return;
        }

        $definition = $container->findDefinition('fork.manager.copy_modules_to_other_locale');

        // find all service IDs with the fork.copy_module_content_to_other_locale tag
        $taggedServices = $container->findTaggedServiceIds('fork.copy_module_content_to_other_locale');

        foreach ($taggedServices as $id => $tags) {
            // add the module service to the CopyModuleContentToOtherLocale service
            $definition->addMethodCall('addModule', array(new Reference($id)));
        }
    }
}
