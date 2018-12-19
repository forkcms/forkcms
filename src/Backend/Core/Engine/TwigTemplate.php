<?php

namespace Backend\Core\Engine;

use Backend\Core\Language\Language as BL;
use Common\Core\Twig\BaseTwigTemplate;
use ReflectionClass;
use Symfony\Bridge\Twig\AppVariable;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

/**
 * This is a twig template wrapper
 * that glues spoon libraries and code standards with twig.
 */
class TwigTemplate extends BaseTwigTemplate
{
    /**
     * The constructor will store the instance in the reference, preset some settings and map the custom modifiers.
     *
     * @param bool $addToReference Should the instance be added into the reference.
     */
    public function __construct(bool $addToReference = true)
    {
        $this->container = Model::getContainer();
        $this->language = BL::getWorkingLanguage();

        parent::__construct(
            clone $this->container->get('twig'),
            $this->container->get('templating.name_parser.public'),
            clone $this->container->get('templating.locator'),
            'Backend'
        );

        if ($addToReference) {
            $this->container->set('template', $this);
        }
    }

    /**
     * Fetch the parsed content from this template.
     *
     * @param string $template The location of the template file, used to display this template.
     *
     * @return string The actual parsed content after executing this template.
     */
    public function getContent(string $template): string
    {
        $this->parseUserDefinedConstants();
        $this->parseAuthenticationSettingsForTheAuthenticatedUser();
        $this->parseAuthenticatedUser();
        $this->parseDebug();
        $this->parseTranslations();
        $this->parseVars();
        $this->startGlobals($this->environment);

        return $this->render(str_replace(BACKEND_MODULES_PATH, '', $template), $this->variables);
    }

    protected function getTemplateLoader(): LoaderInterface
    {
        $appVariableReflection = new ReflectionClass(AppVariable::class);
        $vendorTwigBridgeDir = dirname($appVariableReflection->getFileName());

        return new FilesystemLoader(
            [
                BACKEND_MODULES_PATH,
                BACKEND_CORE_PATH,
                $vendorTwigBridgeDir . '/Resources/views/Form',
            ]
        );
    }

    protected function getDefaultThemes(): array
    {
        return ['Layout/Templates/FormLayout.html.twig'];
    }

    private function parseUserDefinedConstants(): void
    {
        // get all defined constants
        $constants = get_defined_constants(true);

        // we should only assign constants if there are constants to assign
        if (!empty($constants['user'])) {
            $this->assignArray($constants['user']);
        }

        // we use some abbreviations and common terms, these should also be assigned
        $this->assign('LANGUAGE', BL::getWorkingLanguage());

        // check on url object
        if ($this->container->has('url')) {
            $url = $this->container->get('url');

            if ($url instanceof Url) {
                // assign the current module
                $this->assign('MODULE', $url->getModule());

                // assign the current action
                if ($url->getAction() !== '') {
                    $this->assign('ACTION', $url->getAction());
                }

                if ($url->getModule() === 'Core') {
                    $this->assign(
                        'BACKEND_MODULE_PATH',
                        BACKEND_PATH . '/' . $url->getModule()
                    );
                } else {
                    $this->assign(
                        'BACKEND_MODULE_PATH',
                        BACKEND_MODULES_PATH . '/' . $url->getModule()
                    );
                }
            }
        }

        // is the user object filled?
        if (Authentication::getUser()->isAuthenticated()) {
            // assign the authenticated users secret key
            $this->assign('SECRET_KEY', Authentication::getUser()->getSecretKey());

            // assign the authenticated users preferred interface language
            $this->assign('INTERFACE_LANGUAGE', (string) Authentication::getUser()->getSetting('interface_language'));
        }

        // assign some variable constants (such as site-title)
        $this->assign(
            'SITE_TITLE',
            $this->forkSettings->get('Core', 'site_title_' . BL::getWorkingLanguage(), SITE_DEFAULT_TITLE)
        );
    }

