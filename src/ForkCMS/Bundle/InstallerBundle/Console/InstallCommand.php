<?php

namespace ForkCMS\Bundle\InstallerBundle\Console;

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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->formatter = new SymfonyStyle($input, $output);

        if ($this->isForkAlreadyInstalled() && !$this->isPreparedForReinstall()) {
            return 1;
        }

        return 0;
    }

    /**
     * @return bool
     */
    private function isForkAlreadyInstalled()
    {
        $filesystem = new Filesystem();
        $kernelDir = $this->getContainer()->getParameter('kernel.root_dir');

        $parameterFile = $kernelDir . '/config/parameters.yml';

        return $filesystem->exists($parameterFile);
    }

    /**
     * @return bool
     */
    private function isPreparedForReinstall()
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
}
