<?php

namespace Console\Thumbnails;

use ForkCMS\Utility\Thumbnails;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

/**
 * This is a simple command to generate thumbnails
 */
class GenerateThumbnailsCommand extends Command
{
    /** @var Thumbnails */
    private $thumbnails;

    /** @var string */
    private $folder;

    /** @var SplFileInfo[] */
    private $files;

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
        $style = new SymfonyStyle($input, $output);

        $style->title('Fork CMS Thumbnail generator');

        $this->retrieveSourceFilesAndDestinationFolders($input, $output);
        $this->generateThumbnails($input, $output);
    }

    private function generateThumbnails(InputInterface $input, OutputInterface $output): void
    {
        $style = new SymfonyStyle($input, $output);

        $style->section('Generating thumbnails');

        foreach ($this->files as $file) {
            $this->thumbnails->generate($this->folder, $file->getRealPath());
            $output->writeln('<info>Creating thumbnail for ' . $file->getBasename() . '...</info>');
        }

        $style->success('Thumbnails successfully generated');
    }

    private function retrieveSourceFilesAndDestinationFolders(InputInterface $input, OutputInterface $output): void
    {
        $folder = $input->getOption('folder');

        if (!isset($folder)) {
            $this->folder = $this->askForFolder($input, $output);
            $this->files = $this->findFilesInFolder($this->folder);

            return;
        }

        $this->folder = $folder;
        $this->files = $this->findFilesInFolder($folder);
    }

    private function askForFolder(InputInterface $input, OutputInterface $output): string
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $folder = $helper->ask($input, $output, new Question('<question>Path to folder:</question> '));

        while (!$this->confirmFolder($input, $output, $folder)) {
            $output->writeln(['', 'OK, let\'s try again', '']);
            $folder = $helper->ask($input, $output, new Question('<question>Path to folder:</question> '));
        }

        return FRONTEND_FILES_PATH . DIRECTORY_SEPARATOR . $folder;
    }

    private function confirmFolder(InputInterface $input, OutputInterface $output, string $folder): string
    {
        $folder = FRONTEND_FILES_PATH . DIRECTORY_SEPARATOR . $folder;

        if (!file_exists($folder)) {
            $output->writeln('<error>Folder does not exist</error>');

            return false;
        }

        $files = array_values(
            array_map(
                function (SplFileInfo $fileInfo): array {
                    return [$fileInfo->getFilename()];
                },
                $this->findFilesInFolder($folder)
            )
        );
        $directories = array_values(
            array_map(
                function (SplFileInfo $fileInfo): array {
                    return [$fileInfo->getFilename()];
                },
                $this->findDirectoriesInFolder($folder)
            )
        );

        $style = new SymfonyStyle($input, $output);

        $style->section('Found files');
        if (empty($files)) {
            $output->writeln(['none', '']);
        } else {
            $style->table(['Filename'], $files);
        }

        $style->section('Found directories');
        if (empty($directories)) {
            $output->writeln(['none', '']);
        } else {
            $style->table(['Directory'], $directories);
        }

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        return $helper->ask(
            $input,
            $output,
            new ConfirmationQuestion('<question>Does this look right to you? (y/N):</question> ')
        );
    }

    private function findFilesInFolder(string $folder): array
    {
        $finder = new Finder();
        if (file_exists($folder . DIRECTORY_SEPARATOR . 'source')) {
            $sourceFolder = $folder . DIRECTORY_SEPARATOR . 'source';

            return iterator_to_array($finder->files()->in($sourceFolder)->depth('== 0')->getIterator());
        }

        return iterator_to_array($finder->files()->in($folder)->depth('== 0')->getIterator());
    }

    private function findDirectoriesInFolder(string $folder): array
    {
        $finder = new Finder();

        return iterator_to_array($finder->directories()->in($folder)->depth('== 0')->getIterator());
    }
}