    private function parseAuthenticatedUser(): void
    {
        // check if the current user is authenticated
        if (Authentication::getUser()->isAuthenticated()) {
            // show stuff that only should be visible if authenticated
            $this->assign('isAuthenticated', true);

            // get authenticated user-settings
            $settings = (array) Authentication::getUser()->getSettings();

            foreach ($settings as $key => $setting) {
                $this->assign('authenticatedUser' . \SpoonFilter::toCamelCase($key), $setting ?? '');
            }

            // check if this action is allowed
            if (Authentication::isAllowedAction('Edit', 'Users')) {
                // assign special vars
                $this->assign(
                    'authenticatedUserEditUrl',
                    Model::createUrlForAction(
                        'Edit',
                        'Users',
                        null,
                        ['id' => Authentication::getUser()->getUserId()]
                    )
                );
            }
        }
    }

    private function parseAuthenticationSettingsForTheAuthenticatedUser(): void
    {
        // loop actions and assign to template
        foreach (Authentication::getAllowedActions() as $module => $allowedActions) {
            foreach ($allowedActions as $action => $level) {
                if ($level !== 7) {
                    continue;
                }

                $this->assign(
                    'show' . \SpoonFilter::toCamelCase($module, '_') . \SpoonFilter::toCamelCase(
                        $action,
                        '_'
                    ),
                    true
                );
            }
        }
    }

    private function parseDebug(): void
    {
        $this->assign('debug', $this->container->getParameter('kernel.debug'));
    }

    private function parseLabels(array $labels, string $module, string $key): void
    {
        $realLabels = $this->prefixArrayKeys('Core', $labels['Core']);

        if (array_key_exists($module, $labels)) {
            $realLabels = array_merge($realLabels, $labels[$module]);
        }

        if ($this->addSlashes) {
            $realLabels = array_map('addslashes', $realLabels);
        }

        // just so the dump is nicely sorted
        ksort($realLabels);

        $this->assignArray($realLabels, $key);
    }

    private function prefixArrayKeys(string $prefix, array $array): array
    {
        return array_combine(
            array_map(
                function ($key) use ($prefix) {
                    return $prefix . \SpoonFilter::ucfirst($key);
                },
                array_keys($array)
            ),
            $array
        );
    }

    private function parseTranslations(): void
    {
        $currentModule = BL::getCurrentModule();
        $this->parseLabels(BL::getErrors(), $currentModule, 'err');
        $this->parseLabels(BL::getLabels(), $currentModule, 'lbl');
        $this->parseLabels(BL::getMessages(), $currentModule, 'msg');

        $interfaceLanguage = BL::getInterfaceLanguage();
        $this->assignArray($this->prefixArrayKeys('locMonthLong', \SpoonLocale::getMonths($interfaceLanguage, false)));
        $this->assignArray($this->prefixArrayKeys('locMonthShort', \SpoonLocale::getMonths($interfaceLanguage, true)));
        $this->assignArray($this->prefixArrayKeys('locDayLong', \SpoonLocale::getWeekDays($interfaceLanguage, false)));
        $this->assignArray($this->prefixArrayKeys('locDayShort', \SpoonLocale::getWeekDays($interfaceLanguage, true)));
    }

    private function parseVars(): void
    {
        $this->assign('var', '');
        $this->assign('timestamp', time());
        $this->addBodyClassAndId();
        $this->parseNavigation();

        foreach ($this->forms as $form) {
            if ($form->isSubmitted() && !$form->isCorrect()) {
                $this->assign('form_error', true);
                break;
            }
        }

        $this->assign('cookies', Model::getRequest()->cookies->all());
    }

    private function parseNavigation(): void
    {
        if (!Model::has('navigation')) {
            return;
        }

        $navigation = $this->container->get('navigation');
        if ($navigation instanceof Navigation) {
            $navigation->parse($this);
        }
    }

    private function addBodyClassAndId(): void
    {
        if (!$this->container->has('url')) {
            return;
        }

        $url = $this->container->get('url');

        if (!$url instanceof Url) {
            return;
        }

        $this->assign('bodyID', \SpoonFilter::toCamelCase($url->getModule(), '_', true));
        $bodyClass = \SpoonFilter::toCamelCase($url->getModule() . '_' . $url->getAction(), '_', true);
        if (in_array(mb_strtolower($url->getAction()), ['add', 'edit'], true)) {
            $bodyClass = $url->getModule() . 'AddEdit';
        }
        $this->assign('bodyClass', $bodyClass);
    }
}
