<?php

namespace ForkCMS\Modules\Extensions\DependencyInjection;

use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Core\Domain\DependencyInjection\ForkModuleExtension;
use ForkCMS\Modules\Extensions\Domain\Module\InstalledModules;
use ForkCMS\Modules\Extensions\Domain\Theme\ThemeRepository;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionsExtension extends ForkModuleExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $this->getLoader($container)->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->getLoader($container)->load('doctrine.yaml');
        $this->configureLiipImagine($container);
    }

    private function configureLiipImagine(ContainerBuilder $container): void
    {
        $modulesDirectory = $container->getParameter('kernel.project_dir') . '/src/Modules/';
        $dataRoots = [];
        foreach (Application::cases() as $application) {
            $application = ucfirst($application->value);
            foreach (InstalledModules::fromContainer($container)() as $moduleName) {
                $dataRoots[$moduleName . 'Module' . $application] = $modulesDirectory . $moduleName . '/assets/' .
                    $application . '/public';
            }
        }
        foreach (ThemeRepository::getThemePaths() as $themeName => $path) {
            $dataRoots[$themeName . 'Theme'] = $path . '/assets/public';
        }

        $container->prependExtensionConfig('liip_imagine', [
            'loaders' => [
                'default' => [
                    'filesystem' => [
                        'data_root' => ['%kernel.project_dir%/public'] + array_filter($dataRoots, 'is_dir'),
                    ],
                ],
            ],
        ]);
    }
}
