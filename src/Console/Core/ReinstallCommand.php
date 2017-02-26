<?php

namespace Console\Core;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * This command will prepare everuthing for a full reinstall
 */
class ReinstallCommand extends ContainerAwareCommand
{
    const RETURN_SUCCESS = 0;
    const RETURN_DID_NOT_REINSTALL = 1;
    const RETURN_DID_NOT_CLEAR_DATABASE = 2;

    /**
     * Configure the command options.
     */
    protected function configure()
    {
        $this->setName('forkcms:reinstall')
            ->setDescription('Clear the cache');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if (!$io->confirm('Are you sure you want to reinstall?')) {
            return self::RETURN_DID_NOT_REINSTALL;
        }

        $returnCode = $this->clearDatabase($io);
        $this->removeConfiguration($io);
        $this->clearCache($output, $io);

        return $returnCode;
    }

    /**
     * @param SymfonyStyle $io
     *
     * @return int
     */
    private function clearDatabase(SymfonyStyle $io)
    {
        if (!$io->confirm('Clear the database?')) {
            return self::RETURN_DID_NOT_CLEAR_DATABASE;
        }

        $tables = $this->getContainer()->get('database')->getColumn(
            'SHOW TABLES'
        );

        if (!empty($tables)) {
            $this->getContainer()->get('database')->execute('SET FOREIGN_KEY_CHECKS=0');
            $this->getContainer()->get('database')->drop($tables);
        }

        $io->success('Removed all tables');

        return self::RETURN_SUCCESS;
    }

    /**
     * @param SymfonyStyle $io
     */
    private function removeConfiguration(SymfonyStyle $io)
    {
        $fullPath = realpath(__DIR__ . '/../../..' . '/app/config/parameters.yml');
        if (file_exists($fullPath)) {
            unlink($fullPath);
            $io->success('Removed configuration file');
        }
    }

    /**
     * @param OutputInterface $output
     * @param SymfonyStyle $io
     */
    private function clearCache(OutputInterface $output, SymfonyStyle $io)
    {
        $command = $this->getApplication()->find('forkcms:cache:clear');
        $command->run(
            new ArrayInput(
                array(
                    'forkcms:cache:clear',
                )
            ),
            $output
        );

        $io->success('Ready for reinstall.');
    }
}
