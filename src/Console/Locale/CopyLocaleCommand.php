<?php

namespace Console\Locale;

use Backend\Core\Language\Locale;
use Common\Locale as CommonLocale;
use Common\ModulesSettings;
use ForkCMS\Utility\Module\CopyContentToOtherLocale\CopyContentFromModulesToOtherLocaleManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * This command will copy all the content from all the modules
 */
class CopyLocaleCommand extends Command
{
    /**
     * @var CopyContentFromModulesToOtherLocaleManager
     */
    private $copyContentFromModulesToOtherLocaleManager;

    /**
     * @var SymfonyStyle
     */
    private $formatter;

    /**
     * @var ModulesSettings
     */
    private $settings;

    public function __construct(
        ModulesSettings $settings,
        CopyContentFromModulesToOtherLocaleManager $copyContentFromModulesToOtherLocaleManager
    ) {
        parent::__construct();

        $this->settings = $settings;
        $this->copyContentFromModulesToOtherLocaleManager = $copyContentFromModulesToOtherLocaleManager;
    }

    protected function configure(): void
    {
        $this->setName('forkcms:locale:copy')
            ->setDescription('Copy the content across modules from one locale to another.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->formatter = new SymfonyStyle($input, $output);

        $fromLocale = $this->askFromLocale();
        $toLocale = $this->askToLocale($fromLocale);

        $this->copyContentFromModulesToOtherLocaleManager->copy($fromLocale, $toLocale, null);
        $this->formatter->success('All supported modules are copied from "' . $fromLocale . '" to "' . $toLocale . '".');
    }

    private function askFromLocale(): CommonLocale
    {
        return Locale::fromString($this->formatter->choice(
            'What locale would you like to copy?',
            $this->getActiveLocale()
        ));
    }

    private function askToLocale(CommonLocale $fromLocale): CommonLocale
    {
        return Locale::fromString($this->formatter->choice(
            'To which locale would you like to copy it?',
            $this->getToLocale($fromLocale)
        ));
    }

    private function getActiveLocale(): array
    {
        return array_flip($this->settings->get('Core', 'active_languages'));
    }

    private function getToLocale(CommonLocale $fromLocale): array
    {
        $toLocales = $this->getActiveLocale();

        // The "from" locale is not an option
        unset($toLocales[(string) $fromLocale]);

        return $toLocales;
    }
}
