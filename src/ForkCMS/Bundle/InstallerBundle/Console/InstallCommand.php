<?php

namespace ForkCMS\Bundle\InstallerBundle\Console;

use ForkCMS\Bundle\InstallerBundle\Entity\InstallationData;
use ForkCMS\Bundle\InstallerBundle\Form\Handler\DatabaseHandler;
use ForkCMS\Bundle\InstallerBundle\Form\Handler\InstallerHandler;
use ForkCMS\Bundle\InstallerBundle\Form\Handler\LanguagesHandler;
use ForkCMS\Bundle\InstallerBundle\Form\Handler\LoginHandler;
use ForkCMS\Bundle\InstallerBundle\Form\Handler\ModulesHandler;
use ForkCMS\Bundle\InstallerBundle\Form\Type\DatabaseType;
use ForkCMS\Bundle\InstallerBundle\Form\Type\LanguagesType;
use ForkCMS\Bundle\InstallerBundle\Form\Type\LoginType;
use ForkCMS\Bundle\InstallerBundle\Form\Type\ModulesType;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This command will install fork from the cli
 */
class InstallCommand extends ContainerAwareCommand
{
    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var SymfonyStyle */
    private $formatter;

    /**
     * Configure the command options.
     */
    protected function configure()
    {
        $this
            ->setName('forkcms:install:install')
            ->setDescription('Installation of fork from the cli');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;
        $this->formatter = new SymfonyStyle($input, $output);

        if ($this->isForkAlreadyInstalled() && !$this->isPreparedForReinstall()) {
            return 1;
        }

        if (!$this->serverMeetsTheRequirements()) {
            return 1;
        }

        $installationData = new InstallationData();
        $installationData = $this->selectLanguages($installationData);
        $installationData = $this->selectModules($installationData);
        $installationData = $this->requestDatabaseConfiguration($installationData);
        $installationData = $this->requestGodUserCredentials($installationData);

        if ($this->getContainer()->get('forkcms.installer')->install($installationData)) {
            $this->formatter->success('Fork has been installed');

            return 0;
        }

        // Normally we will never have this but you never know.
        $this->formatter->error('Fork could not be installed because the data wasn\'t valid');

        return 1;
    }

    /**
     * @param InstallationData $installationData
     *
     * @return InstallationData
     */
    private function selectLanguages(InstallationData $installationData): InstallationData
    {
        return $this->interactWithForm(LanguagesType::class, $installationData, new LanguagesHandler());
    }

    /**
     * @param InstallationData $installationData
     *
     * @return InstallationData
     */
    private function selectModules(InstallationData $installationData): InstallationData
    {
        return $this->interactWithForm(ModulesType::class, $installationData, new ModulesHandler());
    }

    /**
     * @param InstallationData $installationData
     *
     * @return InstallationData
     */
    private function requestDatabaseConfiguration(InstallationData $installationData): InstallationData
    {
        return $this->interactWithForm(DatabaseType::class, $installationData, new DatabaseHandler());
    }

    /**
     * @param InstallationData $installationData
     *
     * @return InstallationData
     */
    private function requestGodUserCredentials(InstallationData $installationData): InstallationData
    {
        return $this->interactWithForm(LoginType::class, $installationData, new LoginHandler());
    }

    /**
     * @return bool
     */
    private function serverMeetsTheRequirements(): bool
    {
        $checkRequirementsCommand = $this->getApplication()->find('forkcms:install:check-requirements');

        $checkRequirementsOutput = $checkRequirementsCommand->run(
            new ArrayInput(['forkcms:install:check-requirements']),
            $this->output
        );

        if ($checkRequirementsOutput === CheckRequirementsCommand::RETURN_SERVER_DOES_NOT_MEET_REQUIREMENTS) {
            return false;
        }

        if ($checkRequirementsOutput === CheckRequirementsCommand::RETURN_SERVER_MEETS_REQUIREMENTS_BUT_HAS_WARNINGS) {
            return $this->formatter->confirm('There where some warnings, do you still want to continue?');
        }

        return $checkRequirementsOutput === CheckRequirementsCommand::RETURN_SERVER_MEETS_REQUIREMENTS;
    }

    /**
     * @return bool
     */
    private function isForkAlreadyInstalled(): bool
    {
        $filesystem = new Filesystem();
        $kernelDir = $this->getContainer()->getParameter('kernel.root_dir');

        $parameterFile = $kernelDir . '/config/parameters.yml';

        return $filesystem->exists($parameterFile);
    }

    /**
     * @return bool
     */
    private function isPreparedForReinstall(): bool
    {
        $reinstallCommand = $this->getApplication()->find('forkcms:install:prepare-for-reinstall');

        $reinstallOutput = $reinstallCommand->run(
            new ArrayInput(['forkcms:install:prepare-for-reinstall']),
            $this->output
        );

        if ($reinstallOutput === PrepareForReinstallCommand::RETURN_DID_NOT_REINSTALL) {
            $this->formatter->error('This Fork has already been installed.');
            $this->formatter->note('Since you choose not to reinstall we can\'t do a clean install.');

            return false;
        }

        if ($reinstallOutput === PrepareForReinstallCommand::RETURN_DID_NOT_CLEAR_DATABASE) {
            $this->formatter->error('This Fork had already been installed.');
            $this->formatter->note(
                'Since you choose not to clear the database we can\'t do a clean install.' . PHP_EOL
                . 'Clear the database manually and run this command again.'
            );

            return false;
        }

        return $reinstallOutput === PrepareForReinstallCommand::RETURN_SUCCESS;
    }

    /**
     * @param string $className
     * @param InstallationData $installationData
     * @param InstallerHandler $handler
     *
     * @return InstallationData
     */
    private function interactWithForm(
        string $className,
        InstallationData $installationData,
        InstallerHandler $handler
    ): InstallationData {
        $formHelper = $this->getHelper('form');

        return $handler->processInstallationData(
            $formHelper->interactUsingForm($className, $this->input, $this->output, [], $installationData)
        );
    }
}
