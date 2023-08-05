<?php

namespace ForkCMS\Modules\Extensions\Console;

use ForkCMS\Modules\Extensions\Domain\Module\Module;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstallerLocator;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleRepository;
use ForkCMS\Modules\Extensions\Domain\Theme\Theme;
use ForkCMS\Modules\Extensions\Domain\Theme\ThemeRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'forkcms:extensions:webpack-config',
    description: 'Get a json with the config for webpack',
)]
final class WebpackConfigCommand extends Command
{
    public function __construct(
        private readonly ThemeRepository $themeRepository,
        private readonly ModuleRepository $moduleRepository,
        private readonly ModuleInstallerLocator $moduleInstallerLocator,
        private readonly bool $forkIsInstalled = true,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = ['themes' => [], 'modules' => []];
        foreach ($this->getThemes() as $theme) {
            $assetsPath = $theme->getAssetsPath();
            if (!is_dir($assetsPath . '/public') || !is_dir($assetsPath . '/webpack')) {
                continue;
            }

            $config['themes'][] = [
                'name' => $theme->getName(),
                'path' => $assetsPath,
                'js' => is_dir($assetsPath . '/js'),
                'scss' => is_dir($assetsPath . '/scss'),
            ];
        }
        foreach ($this->getModules() as $module) {
            $assetsPath = $module->getAssetsPath();
            if (
                !is_dir($assetsPath . '/Backend/public')
                && !is_dir($assetsPath . '/Backend/webpack')
                && !is_dir($assetsPath . '/Frontend/public')
                && !is_dir($assetsPath . '/Frontend/webpack')
                && !is_dir($assetsPath . '/Installer/public')
                && !is_dir($assetsPath . '/Installer/webpack')
            ) {
                continue;
            }

            $config['modules'][] = [
                'name' => $module->getName()->getName(),
                'path' => $assetsPath,
            ];
        }

        $output->writeln(json_encode($config, JSON_THROW_ON_ERROR));

        return self::SUCCESS;
    }

    /** @return Theme[] */
    private function getThemes(): array
    {
        if ($this->forkIsInstalled) {
            return $this->themeRepository->findAll();
        }

        return array_map(Theme::fromDataTransferObject(...), $this->themeRepository->findInstallable(false));
    }

    /** @return Module[] */
    private function getModules(): array
    {
        if ($this->forkIsInstalled) {
            $modules = $this->moduleRepository->findAll();
        } else {
            $modules = array_map(Module::fromModuleName(...), $this->moduleInstallerLocator->getAllModuleNames());
        }

        $modules[] = Module::fromModuleName(ModuleName::installer());

        return $modules;
    }
}
