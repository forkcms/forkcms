<?php

namespace Console\Locale;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * This is a simple command to enable a locale in fork
 */
class EnableLocaleCommand extends Command
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
        $this->setName('forkcms:locale:enable')
            ->setDescription('Enable a locale');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws Exception
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->formatter = new SymfonyStyle($input, $output);

        $this->output->writeln($this->formatter->title('Fork CMS locale enable'));
    }
}
