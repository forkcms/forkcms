<?php

namespace ForkCMS\Bundle\InstallerBundle\Console;

use ForkCMS\Bundle\InstallerBundle\Entity\InstallationData;
use ForkCMS\Bundle\InstallerBundle\Form\Handler\DatabaseHandler;
use ForkCMS\Bundle\InstallerBundle\Form\Handler\LanguagesHandler;
use ForkCMS\Bundle\InstallerBundle\Form\Handler\LoginHandler;
use ForkCMS\Bundle\InstallerBundle\Form\Handler\ModulesHandler;
use ForkCMS\Bundle\InstallerBundle\Service\ForkInstaller;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Throwable;

/**
 * This command will run the requirements checks of fork
 */
class InstallCommand extends Command
{
    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var SymfonyStyle */
    private $formatter;

    /** @var ForkInstaller */
    private $installer;

    /** @var string */
    private $installConfigPath;

    /** @var bool */
    private $forkIsInstalled;

    /** @var PrepareForReinstallCommand */
    private $prepareForReinstallCommand;

    public function __construct(
        ForkInstaller $installer,
        string $projectDirectory,
        bool $forkIsInstalled,
        PrepareForReinstallCommand $prepareForReinstallCommand
    ) {
        parent::__construct();
        $this->installer = $installer;
        $this->installConfigPath = $projectDirectory . '/app/config/cli-install.yml';
        $this->forkIsInstalled = $forkIsInstalled;
        $this->prepareForReinstallCommand = $prepareForReinstallCommand;
    }

    protected function configure(): void
    {
        $this
            ->setName('forkcms:install:install')
            ->setDescription('Install fork')
            ->addOption('email', 'u', InputOption::VALUE_REQUIRED, 'The email address of the backend user')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'The password of the backend user')
            ->setHidden($this->forkIsInstalled);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->input = $input;
        $this->output = $output;
        $this->formatter = new SymfonyStyle($input, $output);

        if (!$this->isReadyForInstall()) {
            return;
        }

        try {
            if ($this->installer->install($this->getInstallationData())) {
                $this->formatter->success('Fork CMS is installed');

                return;
            }
        } catch (Throwable $throwable) {
            // There was a validation error
            $this->formatter->error($throwable->getMessage());

            return;
        }

