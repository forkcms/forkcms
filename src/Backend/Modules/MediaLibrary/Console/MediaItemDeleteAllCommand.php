<?php

namespace Backend\Modules\MediaLibrary\Console;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Delete all media items Console Command
 * Example: "bin/console media_library:delete:items", will only delete all not-connected MediaItems
 * Example: "bin/console media_library:delete:items --all", will delete all MediaItems (even the connected ones)
 */
class MediaItemDeleteAllCommand extends ContainerAwareCommand
{
    /**
     * Should we delete all
     *
     * @var bool
     */
    protected $deleteAll = false;

    protected function configure(): void
    {
        $this
            ->setName('media_library:delete:items')
            ->setDescription('Delete all MediaLibrary items.')
            ->addOption(
                'all',
                null,
                InputOption::VALUE_NONE,
                'If set, all MediaItems (even the connected items) will be deleted.'
            );
    }

    private function checkOptions(InputInterface $input): void
    {
        if ($input->getOption('all')) {
            $this->deleteAll = true;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            'Are you sure you want do delete all media? Type "y" or "yes" to confirm: ',
            false
        );
        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('<info>Action cancelled.</info>');

            return;
        }

        $output->writeln('<info>Started deleting media items.</info>');
        $this->checkOptions($input);
        $numberOfDeletedMediaItems = $this->deleteMediaItems();
        $output->writeln('<info>Finished deleting ' . $numberOfDeletedMediaItems . ' media items.</info>');
    }

    private function deleteMediaItems(): int
    {
        return $this->getContainer()->get('media_library.manager.item')->deleteAll($this->deleteAll);
    }
}
