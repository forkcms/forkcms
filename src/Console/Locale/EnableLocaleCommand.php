<?php

namespace Console\Locale;

use Common\ModulesSettings;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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

    /** @var ModulesSettings */
    private $settings;

    /** @var string */
    private $workingLocale;

    /** @var string */
    private $installedLocale;

    /** @var string */
    private $interfaceLocale;

    /** @var string */
    private $enabledLocale;

    /** @var string */
    private $redirectLocale;

    /** @var string */
    private $defaultEnabledLocale;

    /** @var string */
    private $defaultInterfaceLocale;

    /**
     * @param ModulesSettings $settings
     * @param string|null $name
     */
    public function __construct(ModulesSettings $settings, $name = null)
    {
        parent::__construct($name);

        $this->settings = $settings;
        $this->installedModules = $installedModules;
    }

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
        $this->installedLocale = array_flip($this->settings->get('Core', 'languages'));
        $this->interfaceLocale = array_flip($this->settings->get('Core', 'interface_languages'));
        $this->enabledLocale = array_flip($this->settings->get('Core', 'active_languages'));
        $this->redirectLocale = array_flip($this->settings->get('Core', 'redirect_languages'));
        $this->defaultEnabledLocale = $this->settings->get('Core', 'default_language');
        $this->defaultInterfaceLocale = $this->settings->get('Core', 'default_interface_language');

        $this->input = $input;
        $this->output = $output;
        $this->formatter = new SymfonyStyle($input, $output);

        $this->output->writeln($this->formatter->title('Fork CMS locale enable'));

        $this->showLocaleOverview();
        $this->selectWorkingLocale();
        if (!$this->askToInstall()) {
            return;
        }
    }

    /**
     * @return bool
     */
    private function askToInstall()
    {
        if (array_key_exists($this->workingLocale, $this->installedLocale)) {
            $reinstallLocale = $this->formatter->confirm(
                'The locale is already installed, would you like to reinstall and overwrite the current translations?',
                false
            );

            if (!$reinstallLocale) {
                return true;
            }

            $this->installWorkingLocale(true);

            return true;
        }

        $install = $this->formatter->confirm(
            'Would you like to install this locale?'
        );

        if (!$install) {
            return false;
        }

        $this->installWorkingLocale();

        return true;
    }

    /**
     * @param bool $force
     */
    private function installWorkingLocale($force = false)
    {
        // @TODO
    }

    private function selectWorkingLocale()
    {
        $this->workingLocale = $this->formatter->choice(
            'What locale would you like to configure',
            $this->getInstallableLocale()
        );
    }

    private function showLocaleOverview()
    {
        $locale = array_map(
            function ($locale, $key) {
                $enabledMessage = null;
                $interfaceMessage = null;

                if ($this->defaultEnabledLocale === $key) {
                    $enabledMessage = ' (default)';
                }

                if ($this->defaultInterfaceLocale === $key) {
                    $interfaceMessage = ' (default)';
                }

                return [
                    'key' => $key,
                    'locale' => $locale,
                    'installed' => array_key_exists($key, $this->installedLocale) ? 'Y' : 'N',
                    'interface' => (array_key_exists($key, $this->interfaceLocale) ? 'Y' : 'N') . $interfaceMessage,
                    'enabled' => (array_key_exists($key, $this->enabledLocale) ? 'Y' : 'N') . $enabledMessage,
                    'redirect' => array_key_exists($key, $this->redirectLocale) ? 'Y' : 'N',
                ];
            },
            $this->getInstallableLocale(),
            array_keys($this->getInstallableLocale())
        );

        $this->formatter->listing(
            [
                "key:\t\tThe identifier of the locale",
                "locale:\tThe name of the locale",
                "installed:\tPossible locale to use as interface, enabled or redirect locale",
                "interface:\tLocale that the user in the backend can use for the interface",
                "enabled:\tLocale that are accessible for visitors",
                "redirect:\tLocale that people may automatically be redirected to based upon their browser locale",
            ]
        );
        $this->formatter->table(['key', 'locale', 'installed', 'interface', 'enabled', 'redirect'], $locale);
    }

    /**
     * @return array
     */
    private function getInstallableLocale()
    {
        return [
            'en' => 'English',
            'zh' => 'Chinese',
            'nl' => 'Dutch',
            'fr' => 'French',
            'de' => 'German',
            'el' => 'Greek',
            'hu' => 'Hungarian',
            'it' => 'Italian',
            'lt' => 'Lithuanian',
            'ru' => 'Russian',
            'es' => 'Spanish',
            'sv' => 'Swedish',
            'uk' => 'Ukrainian',
        ];
    }
}