        $this->formatter->error('Fork CMS was not installed');
    }

    private function serverMeetsRequirements(): int
    {
        $checkRequirementsCommand = $this->getApplication()->find('forkcms:install:check-requirements');
        $this->formatter->writeln('<info>Checking requirements</info>');
        $checkRequirementsResult = $checkRequirementsCommand->run(new ArrayInput([]), $this->output);

        return $checkRequirementsResult === CheckRequirementsCommand::RETURN_SERVER_MEETS_REQUIREMENTS
               || $checkRequirementsResult === CheckRequirementsCommand::RETURN_SERVER_MEETS_REQUIREMENTS_BUT_HAS_WARNINGS;
    }

    private function getInstallationData(): InstallationData
    {
        $config = $this->getInstallationConfig();
        $installationData = new InstallationData();

        $this->setLanguageConfig($config['language'] ?? [], $installationData);
        $this->setModulesConfig($config['modules'] ?? [], $installationData);
        $this->setDebugConfig($config['debug'] ?? [], $installationData);
        $this->setDatabaseConfig($config['database'] ?? [], $installationData);
        $this->setUserConfig($config['user'] ?? [], $installationData);

        return $installationData;
    }

    private function setLanguageConfig(array $config, InstallationData $installationData): void
    {
        if (!$this->isConfigComplete($config, ['multiLanguage', 'defaultLanguage'])) {
            throw new RuntimeException('Language config is not complete');
        }
        $installationData->setLanguageType($config['multiLanguage'] ? 'multiple' : 'single');
        $installationData->setDefaultLanguage($config['defaultLanguage']);
        $installationData->setDefaultInterfaceLanguage(
            $config['defaultInterfaceLanguage'] ?? $config['defaultLanguage']
        );

        $installationData->setLanguages($config['languages'] ?? []);
        $installationData->setInterfaceLanguages($config['interfaceLanguages'] ?? []);

        (new LanguagesHandler())->processInstallationData($installationData);
    }

    private function setModulesConfig(array $config, InstallationData $installationData): void
    {
        $installationData->setExampleData($config['exampleData'] ?? false);

        foreach ($config['modules'] ?? [] as $module) {
            $installationData->addModule($module);
        }
        foreach (ForkInstaller::getRequiredModules() as $module) {
            $installationData->addModule($module);
        }
        foreach (ForkInstaller::getHiddenModules() as $module) {
            $installationData->addModule($module);
        }

        (new ModulesHandler())->processInstallationData($installationData);
    }

    private function setDebugConfig(array $config, InstallationData $installationData): void
    {
        $installationData->setDifferentDebugEmail(array_key_exists('email', $config) && $config['email'] !== null);

        if ($installationData->hasDifferentDebugEmail()) {
            $installationData->setDebugEmail($config['email']);
        }
    }

    private function setDatabaseConfig(array $config, InstallationData $installationData): void
    {
        if (!$this->isConfigComplete($config, ['hostname', 'username', 'password', 'name'])) {
            throw new RuntimeException('Database config is not complete');
        }

        $installationData->setDatabaseHostname($config['hostname']);
        $installationData->setDatabaseUsername($config['username']);
        $installationData->setDatabasePassword($config['password']);
        $installationData->setDatabaseName($config['name']);

        if (array_key_exists('port', $config) && $config['port'] !== null) {
            $installationData->setDatabasePort($config['port']);
        }

        (new DatabaseHandler())->processInstallationData($installationData);
    }

    private function setUserConfig(array $config, InstallationData $installationData): void
    {
        if (!array_key_exists('email', $config) || $config['email'] === null) {
            $config['email'] = $this->formatter->askQuestion($this->getAskEmailQuestion());
        }
        $installationData->setEmail($config['email']);
        if (!array_key_exists('password', $config) || $config['password'] === null) {
            $config['password'] = $this->formatter->askQuestion($this->getAskPasswordQuestion());
        }
        $installationData->setPassword($config['password']);

        (new LoginHandler())->processInstallationData($installationData);
    }

    private function isConfigComplete(array $config, array $required): bool
    {
        return count(array_intersect_key(array_flip($required), array_filter($config))) === count($required);
    }

    private function getAskEmailQuestion(): Question
    {
        $question = new Question('What is the email of the main backend user?');
        $question->setValidator(
            static function ($email) {
                if ('' === trim($email)) {
                    throw new InvalidArgumentException('The email must not be empty.');
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new InvalidArgumentException('Please enter a valid email address.');
                }

                return $email;
            }
        );

        return $question;
    }

    private function getAskPasswordQuestion(): Question
    {
        $question = new Question('What is the password of the main backend user?');
        $question->setValidator(
            static function ($password) {
                if ('' === trim($password)) {
                    throw new InvalidArgumentException('The password must not be empty.');
                }

                return $password;
            }
        );
        $question->setHidden(true);

        return $question;
    }

    private function isReadyForInstall(): bool
    {
        if ($this->forkIsInstalled
            && $this->prepareForReinstallCommand->run(new ArrayInput([]), $this->output) !== PrepareForReinstallCommand::RETURN_SUCCESS) {
            $this->formatter->error('Fork CMS is already installed');

            return false;
        }

        if (!$this->serverMeetsRequirements()) {
            $this->formatter->error('This server is not compatible with Fork CMS');

            return false;
        }

        if (!is_file($this->installConfigPath)) {
            $this->formatter->error(
                'Please add your app/config/cli-install.yml based on app/config/cli-install.yml.dist'
            );

            return false;
        }

        return true;
    }

    private function getInstallationConfig(): array
    {
        $config = Yaml::parse(file_get_contents($this->installConfigPath))['config'] ?? [];

        if ($this->input->hasOption('email') && $this->input->getOption('email') !== null) {
            $config['user']['email'] = $this->input->getOption('email');
        }
        if ($this->input->hasOption('password') && $this->input->getOption('password') !== null) {
            $config['user']['password'] = $this->input->getOption('password');
        }

        return $config;
    }
}
