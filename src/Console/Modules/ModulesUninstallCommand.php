<?php

namespace Console\Modules;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Installer\UninstallerInterface;
use Backend\Core\Language\Language as BL;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Console\Exceptions\ModuleNotExistsException;
use Console\Exceptions\ModuleNotInstalledException;
use Console\Exceptions\UninstallerClassNotFoundException;
use Console\Exceptions\UninstallerInterfaceException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Console command for uninstall forkcms modules
 */
class ModulesUninstallCommand extends AbstractModulesInstallCommand
{
    protected function configure(): void
    {
        $this->setName('forkcms:modules:uninstall')
             ->setDescription('Uninstall module')
             ->addUsage('ModuleOne ModuleTwo')
             ->addUsage('--show-all')
             ->addArgument('modules-id', InputArgument::OPTIONAL | InputArgument::IS_ARRAY)
             ->addOption('show-all', 'a', InputOption::VALUE_OPTIONAL, 'Show all modules (default only installed)', false);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @throws \LogicException
     * @throws \Console\Exceptions\ModuleNotExistsException
     * @throws \Console\Exceptions\ModuleNotInstalledException
     * @throws \Console\Exceptions\UninstallerClassNotFoundException
     * @throws \Console\Exceptions\UninstallerInterfaceException
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        // get parameters
        $modules = $input->getArgument('modules-id');

        if (empty($modules)) {
            $modules = $this->promptModule(static::PROMPT_INSTALLED, [$this, 'uninstallModulePromptFormat']);
        }

        if (empty($modules)) {
            $output->writeln('  Module not selected.');
        } else {
            foreach ($modules as $module) {
                // make sure this module can be installed
                $this->validateIfModuleCanBeUninstalled($module);

                $toRemove = [];

                $this->findInstalledDependsModules($toRemove, $module);

                if (empty($toRemove)) {
                    $quest = "\n  Are you sure you want to <error>remove</error> module <info>$module</info>?"
                        . "\n  <comment>This action can not be undone!</comment> (y/N) ";

                    if ($this->confirmAction($quest)) {
                        // do the actual install
                        $this->uninstallModule($module);

                        $output->writeln("\n    * <info>Module <comment>$module</comment> successful uninstalled</info>.\n");
                    } else {
                        $output->writeln('  Canceled.');
                    }
                } else {
                    arsort($toRemove);

                    $toRemoveModules = implode(', ', array_keys($toRemove));

                    $quest = "\n  Module <info>$module</info> depends on modules <comment>$toRemoveModules</comment>."
                        . "\n\n  To <error>remove</error> module <info>$module</info>, you will have to <error>remove</error> modules <comment>$toRemoveModules</comment>."
                        . "\n  Are you sure you want to do this?"
                        . "\n  <comment>This action can not be undone!</comment> (y/N) ";

                    if ($this->confirmAction($quest)) {
                        $output->writeln('');

                        // Adding main module to remove list
                        $toRemove[$module] = 0;

                        foreach ($toRemove as $toRemoveModule => $priority) {
                            // do the actual install
                            $this->uninstallModule($toRemoveModule);

                            $output->writeln("    * <info>Module <comment>$toRemoveModule</comment> successful uninstalled</info>.");
                        }

                        $output->writeln('');
                    } else {
                        $output->writeln('  Canceled.');
                    }
                }
            }

            // remove our container cache after this request
            (new Filesystem)->remove($this->getContainer()->getParameter('kernel.cache_dir'));
        }

        return 0;
    }

    /**
     * @param string $module
     * @return string
     * @throws \LogicException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function uninstallModulePromptFormat(string $module): string
    {
        $module_pad = str_pad($module, 18, ' ', STR_PAD_RIGHT);

        $isInstalled = BackendModel::isModuleInstalled($module);

        $installed = $isInstalled
            ? '<info>  ✔ installed</info>'
            : '<fg=magenta>not installed</>';

        try {
            $this->validateIfModuleCanBeUninstalled($module);

            $isCanBeUninstalled = true;
        } catch (\Exception $e) {
            $isCanBeUninstalled = false;
        }

        $canBeUninstalled = $isCanBeUninstalled
            ? ''
            : '<error>can not be uninstalled</error>';

        return "$module_pad $installed    $canBeUninstalled";
    }

    /**
     * @param string $module
     * @throws \Console\Exceptions\ModuleNotExistsException
     * @throws \Console\Exceptions\ModuleNotInstalledException
     * @throws \Console\Exceptions\UninstallerClassNotFoundException
     * @throws \Console\Exceptions\UninstallerInterfaceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    private function validateIfModuleCanBeUninstalled(string $module): void
    {
        // does the item exist
        if (!BackendExtensionsModel::existsModule($module)) {
            throw new ModuleNotExistsException($module);
        }

        // already installed
        if (!BackendModel::isModuleInstalled($module)) {
            throw new ModuleNotInstalledException($module);
        }

        $uninstallerFile = BACKEND_MODULES_PATH . '/' . $module . '/Installer/Uninstaller.php';

        if (!is_file($uninstallerFile)) {
            throw new UninstallerClassNotFoundException($module, $uninstallerFile);
        }

        $uninstaller = $this->createUninstaller($module);

        if (!($uninstaller instanceof UninstallerInterface)) {
            throw new UninstallerInterfaceException($module, $uninstallerFile);
        }
    }

    /**
     * @param string $module
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    private function uninstallModule(string $module): void
    {
        $variables = $this->getRequiredVariables($module, 'uninstall');

        $variables = $this->askVariables($variables);

        $uninstaller = $this->createUninstaller($module, $variables);

        $uninstaller->uninstall();

        // clear the cache so locale (and so much more) gets rebuilt
        BackendExtensionsModel::clearCache();
    }

    /**
     * @param string $module
     * @param array $variables
     * @return \Backend\Core\Installer\UninstallerInterface
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    private function createUninstaller(string $module, array $variables = []): UninstallerInterface
    {
        $class = 'Backend\\Modules\\' . $module . '\\Installer\\Uninstaller';

        return new $class(
            BackendModel::getContainer()->get('database'),
            BL::getActiveLanguages(),
            array_keys(BL::getInterfaceLanguages()),
            false,
            $variables,
            $this->input,
            $this->output
        );
    }
}
