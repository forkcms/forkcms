<?php

namespace Console\Thumbnails;

use Common\Core\Model;
use Exception;
use ForkCMS\Utility\Thumbnails;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * This is a simple command to generate thumbnails
 */
class GenerateThumbnailsCommand extends Command
{
    /** @var Thumbnails */
    private $thumbnails;

    protected function configure(): void
    {
        $this->setName('forkcms:thumbnails:generate')
            ->setAliases(['thumbnails:generate'])
            ->setDescription('Create thumbnails')
            ->addOption(
                'folder',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Name of the folder in /src/Frontend/Files where you want to generate thumbnails for.'
            );
    }

    public function __construct(Thumbnails $thumbnails)
    {
        $this->thumbnails = $thumbnails;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->generateThumbnails($this->getFolderPath($input), $output);
    }

    private function generateThumbnails(string $folderPath, OutputInterface $output): void
    {
        $finder = new Finder();
        $finder->files()->in($folderPath)->name('/^.*\.(jpg|jpeg|png|gif)$/i');

        foreach ($finder as $file) {
            $this->thumbnails->generate($folderPath, $file->getRealPath());
            $output->writeln('<info>Creating thumbnail for ' . $file->getBasename() . '...</info>');
        }
    }

    private function getFolderPath(InputInterface $input): string
    {
        $folderOption = $input->getOption('folder');

        if (!isset($folderOption)) {
            throw new Exception('Please specify a foldername "--folder=XXX" from /src/Frontend/Files where you want to generate thumbnails for.');
        }

        return realpath(__DIR__ . '/../../../src/Frontend/Files/' . $folderOption);
    }
}
