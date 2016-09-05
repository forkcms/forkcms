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
    /**
     * Configure the command options.
     */
    protected function configure()
    {
        $this->setName('locale:import')
             ->setDescription('Import locale translations')
             ->addOption('overwrite', 'o', InputOption::VALUE_OPTIONAL, 'Overwrite the existing locale', true)
             ->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Path to the locale file')
             ->addOption('module', 'm', InputOption::VALUE_OPTIONAL, 'Name of the module that contains the locale');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws Exception
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get input values
        $fileOption = $input->getOption('file');
        $moduleOption = $input->getOption('module');
        $overwriteOption = $input->hasOption('overwrite') ? true : false;

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
        $this->importLocale($localePath, $overwriteOption, $output);
    }

    /**
     * @param string $localePath
     * @param bool $overwrite
     * @param OutputInterFace $output
     *
     * @throws Exception
     */
    private function importLocale($localePath, $overwrite, OutputInterface $output)
    {
        // Load the xml from the file
        $xmlData = @simplexml_load_file($localePath);

        // This is an invalid xml file
        if ($xmlData === false) {
            throw new Exception('Invalid locale.xml file.');
        }

        // Everything ok, let's import the locale
        $results = BackendLocaleModel::importXML($xmlData, $overwrite, null, null, 1);

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

    /**
     * Get the file or module path according to the input options
     *
     * @param string $fileOption
     * @param string $moduleOption
     *
     * @return string
     */
    private function getLocalePath($fileOption, $moduleOption)
    {
        if (isset($fileOption)) {
            return $fileOption;
        }

        return __DIR__ . '/../../..' . '/src/Backend/Modules/' . ucfirst($moduleOption) . '/Installer/Data/locale.xml';
    }
}
