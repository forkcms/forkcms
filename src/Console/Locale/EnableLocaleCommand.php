<?php

namespace Console\Locale;

use Backend\Core\Engine\Authentication;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Common\ModulesSettings;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
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

    /** @var array */
    private $installedLocale;

    /** @var array */
    private $interfaceLocale;

    /** @var array */
    private $enabledLocale;

    /** @var array */
    private $redirectLocale;

    /** @var string */
    private $defaultEnabledLocale;

    /** @var string */
    private $defaultInterfaceLocale;

    /** @var array */
    private $installedModules;

    /** @var bool */
    private $multiLanguageIsEnabled;

    public function __construct(
        ModulesSettings $settings,
        array $installedModules,
        bool $multiLanguageIsEnabled,
        string $name = null
    ) {
        parent::__construct($name);

        $this->settings = $settings;
        $this->multiLanguageIsEnabled = $multiLanguageIsEnabled;

        // some core modules don't have locale so we remove them to prevent showing errors we know of
        $this->installedModules = array_filter(
            $installedModules,
            function ($installedModule) {
                return !in_array($installedModule, ['Error', 'Core', 'Authentication']);
            }
        );
    }

    protected function configure(): void
    {
        $this->setName('forkcms:locale:enable')
            ->setDescription('Enable a locale');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->input = $input;
        $this->output = $output;
        $this->formatter = new SymfonyStyle($input, $output);

        if (!$this->multiLanguageIsEnabled) {
            $this->formatter->error(
                [
                    'site.multilanguage should be set to true in parameters.yml.',
                    'Warning: All your urls will change from example.com/page to example.com/[locale]/page',
                ]
            );

            return;
        }

        $this->installedLocale = array_flip($this->settings->get('Core', 'languages'));
        $this->interfaceLocale = array_flip($this->settings->get('Core', 'interface_languages'));
        $this->enabledLocale = array_flip($this->settings->get('Core', 'active_languages'));
        $this->redirectLocale = array_flip($this->settings->get('Core', 'redirect_languages'));
        $this->defaultEnabledLocale = $this->settings->get('Core', 'default_language');
        $this->defaultInterfaceLocale = $this->settings->get('Core', 'default_interface_language');

        $this->output->writeln($this->formatter->title('Fork CMS locale enable'));

        $this->showLocaleOverview();
        $this->selectWorkingLocale();
        if (!$this->askToInstall()) {
            return;
        }
        $this->askToAddInterfaceLocale();
        if ($this->askToMakeTheLocaleAccessibleToVisitors()) {
            $this->askToEnableTheLocaleForRedirecting();
        }
    }

    private function askToEnableTheLocaleForRedirecting(): void
    {
        $enableRedirect = $this->formatter->confirm(
            'Would you like to redirect visitors based on their browser locale to this locale?'
        );

        if (!$enableRedirect) {
            return;
        }

        $this->redirectLocale = array_flip($this->redirectLocale);
        $this->redirectLocale[] = $this->workingLocale;
        $this->settings->set('Core', 'redirect_languages', $this->redirectLocale);
        $this->redirectLocale = array_flip($this->redirectLocale);
    }

    private function askToMakeTheLocaleAccessibleToVisitors(): bool
    {
        $makeAccessible = $this->formatter->confirm(
            'Would you like to make this locale accessible to visitors?'
        );

        if (!$makeAccessible) {
            return false;
        }

        $this->enabledLocale = array_flip($this->enabledLocale);
        $this->enabledLocale[] = $this->workingLocale;
        $this->settings->set('Core', 'active_languages', $this->enabledLocale);
        $this->enabledLocale = array_flip($this->enabledLocale);

        $makeDefault = $this->formatter->confirm(
            'Would you like to make this locale the default locale for visitors?',
            false
        );

        if (!$makeDefault) {
            return true;
        }

        $this->defaultEnabledLocale = $this->workingLocale;
        $this->settings->set('Core', 'default_language', $this->workingLocale);

        return true;
    }

    private function askToAddInterfaceLocale(): void
    {
        $addToInterfaceLocale = $this->formatter->confirm(
            'Would you like to add this locale to the interface locale?'
        );

        if (!$addToInterfaceLocale) {
            return;
        }

        $this->interfaceLocale = array_flip($this->interfaceLocale);
        $this->interfaceLocale[] = $this->workingLocale;
        $this->settings->set('Core', 'interface_languages', $this->interfaceLocale);
        $this->interfaceLocale = array_flip($this->interfaceLocale);

        $makeDefault = $this->formatter->confirm(
            'Would you like to make this locale the default interface locale?',
            false
        );

        if (!$makeDefault) {
            return;
        }

        $this->defaultInterfaceLocale = $this->workingLocale;
        $this->settings->set('Core', 'default_interface_language', $this->workingLocale);
    }

    private function askToInstall(): bool
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

        $this->formatter->writeln(
            '<info>Before you can enable a new locale you need to authenticate to be able to create the pages</info>'
        );

        while (!Authentication::loginUser($this->formatter->ask('Login'), $this->formatter->askHidden('Password'))) {
            $this->formatter->error('Failed to login, please try again');
        }

        if (!Authentication::isAllowedAction('Copy', 'Pages')) {
            $this->formatter->error(
                'Your profile doesn\'t have the permission to execute the action Copy of the Pages module'
            );

            return false;
        }

        $this->installWorkingLocale();

        $this->formatter->writeln('<info>Copying pages from the default locale to the current locale</info>');
        BackendPagesModel::copy($this->defaultEnabledLocale, $this->workingLocale);

        return true;
    }

    private function installWorkingLocale(bool $force = false): void
    {
        $installLocaleCommand = $this->getApplication()->find('forkcms:locale:import');
        $installBackendLocaleCommandArguments = [
            '-f' => PATH_WWW . '/src/Backend/Core/Installer/Data/locale.xml',
            '-o' => $force,
            '-l' => $this->workingLocale,
        ];
        $this->formatter->writeln('<info>Installing Core locale</info>');
        $installLocaleCommand->run(new ArrayInput($installBackendLocaleCommandArguments), $this->output);

        foreach ($this->installedModules as $installedModule) {
            $installModuleLocaleCommandArguments = [
                '-m' => $installedModule,
                '-o' => $force,
                '-l' => $this->workingLocale,
            ];

            $this->formatter->writeln('<info>Installing ' . $installedModule . ' locale</info>');
            try {
                $installLocaleCommand->run(new ArrayInput($installModuleLocaleCommandArguments), $this->output);
            } catch (Exception $exception) {
                $this->formatter->error($installedModule . ': skipped because ' . $exception->getMessage());
            }
        }

        if (!array_key_exists($this->workingLocale, $this->installedLocale)) {
            // add the working locale to the installed locale
            $this->installedLocale = array_flip($this->installedLocale);
            $this->installedLocale[] = $this->workingLocale;
            $this->settings->set('Core', 'languages', $this->installedLocale);
            $this->installedLocale = array_flip($this->installedLocale);
        }
    }

    private function selectWorkingLocale(): void
    {
        $this->workingLocale = $this->formatter->choice(
            'What locale would you like to configure',
            $this->getInstallableLocale()
        );
    }

    private function showLocaleOverview(): void
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

    private function getInstallableLocale(): array
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
