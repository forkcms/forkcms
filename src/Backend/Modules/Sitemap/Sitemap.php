<?php

namespace Backend\Modules\Sitemap;

use Backend\Modules\Sitemap\DependencyInjection\Compiler\SitemapProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Sitemap bundle for Fork CMS
 */
class Sitemap extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SitemapProviderPass());
    }
}
