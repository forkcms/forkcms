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
    private $installedLanguages;

    /** @var string */
    private $interfaceLanguages;

    /** @var string */
    private $enabledLanguages;

    /** @var string */
    private $redirectLanguages;

    /** @var string */
    private $defaultEnabledLanguage;

    /** @var string */
    private $defaultInterfaceLanguage;

    /**
     * @param ModulesSettings $settings
     * @param string|null $name
     */
    public function __construct(ModulesSettings $settings, $name = null)
    {
        parent::__construct($name);

        $this->settings = $settings;
        $this->installedLanguages = array_flip($this->settings->get('Core', 'languages'));
        $this->interfaceLanguages = array_flip($this->settings->get('Core', 'interface_languages'));
        $this->enabledLanguages = array_flip($this->settings->get('Core', 'languages'));
        $this->redirectLanguages = array_flip($this->settings->get('Core', 'languages'));
        $this->defaultEnabledLanguage = $this->settings->get('Core', 'default_language');
        $this->defaultInterfaceLanguage = $this->settings->get('Core', 'default_interface_language');
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
        $this->input = $input;
        $this->output = $output;
        $this->formatter = new SymfonyStyle($input, $output);

        $this->output->writeln($this->formatter->title('Fork CMS locale enable'));

        $this->showLanguageOverview();
        $this->selectWorkingLanguage();
    }

    private function selectWorkingLanguage()
    {
        $this->workingLocale = $this->formatter->choice(
            'What language would you like to configure',
            $this->getInstallableLanguages()
        );
    }

    private function showLanguageOverview()
    {
        $languages = array_map(
            function ($language, $key) {
                $enabledMessage = null;
                $interfaceMessage = null;

                if ($this->defaultEnabledLanguage === $key) {
                    $enabledMessage = ' (default)';
                }

                if ($this->defaultInterfaceLanguage === $key) {
                    $interfaceMessage = ' (default)';
                }

                return [
                    'key' => $key,
                    'language' => $language,
                    'installed' => array_key_exists($key, $this->installedLanguages) ? 'Y' : 'N',
                    'interface' => (array_key_exists($key, $this->interfaceLanguages) ? 'Y' : 'N') . $interfaceMessage,
                    'enabled' => (array_key_exists($key, $this->enabledLanguages) ? 'Y' : 'N') . $enabledMessage,
                    'redirect' => array_key_exists($key, $this->redirectLanguages) ? 'Y' : 'N',
                ];
            },
            $this->getInstallableLanguages(),
            array_keys($this->getInstallableLanguages())
        );

        $this->formatter->listing(
            [
                "key:\t\tThe identifier of the language",
                "language:\tThe name of the language",
                "installed:\tPossible languages to use as interface, enabled or redirect language",
                "interface:\tLanguages that the user in the backend can use for the interface",
                "enabled:\tLanguages that are accessible for visitors",
                "redirect:\tLanguages that people may automatically be redirected to based upon their browser language",
            ]
        );
        $this->formatter->table(['key', 'language', 'installed', 'interface', 'enabled', 'redirect'], $languages);
    }

    /**
     * @return array
     */
    private function getInstallableLanguages()
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
