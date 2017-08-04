<?php

namespace Console\Locale;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;

/**
 * This is a simple command to install a locale file
 */
class ImportLocaleCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('forkcms:locale:import')
            ->setAliases(['locale:import'])
            ->setDescription('Import locale translations')
            ->addOption('overwrite', 'o', InputOption::VALUE_OPTIONAL, 'Overwrite the existing translations', true)
            ->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Path to the locale file')
            ->addOption('module', 'm', InputOption::VALUE_OPTIONAL, 'Name of the module that contains the translations')
            ->addOption('locale', 'l', InputOption::VALUE_OPTIONAL, 'Only install for a specific locale');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        // Get input values
        $fileOption = $input->getOption('file');
        $moduleOption = $input->getOption('module');
        $localeOption = $input->getOption('locale');
        $overwriteOption = $input->hasOption('overwrite');

        if (!isset($fileOption) && !isset($moduleOption)) {
            throw new Exception('Please specify a modulename or path to a locale file');
        }

        // Get path to locale file
        $localePath = $this->getLocalePath($fileOption, $moduleOption);

        // Verify existence file
        if (!file_exists($localePath)) {
            throw new Exception('The given locale file (' . $localePath . ') does not exist.');
        }

        // Import locale
        $output->writeln('<info>Importing locale....</info>');
        $this->importLocale($localePath, $overwriteOption, $output, $localeOption);
    }

    private function importLocale(
        string $localePath,
        bool $overwrite,
        OutputInterface $output,
        string $specificLocale = null
    ): void {
        // Load the xml from the file
        $xmlData = @simplexml_load_file($localePath);

        // This is an invalid xml file
        if ($xmlData === false) {
            throw new Exception('Invalid locale.xml file.');
        }

        // Everything ok, let's import the locale
        $results = BackendLocaleModel::importXML(
            $xmlData,
            $overwrite,
            isset($specificLocale) ? [$specificLocale] : null,
            isset($specificLocale) ? [$specificLocale] : null,
            1
        );

        if ($results['total'] < 0) {
            $output->writeln('<error>Something went wrong during import.</error>');

            return;
        }

        if ($results['imported'] > 0) {
            $output->writeln('<comment>Imported ' . $results['imported'] . ' translations succesfully!</comment>');

            return;
        }

        if ($results['imported'] == 0) {
            $output->writeln('<info>No locale was imported. Try adding the overwrite (-o) option.</info>');

            return;
        }
    }

    private function getLocalePath(?string $fileOption, ?string $moduleOption): string
    {
        if (isset($fileOption)) {
            return $fileOption;
        }

        return __DIR__ . '/../../..' . '/src/Backend/Modules/' . ucfirst($moduleOption) . '/Installer/Data/locale.xml';
    }
}
