<?php

namespace ForkCMS\Modules\Extensions\Domain\Twig;

use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Core\Domain\PDO\ForkConnection;
use ForkCMS\Modules\Extensions\Domain\Module\InstalledModules;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Twig\Loader\FilesystemLoader;

#[AsTaggedItem('twig.loader')]
final class ForkTemplateLoader extends FilesystemLoader
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        string $kernelProjectDir,
        #[Autowire('%fork.is_installed')]
        bool $forkIsInstalled
    ) {

        $filesystem = new Filesystem();

        $modulesDirectory = $kernelProjectDir . '/src/Modules/';

        if (!$forkIsInstalled) {
            parent::__construct([], $modulesDirectory . 'Installer/templates');
            $this->addPath($modulesDirectory . 'Extensions/templates', 'Extensions');

            return;
        }
        parent::__construct([], $kernelProjectDir . '/src/Core/templates');

        $theme = ForkConnection::get()->getActiveTheme();
        $themePath = realpath(__DIR__ . '/../../../../Themes/' . $theme);

        foreach ((new InstalledModules(true))() as $moduleName) {
            $themeTemplates = $themePath . '/templates/' . $moduleName;
            if ($themePath !== false && $filesystem->exists($themeTemplates)) {
                $this->addPath($themeTemplates, $moduleName->getName());
            }
            $moduleTemplates = $modulesDirectory . $moduleName . '/templates';
            if ($filesystem->exists($moduleTemplates)) {
                $this->addPath($moduleTemplates, $moduleName->getName());
            }
        }
    }

    protected function findTemplate(string $name, bool $throw = true): ?string
    {
        $themeName = preg_replace('/^(@[A-Z][A-Za-z0-9]*\/)Frontend\//', '$1', $name);
        if ($themeName !== $name && is_string($themeName)) {
            $themeTemplate = parent::findTemplate($themeName, false);
            if ($themeTemplate !== null) {
                return $themeTemplate;
            }
        }

        return parent::findTemplate($name, $throw);
    }

    /** @return array<string,string> */
    public function getPossibleTemplates(
        ModuleName $moduleName,
        Application $application,
        ?string $subDirectory = null
    ): array {
        $possibleTemplates = [];
        $finder = new Finder();
        $finder->name('*.html.twig');

        $basePaths = $this->getPaths($moduleName->getName());
        foreach ($basePaths as $basePath) {
            $isTheme = str_contains($basePath, 'src/Themes');
            if ($isTheme && $application === Application::FRONTEND) {
                $finder->in($basePath .  '/' . $subDirectory);
            } else {
                $finder->in($basePath . '/' . ucfirst($application->value) . '/' . $subDirectory);
            }
        }

        foreach ($finder->files() as $file) {
            $fineName = $file->getBasename();
            $possibleTemplates[$fineName] = $fineName;
        }

        return $possibleTemplates;
    }
}
