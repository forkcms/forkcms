<?php

namespace ForkCMS\Bundle\InstallerBundle\Console;

use ForkCMS\Bundle\InstallerBundle\Entity\InstallationData;
use ForkCMS\Bundle\InstallerBundle\Form\Handler\DatabaseHandler;
use ForkCMS\Bundle\InstallerBundle\Form\Handler\LanguagesHandler;
use ForkCMS\Bundle\InstallerBundle\Service\ForkInstaller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

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

    public function __construct(ForkInstaller $installer, string $projectDirectory)
    {
        parent::__construct();
        $this->installer = $installer;
        $this->installConfigPath = $projectDirectory . '/app/config/cli-install.yml';
    }

    protected function configure(): void
    {
        $this
            ->setName('forkcms:install:install')
            ->setDescription('Install fork');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->input = $input;
        $this->output = $output;
        $this->formatter = new SymfonyStyle($input, $output);

        if (!$this->serverMeetsRequirements()) {
            $this->formatter->error('This server is not compatible with Fork CMS');

            return;
        }

        if (!is_file($this->installConfigPath)) {
            $this->formatter->error(
                'Please add your app/config/cli-install.yml based on app/config/cli-install.yml.dist'
            );

            return;
        }

        if ($this->installer->install($this->getInstallationData())) {
            $this->formatter->success('Fork CMS is installed');

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
        $config = Yaml::parse(file_get_contents($this->installConfigPath))['config'] ?? [];
        $installationData = new InstallationData();

        $this->setLanguageConfig($config['language'] ?? [], $installationData);
        $this->setDatabaseConfig($config['database'] ?? [], $installationData);

        return $installationData;
    }

    private function setLanguageConfig(array $config, InstallationData $installationData): void
    {
        if (!$this->isConfigComplete($config, ['multiLanguage', 'defaultLanguage', 'defaultInterfaceLanguage'])) {
            $this->formatter->error('Language config is not complete');

            return;
        }
        $installationData->setLanguageType($config['multiLanguage'] ? 'multiple' : 'single');
        $installationData->setDefaultLanguage($config['defaultLanguage']);
        $installationData->setDefaultInterfaceLanguage($config['defaultInterfaceLanguage']);

        $installationData->setLanguages($config['languages'] ?? []);
        $installationData->setInterfaceLanguages($config['interfaceLanguages'] ?? []);

        (new LanguagesHandler())->processInstallationData($installationData);
    }

    private function setDatabaseConfig(array $config, InstallationData $installationData): void
    {
        if (!$this->isConfigComplete($config, ['hostname', 'username', 'password', 'name'])) {
            $this->formatter->error('Database config is not complete');

            return;
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

    private function isConfigComplete(array $config, array $required): bool
    {
        return count(array_intersect_key(array_flip($required), $config)) === count($required);
    }
}
