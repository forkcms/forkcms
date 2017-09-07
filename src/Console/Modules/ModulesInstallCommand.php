<?php

namespace Console\Modules;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Installer\InstallerInterface;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Core\Language\Language as BL;
use Console\Exceptions\InstallerClassNotFoundException;
use Console\Exceptions\InstallerInterfaceException;
use Console\Exceptions\ModuleAlreadyInstalledException;
use Console\Exceptions\ModuleNotExistsException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Console command for install forkcms modules
 */
class ModulesInstallCommand extends ModulesBaseInstallCommand
{
    protected function configure(): void
    {
        $this->setName('forkcms:modules:install')
             ->setDescription('Install module')
             ->addUsage('ModuleOne ModuleTwo')
             ->addUsage('--show-all')
             ->addArgument('modules-id', InputArgument::OPTIONAL | InputArgument::IS_ARRAY)
             ->addOption('show-all', 'a', InputOption::VALUE_OPTIONAL, 'Show all modules (default only not installed)', false);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @throws \LogicException
     * @throws \Console\Exceptions\ModuleNotExistsException
     * @throws \Console\Exceptions\ModuleAlreadyInstalledException
     * @throws \Console\Exceptions\InstallerClassNotFoundException
     * @throws \Console\Exceptions\InstallerInterfaceException
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
            $modules = $this->promptModule(static::PROMPT_NOT_INSTALLED, [$this, 'installModulePromptFormat']);
        }

        if (!empty($modules)) {
            foreach ($modules as $module) {
                // make sure this module can be installed
                $this->validateIfModuleCanBeInstalled($module);

                $toInstall = [];

                $this->findNotInstalledModuleDependencies($toInstall, $module);

                // dump($toInstall);

                if (empty($toInstall)) {
                    $quest = "\n  Are you sure you want to <comment>install</comment> module <info>$module</info>? (y/N) ";

                    if ($this->confirmAction($quest)) {
                        // do the actual install
                        $this->installModule($module);

                        $output->writeln("\n    * <info>Module <comment>$module</comment> successful installed</info>.\n");
                    } else {
                        $output->writeln('  Canceled.');
                    }
                } else {
                    arsort($toInstall);

                    $toInstallModules = implode(', ', array_keys($toInstall));

                    $quest = "\n  Module <info>$module</info> depends on modules <comment>$toInstallModules</comment>."
                        . "\n\n  To <comment>install</comment> module <info>$module</info>, you will have to <comment>install</comment> modules <comment>$toInstallModules</comment>."
                        . "\n  Are you sure you want to do this? (y/N) ";

                    if ($this->confirmAction($quest)) {
                        $output->writeln('');

                        // Adding main module to install list
                        $toInstall[$module] = 0;

                        foreach ($toInstall as $toInstallModule => $priority) {
                            // do the actual install
                            $this->installModule($toInstallModule);

                            $output->writeln("    * <info>Module <comment>$toInstallModule</comment> successful installed</info>.");
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
    public function installModulePromptFormat(string $module): string
    {
        $module_pad = str_pad($module, 18, ' ', STR_PAD_RIGHT);

        $isInstalled = $this->isModuleInstalled($module);

        $installed = $isInstalled
            ? '<info>  ✔ installed</info>'
            : '<fg=magenta>not installed</>';

        try {
            $this->validateIfModuleCanBeInstalled($module);

            $isCanBeInstalled = true;
        } catch (\Exception $e) {
            $isCanBeInstalled = false;
        }

        $canBeInstalled = $isCanBeInstalled
            ? ''
            : '<error>can not be installed</error>';

        return "$module_pad $installed $canBeInstalled";
    }

    /**
     * @param string $module
     * @throws \Console\Exceptions\ModuleNotExistsException
     * @throws \Console\Exceptions\ModuleAlreadyInstalledException
     * @throws \Console\Exceptions\InstallerClassNotFoundException
     * @throws \Console\Exceptions\InstallerInterfaceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    private function validateIfModuleCanBeInstalled(string $module): void
    {
        // does the item exist
        if (!$this->existsModule($module)) {
            throw new ModuleNotExistsException($module);
        }

        // already installed
        if (BackendModel::isModuleInstalled($module)) {
            throw new ModuleAlreadyInstalledException($module);
        }

        $installerFile = BACKEND_MODULES_PATH . '/' . $module . '/Installer/Installer.php';

        // no installer class present
        if (!is_file($installerFile)) {
            throw new InstallerClassNotFoundException($module, $installerFile);
        }

        $installer = $this->createInstaller($module);

        if (!($installer instanceof InstallerInterface) && !($installer instanceof ModuleInstaller)) {
            throw new InstallerInterfaceException($module, $installerFile);
        }
    }

    /**
     * @param string $module
     * @throws \LogicException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    private function installModule(string $module): void
    {
        $installer = $this->createInstaller($module);
        $installer->setInput($this->input);
        $installer->setOutput($this->output);

        $variables = $installer->getPromptVariables();

        if (!empty($variables)) {

            /** @var \Symfony\Component\Console\Helper\SymfonyQuestionHelper $helper */
            $helper = $this->getHelper('question');

            foreach ($variables as $variable => $desc) {
                $question = new Question($desc . ':');
                $question->setMaxAttempts(1);

                $variables[$variable] = $helper->ask($this->input, $this->output, $question);
            }
        }

        $installer->setVariables($variables);

        $installer->install();

        // clear the cache so locale (and so much more) gets rebuilt
        $this->clearCache();
    }

    /**
     * @param string $module
     * @param array $variables
     * @return \Backend\Core\Installer\InstallerInterface
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    private function createInstaller(string $module, array $variables = []): InstallerInterface
    {
        $class = 'Backend\\Modules\\' . $module . '\\Installer\\Installer';

        return new $class(
            BackendModel::getContainer()->get('database'),
            BL::getActiveLanguages(),
            array_keys(BL::getInterfaceLanguages()),
            false,
            $variables
        );
    }
}
