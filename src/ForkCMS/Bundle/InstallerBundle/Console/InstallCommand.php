<?php

namespace ForkCMS\Bundle\InstallerBundle\Console;

use ForkCMS\Bundle\InstallerBundle\Entity\InstallationData;
use ForkCMS\Bundle\InstallerBundle\Requirement\Requirement;
use ForkCMS\Bundle\InstallerBundle\Requirement\RequirementCategory;
use ForkCMS\Bundle\InstallerBundle\Service\ForkInstaller;
use ForkCMS\Bundle\InstallerBundle\Service\RequirementsChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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

    public function __construct(ForkInstaller $installer)
    {
        parent::__construct();

        $this->installer = $installer;
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
        }

        if ($this->installer->install(new InstallationData())) {
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
}
