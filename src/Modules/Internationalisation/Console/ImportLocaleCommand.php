<?php

namespace ForkCMS\Modules\Internationalisation\Console;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Internationalisation\Domain\Importer\Importer;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class ImportLocaleCommand extends Command
{
    public function __construct(
        private string $rootDir,
        private Importer $translationImporter,
    ) {
        parent::__construct('forkcms:internationalisation:locale:import');
    }

    protected function configure(): void
    {
        $this->setDescription('Import fork translations for a specific modul or from a given file')
            ->addOption('overwrite', 'o', InputOption::VALUE_NONE, 'Overwrite the existing translations')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Path to the file with the translations')
            ->addOption('module', 'm', InputOption::VALUE_REQUIRED, 'Name of the module that contains the translations')
            ->addOption('locale', 'l', InputOption::VALUE_REQUIRED, 'Only install for a specific locale');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getOption('file');
        $moduleName = $input->getOption('module');
        $locale = $input->getOption('locale');
        $overwriteExistingTranslations = $input->getOption('overwrite');
        $formatter = new SymfonyStyle($input, $output);

        if ($filePath === null && $moduleName === null) {
            $formatter->error('Please specify a module or path to a translation file');

            return self::INVALID;
        }

        $translationPath = $this->getTranslationPath($filePath, $moduleName);

        try {
            $importResults = $this->translationImporter->import(
                $translationPath,
                $overwriteExistingTranslations,
                $locale === null ? null : Locale::from($locale)
            );
        } catch (FileNotFoundException) {
            $formatter->error('The given locale file (' . $translationPath . ') does not exist.');

            return self::INVALID;
        }

        if ($importResults->getTotalCount() === 0) {
            $formatter->error('No translations were found.');

            return self::INVALID;
        }

        if ($importResults->getImportedCount() > 0) {
            $formatter->comment('Imported ' . $importResults->getImportedCount() . ' translations succesfully!');
        }
        if ($importResults->getUpdatedCount() > 0) {
            $formatter->comment('Updated ' . $importResults->getUpdatedCount() . ' translations succesfully!');
        }
        if ($importResults->getSkippedCount() > 0) {
            $formatter->comment(
                sprintf(
                    'Skipped %d translations because they belong to a locale that is not installed or to a different locale than specified with the --locale option.',
                    $importResults->getSkippedCount()
                )
            );
        }
        if ($importResults->getFailedCount() > 0) {
            $formatter->warning(
                sprintf(
                    'Failed to import %d translations because they already existed, add --overwrite if you want to overwrite them.',
                    $importResults->getFailedCount()
                )
            );
        }

        return $importResults->getFailedCount() > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function getTranslationPath(?string $filePath, ?string $moduleName): string
    {
        return $filePath ?? sprintf(
            '%s/src/Modules/%s/assets/installer/translations.xml',
            $this->rootDir,
            ModuleName::fromString($moduleName)
        );
    }
}
