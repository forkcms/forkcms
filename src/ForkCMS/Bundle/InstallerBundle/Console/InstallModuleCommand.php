<?php

namespace ForkCMS\Bundle\InstallerBundle\Console;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Extensions\Engine\Model;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Exception;
use ForkCMS\App\BaseModel;
use PDO;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This command allows you to install a module via the CLI
 */
class InstallModuleCommand extends Command
{
    /** @var SymfonyStyle */
    private $formatter;

    /** @var Connection */
    private $dbConnection;

    /** @var KernelInterface */
    private $kernel;

    public function __construct(EntityManager $em, KernelInterface $kernel)
    {
        parent::__construct();
        $this->dbConnection = $em->getConnection();
        $this->kernel = $kernel;
    }

    protected function configure(): void
    {
        $this
            ->setName('forkcms:install:module')
            ->setDescription('Command to install a module in Fork CMS')
            ->addArgument('module', InputArgument::OPTIONAL, 'Name of the module to install');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->formatter = new SymfonyStyle($input, $output);
        $module = $this->getModuleToInstall($input, $output);

        if (BackendExtensionsModel::existsModule($module)) {
            // Make sure this module can be installed
            $output->writeln("<comment>Validating if module can be installed...</comment>");
            $this->validateIfModuleCanBeInstalled($module);

            // Reboot the kernel to trigger a kernel initialize which registers the Dependency Injection Extension of the
            // module we would like to install. Also, make sure to replace the static cached container in our BaseModel!
            $_SERVER['INSTALLING_MODULE'] = $module;
            $this->kernel->shutdown();
            $this->kernel->boot();
            BaseModel::setContainer($this->kernel->getContainer());

            // Do the actual module install
            $output->writeln("<comment>Installing module $module...</comment>");
            BackendExtensionsModel::installModule($module);

            // Remove our container cache after this installation
            $output->writeln("<comment>Triggering cache clear...</comment>");
            $symfonyCacheClearCommand = $this->getApplication()->find('cache:clear');
            $symfonyCacheClearCommand->run(new ArrayInput(['--no-warmup' => true]), $output);

            $output->writeln("<info>Module $module is installed succesfully 🎉!");
        }
    }

    /**
     * Get the module name from the input arguments, or by creating an interactive selection menu.
     */
    private function getModuleToInstall(InputInterface $input, OutputInterface $output): string
    {
        $moduleName = $input->getArgument('module');
        $options = $this->getModulesToInstall();
        if ($moduleName !== null && array_key_exists($moduleName, $options)) {
            return $moduleName;
        }

        // Ask question
        $output->writeln('<question>Select the module to install:</question>');

        // Calculate max width to align the descriptions
        $width = $this->getColumnWidth($options);

        // Write the modules with name & description
        foreach ($options as $option) {
            $name = $option['name'];
            $description = $option['description'];
            $spacingWidth = $width - strlen($name);

            $output->write(
                sprintf('  <info>%s</info>%s%s', $name, str_repeat(' ', $spacingWidth), $description),
                $options
            );
        }

        // Write the module selection question w/ autocomplete
        $helper = $this->getHelper('question');
        $question = new Question('<question>Your selection:</question> ');
        $question->setAutocompleterValues(array_keys($options));
        $question->setMaxAttempts(3);
        $question->setValidator(function ($answer) use ($options) {
            if (!array_key_exists($answer, $options)) {
                throw new RunTimeException("Incorrect option: {$answer}");
            }
            return $answer;
        });

        return $helper->ask($input, $output, $question);
    }

    private function getAlreadyInstalledModules(): array
    {
        return $this->dbConnection
            ->executeQuery('SELECT name FROM modules')
            ->fetchAll(PDO::FETCH_COLUMN);
    }

    private function getModulesToInstall(): array
    {
        $modules = [];

        $finder = new Finder();
        $directories = $finder->directories()->in(__DIR__ . '/../../../../Backend/Modules')->depth(0);
        $installedModules = $this->getAlreadyInstalledModules();

        foreach ($directories->getIterator() as $directory) {
            $name = $directory->getFilename();

            // Skip module if already installed
            if (in_array($name, $installedModules, true)) {
                continue;
            }

            // Build array with module information
            $moduleInformation = Model::getModuleInformation($name);
            $description = preg_replace(['/\s{2,}/', '/[\t\n]/'], '', strip_tags($moduleInformation['data']['description']) ?? "");
            $modules[$name] = [
                'name' => $name,
                'description' => strlen($description) > 100 ? substr($description, 0, 100)."..." : $description,
            ];
        }

        ksort($modules);
        return $modules;
    }

    /**
     * Calculate the optimal column width for our interactive selection menu.
     */
    private function getColumnWidth(array $modules): int
    {
        $width = 0;
        foreach ($modules as $module) {
            $width = strlen($module['name']) > $width ? strlen($module['name']) : $width;
        }

        return $width + 2;
    }


    private function validateIfModuleCanBeInstalled(string $module): void
    {
        // Check if module is already installed
        if (BackendModel::isModuleInstalled($module)) {
            throw new Exception("Module is already installed");
        }

        // Check if installer class is present
        if (!is_file(BACKEND_MODULES_PATH . '/' . $module . '/Installer/Installer.php')) {
            throw new Exception("Module does not have an installer class");
        }
    }
}
