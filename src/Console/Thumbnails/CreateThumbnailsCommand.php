<?php

namespace Console\Thumbnails;

use Common\Core\Model;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * This is a simple command to create thumbnails
 */
class CreateThumbnailsCommand extends Command
{
    /**
     * Configure the command options.
     */
    protected function configure()
    {
        $this->setName('forkcms:thumbnails:create')
            ->setAliases(['thumbnails:create'])
            ->setDescription('Create thumbnails')
            ->addOption('folder', 'f', InputOption::VALUE_OPTIONAL, 'Name of the folder in /src/Frontend/Files where you want to create thumbnails for.');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get input values
        $folderOption = $input->getOption('folder');

        if (!isset($folderOption) && !isset($moduleOption)) {
            throw new Exception('Please specify a foldername "--folder=XXX"');
        }

        // Get path to locale file
        $folderPath = $this->getFolderPath($folderOption);

        $this->createThumbnails($folderPath, $output);
    }

    /**
     * @param string $folderPath
     * @param OutputInterFace $output
     *
     * @throws Exception
     */
    private function createThumbnails($folderPath, $output)
    {
        $finder = new Finder();
        $finder->files()->in($folderPath)->name('/^.*\.(jpg|jpeg|png|gif)$/i');

        foreach ($finder as $file) {
            Model::generateThumbnails($folderPath, $file);
            $output->writeln('<info>Creating thumbnail for ' . $file->getBasename() . ' if not already exists...</info>');
        }
    }

    /**
     * Get the file or module path according to the input options
     *
     * @param string $folderOption
     *
     * @return string
     */
    private function getFolderPath($folderOption)
    {
        return __DIR__ . '/../../..' . '/src/Frontend/Files/' . $folderOption;
    }
}
