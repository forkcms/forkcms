<?php

namespace Backend\Core\Engine;

use Backend\Core\Language\Language as BL;
use Frontend\Core\Engine\FormExtension;
use Common\Core\Twig\BaseTwigTemplate;
use Common\Core\Twig\Extensions\TwigFilters;
use ReflectionClass;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Bridge\Twig\Extension\FormExtension as SymfonyFormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

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
        $container = Model::getContainer();
        parent::__construct(
            $this->buildTwigEnvironmentForTheBackend(),
            $container->get('templating.name_parser'),
            $container->get('templating.locator')
        );

        if ($addToReference) {
            $container->set('template', $this);
        }

        $this->forkSettings = $container->get('fork.settings');
        $this->debugMode = $container->getParameter('kernel.debug');
        $this->language = BL::getWorkingLanguage();
        $this->connectSymfonyForms();
        $this->connectSymfonyTranslator();
        $this->connectSpoonForm();
        TwigFilters::addFilters($this->environment, 'Backend');
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
        $this->parseConstants();
        $this->parseAuthentication();
        $this->parseAuthenticatedUser();
        $this->parseDebug();
        $this->parseTranslations();
        $this->parseVars();
        $this->startGlobals($this->environment);

        if (count($this->forms) > 0) {
            foreach ($this->forms as $form) {
                $this->environment->addGlobal('form_' . $form->getName(), $form);
            }
        }

        return $this->render(str_replace(BACKEND_MODULES_PATH, '', $template), $this->variables);
    }

    /**
     * @return Twig_Environment
     */
    private function buildTwigEnvironmentForTheBackend(): Twig_Environment
    {
        // path to TwigBridge library so we can locate the form theme files.
        $appVariableReflection = new ReflectionClass(AppVariable::class);
        $vendorTwigBridgeDir = dirname($appVariableReflection->getFileName());

        // render the compiled File
        $loader = new Twig_Loader_Filesystem(
            [
                BACKEND_MODULES_PATH,
                BACKEND_CORE_PATH,
                $vendorTwigBridgeDir . '/Resources/views/Form',
            ]
        );

        return new Twig_Environment(
            $loader,
            [
                'cache' => Model::getContainer()->getParameter('kernel.cache_dir') . '/twig',
                'debug' => $this->debugMode,
            ]
        );
    }

    private function connectSymfonyForms()
    {
        $formEngine = new TwigRendererEngine(['Layout/Templates/FormLayout.html.twig']);
        $formEngine->setEnvironment($this->environment);
        $this->environment->addExtension(
            new SymfonyFormExtension(
                new TwigRenderer($formEngine, Model::get('security.csrf.token_manager'))
            )
        );
    }

    private function connectSymfonyTranslator()
    {
        $twigTranslationExtensionClass = Model::getContainer()->getParameter('twig.extension.trans.class');
        $this->environment->addExtension(new $twigTranslationExtensionClass(Model::get('translator')));
    }

    private function connectSpoonForm()
    {
        new FormExtension($this->environment);
    }

    /**
     * Parse all user-defined constants
     */
    private function parseConstants()
    {
        // constants that should be protected from usage in the template
        $notPublicConstants = ['DB_TYPE', 'DB_DATABASE', 'DB_HOSTNAME', 'DB_PORT', 'DB_USERNAME', 'DB_PASSWORD'];

        // get all defined constants
        $constants = get_defined_constants(true);

        // init var
        $realConstants = [];

        // remove protected constants aka constants that should not be used in the template
        foreach ($constants['user'] as $key => $value) {
            if (!in_array($key, $notPublicConstants)) {
                $realConstants[$key] = $value;
            }
        }

        // we should only assign constants if there are constants to assign
        if (!empty($realConstants)) {
            $this->assignArray($realConstants);
        }

        // we use some abbreviations and common terms, these should also be assigned
        $this->assign('LANGUAGE', BL::getWorkingLanguage());

        // check on url object
        if (Model::getContainer()->has('url')) {
            $url = Model::get('url');

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
            Model::get('fork.settings')->get('Core', 'site_title_' . BL::getWorkingLanguage(), SITE_DEFAULT_TITLE)
        );
    }

    /**
     * Parse the settings for the authenticated user
     */
    private function parseAuthenticatedUser()
    {
        // check if the current user is authenticated
        if (Authentication::getUser()->isAuthenticated()) {
            // show stuff that only should be visible if authenticated
            $this->assign('isAuthenticated', true);

            // get authenticated user-settings
            $settings = (array) Authentication::getUser()->getSettings();

            foreach ($settings as $key => $setting) {
                // redefine setting
                $setting = ($setting === null) ? '' : $setting;

                // assign setting
                $this->assign('authenticatedUser' . \SpoonFilter::toCamelCase($key), $setting);
            }

            // check if this action is allowed
            if (Authentication::isAllowedAction('Edit', 'Users')) {
                // assign special vars
                $this->assign(
                    'authenticatedUserEditUrl',
                    Model::createURLForAction(
                        'Edit',
                        'Users',
                        null,
                        ['id' => Authentication::getUser()->getUserId()]
                    )
                );
            }
        }
    }

    /**
     * Parse the authentication settings for the authenticated user
     */
    private function parseAuthentication()
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

    /**
     * Assigns an option if we are in debug-mode
     */
    private function parseDebug()
    {
        $this->assign('debug', Model::getContainer()->getParameter('kernel.debug'));

        if ($this->debugMode === true && !$this->environment->hasExtension(Twig_Extension_Debug::class)) {
            $this->environment->addExtension(new Twig_Extension_Debug());
        }
    }

    /**
     * @param array $labels
     * @param string $module
     * @param string $key
     */
    private function parseLabels(array $labels, string $module, string $key)
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

    /**
     * @param string $prefix
     * @param array $array
     *
     * @return array
     */
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

    /**
     * Make the translations available in the template
     */
    private function parseTranslations()
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

    /**
     * Parse some vars
     */
    private function parseVars()
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

        $this->assign('cookies', Model::get('request')->cookies->all());
    }

    private function parseNavigation()
    {
        if (!Model::has('navigation')) {
            return;
        }

        $navigation = Model::get('navigation');
        if ($navigation instanceof Navigation) {
            $navigation->parse($this);
        }
    }

    private function addBodyClassAndId()
    {
        if (!Model::getContainer()->has('url')) {
            return;
        }

        $url = Model::get('url');

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
