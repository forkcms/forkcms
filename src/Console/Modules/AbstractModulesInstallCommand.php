<?php

namespace Console\Modules;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Base module installer\uninstaller class
 */
abstract class AbstractModulesInstallCommand extends ContainerAwareCommand
{
    const PROMPT_INSTALLED = 1;
    const PROMPT_NOT_INSTALLED = 0;

    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var array|null
     */
    private $installedModules;

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param string $module
     * @return bool
     */
    protected function existsModule(string $module): bool
    {
        return BackendExtensionsModel::existsModule($module);
    }

    /**
     * Clear cache
     */
    protected function clearCache(): void
    {
        BackendExtensionsModel::clearCache();
    }

    /**
     * @param int $mode
     * @param callable $formatter
     * @return array
     * @throws \LogicException
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    protected function promptModule(
        int $mode,
        callable $formatter
    ): array {
        $input = $this->input;
        $output = $this->output;

        $modules = $this->getModules();

        $showAllModules = $input->getOption('show-all') !== false;

        if (!$showAllModules) {
            $modules = array_filter($modules, function ($module) use ($mode) {
                $isInstalled = $this->isModuleInstalled($module);

                return $mode === static::PROMPT_INSTALLED ? $isInstalled : !$isInstalled;
            });

            sort($modules);
        }

        $formattedModules = array_map($formatter, $modules);

        /** @var \Symfony\Component\Console\Helper\SymfonyQuestionHelper $helper */
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('Select module:', $formattedModules);
        $question->setMaxAttempts(1);

        if ($module = $helper->ask($input, $output, $question)) {
            $index = array_search($module, $formattedModules, true);

            $modules = (array) $modules[$index];
        }

        return $modules;
    }

    protected function confirmAction(string $quest): bool
    {
        $input = $this->input;
        $output = $this->output;

        /** @var \Symfony\Component\Console\Helper\SymfonyQuestionHelper $helper */
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion($quest, false);
        $question->setMaxAttempts(1);

        return $helper->ask($input, $output, $question);
    }

    /**
     * @return array|null
     * @throws \LogicException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    protected function fetchInstalledModules(): ?array
    {
        $sql = 'SELECT m.name FROM modules AS m';

        return $this->getContainer()->get('database')->getColumn($sql);
    }

    /**
     * @return array|null
     * @throws \LogicException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    protected function getInstalledModules(): ?array
    {
        if ($this->installedModules === null) {
            $this->installedModules = $this->fetchInstalledModules();
        }

        return $this->installedModules;
    }

    /**
     * @param string $module
     * @return bool
     * @throws \LogicException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    protected function isModuleInstalled(string $module): bool
    {
        return in_array($module, $this->getInstalledModules(), true);
    }

    /**
     * @return array
     */
    protected function getModules(): array
    {
        return array_map(function ($path) {
            return basename($path);
        }, $this->getModulesPath());
    }

    /**
     * @return array
     */
    protected function getModulesPath(): array
    {
        return glob(BACKEND_MODULES_PATH . '/*', GLOB_ONLYDIR);
    }

    /**
     * The method finds all installed modules that depend on the desired module
     *
     * @param array $modules
     * @param string $module
     * @param int $level
     * @throws \LogicException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    protected function findInstalledDependsModules(array &$modules, string $module, int $level = 0)
    {
        ++$level;

        $dependsModules = $this->findDependsModules($module);

        foreach ($dependsModules as $dependsModule) {
            if (!isset($modules[$dependsModule]) && $this->isModuleInstalled($dependsModule)) {
                $modules[$dependsModule] = $level;

                $this->findInstalledDependsModules($modules, $dependsModule, $level);
            }
        }
    }

    /**
     * The method finds all not installed modules depends on the desired module
     *
     * @param array $modules
     * @param string $module
     * @param int $level
     * @throws \LogicException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    protected function findNotInstalledModuleDependencies(array &$modules, string $module, int $level = 0)
    {
        ++$level;

        $dependsModules = $this->getModuleDependencies($module);

        foreach ($dependsModules as $dependsModule) {
            if (!isset($modules[$dependsModule]) && !$this->isModuleInstalled($dependsModule)) {
                $modules[$dependsModule] = $level;

                $this->findNotInstalledModuleDependencies($modules, $dependsModule, $level);
            }
        }
    }

    /**
     * The method finds all modules that depend on the desired module
     *
     * @param string $needle
     * @return array
     */
    protected function findDependsModules(string $needle): array
    {
        $result = [];

        $modules = $this->getModulesPath();

        foreach ($modules as $modulePath) {
            $info = $modulePath . '/info.xml';

            if (file_exists($info) && is_readable($info)) {
                $xml = new \SimpleXMLElement(file_get_contents($info));

                $dependsOn = $xml->xpath('//depends_on/module');

                if (!empty($dependsOn) && is_array($dependsOn)) {
                    foreach ($dependsOn as $depends) {
                        if ($needle === (string) $depends) {
                            $result[] = basename($modulePath);

                            break;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * The method finds all modules depends on the desired module
     *
     * @param string $module
     * @return array
     */
    protected function getModuleDependencies(string $module): array
    {
        $result = [];

        $info = BACKEND_MODULES_PATH . "/$module/info.xml";

        if (file_exists($info) && is_readable($info)) {
            $xml = new \SimpleXMLElement(file_get_contents($info));

            $dependsOn = $xml->xpath('//depends_on/module');

            if (!empty($dependsOn) && is_array($dependsOn)) {
                foreach ($dependsOn as $depends) {
                    $result[] = (string) $depends;
                }
            }
        }

        return $result;
    }

    /**
     * This method reads the required variables for any operation.
     *
     * @param string $module
     * @param string $section
     * @return array
     */
    protected function getRequiredVariables(string $module, string $section)
    {
        $result = [];

        $info = BACKEND_MODULES_PATH . "/$module/info.xml";

        if (file_exists($info) && is_readable($info)) {
            $xml = new \SimpleXMLElement(file_get_contents($info));

            $variables = $xml->xpath('//variables/' . $section . '/var');

            if (!empty($variables) && is_array($variables)) {
                foreach ($variables as $variable) {
                    $attr = ((array) $variable)['@attributes'];

                    $result[$attr['name']] = (string) $variable;
                }
            }
        }

        return $result;
    }
}
