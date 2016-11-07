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
    public function __construct($addToReference = true)
    {
        if ($addToReference) {
            Model::getContainer()->set('template', $this);
        }

        $this->forkSettings = Model::get('fork.settings');
        $this->debugMode = Model::getContainer()->getParameter('kernel.debug');
        $this->language = BL::getWorkingLanguage();
    }

    /**
     * Fetch the parsed content from this template.
     *
     * @param string $template The location of the template file, used to display this template.
     *
     * @return string The actual parsed content after executing this template.
     */
    public function getContent($template)
    {
        $this->parseConstants();
        $this->parseAuthentication();
        $this->parseAuthenticatedUser();
        $this->parseDebug();
        $this->parseLabels();
        $this->parseLocale();
        $this->parseVars();

        $template = str_replace(BACKEND_MODULES_PATH, '', $template);

        // path to TwigBridge library so we can locate the form theme files.
        $appVariableReflection = new ReflectionClass(AppVariable::class);
        $vendorTwigBridgeDir = dirname($appVariableReflection->getFileName());

        // render the compiled File
        $loader = new Twig_Loader_Filesystem(
            array(
                BACKEND_MODULES_PATH,
                BACKEND_CORE_PATH,
                $vendorTwigBridgeDir . '/Resources/views/Form',
            )
        );

        $twig = new Twig_Environment(
            $loader,
            array(
                'cache' => Model::getContainer()->getParameter('kernel.cache_dir') . '/twig',
                'debug' => $this->debugMode,
            )
        );

        // connect symphony forms
        $formEngine = new TwigRendererEngine(array('Layout/Templates/FormLayout.html.twig'));
        $formEngine->setEnvironment($twig);
        $twig->addExtension(
            new SymfonyFormExtension(
                new TwigRenderer($formEngine, Model::get('security.csrf.token_manager'))
            )
        );

        $twigTranslationExtensionClass = Model::getContainer()->getParameter('twig.extension.trans.class');
        $twig->addExtension(new $twigTranslationExtensionClass(Model::get('translator')));

        // debug options
        if ($this->debugMode === true) {
            $twig->addExtension(new Twig_Extension_Debug());
        }

        if (count($this->forms) > 0) {
            foreach ($this->forms as $form) {
                $twig->addGlobal('form_' . $form->getName(), $form);
            }
        }

        // should always be included, makes it possible to parse SpoonForm in twig
        new FormExtension($twig);

        // start the filters / globals
        TwigFilters::getFilters($twig, 'Backend');
        $this->startGlobals($twig);

        return $twig->render($template, $this->variables);
    }

    /**
     * Parse all user-defined constants
     */
    private function parseConstants()
    {
        // constants that should be protected from usage in the template
        $notPublicConstants = array('DB_TYPE', 'DB_DATABASE', 'DB_HOSTNAME', 'DB_PORT', 'DB_USERNAME', 'DB_PASSWORD');

        // get all defined constants
        $constants = get_defined_constants(true);

        // init var
        $realConstants = array();

        // remove protected constants aka constants that should not be used in the template
        foreach ($constants['user'] as $key => $value) {
            if (!in_array($key, $notPublicConstants)) {
                $realConstants[$key] = $value;
            }
        }

        // we should only assign constants if there are constants to assign
        if (!empty($realConstants)) {
            $this->assign($realConstants);
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
                if ($url->getAction() != '') {
                    $this->assign('ACTION', $url->getAction());
                }

                if ($url->getModule() == 'Core') {
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
                        array('id' => Authentication::getUser()->getUserId())
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
                if ($level == '7') {
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
    }

    /**
     * Assigns an option if we are in debug-mode
     */
    private function parseDebug()
    {
        $this->assign(
            'debug',
            Model::getContainer()->getParameter('kernel.debug')
        );
    }

    /**
     * Assign the labels
     */
    private function parseLabels()
    {
        // grab the current module
        $currentModule = BL::getCurrentModule();

        $errors = BL::getErrors();
        $labels = BL::getLabels();
        $messages = BL::getMessages();

        // set the begin state
        $realErrors = $errors['Core'];
        $realLabels = $labels['Core'];
        $realMessages = $messages['Core'];

        // loop all errors, label, messages and add them again, but prefixed with Core. So we can decide in the
        // template to use the Core-value instead of the one set by the module
        foreach ($errors['Core'] as $key => $value) {
            $realErrors['Core' . $key] = $value;
        }
        foreach ($labels['Core'] as $key => $value) {
            $realLabels['Core' . $key] = $value;
        }
        foreach ($messages['Core'] as $key => $value) {
            $realMessages['Core' . $key] = $value;
        }

        // are there errors for the current module?
        if (isset($errors[$currentModule])) {
            // loop the module-specific errors and reset them in the array with values we will use
            foreach ($errors[$currentModule] as $key => $value) {
                $realErrors[$key] = $value;
            }
        }

        // are there labels for the current module?
        if (isset($labels[$currentModule])) {
            // loop the module-specific labels and reset them in the array with values we will use
            foreach ($labels[$currentModule] as $key => $value) {
                $realLabels[$key] = $value;
            }
        }

        // are there messages for the current module?
        if (isset($messages[$currentModule])) {
            // loop the module-specific errors and reset them in the array with values we will use
            foreach ($messages[$currentModule] as $key => $value) {
                $realMessages[$key] = $value;
            }
        }

        // execute addslashes on the values for the locale, will be used in JS
        if ($this->addSlashes) {
            foreach ($realErrors as &$value) {
                $value = addslashes($value);
            }
            foreach ($realLabels as &$value) {
                $value = addslashes($value);
            }
            foreach ($realMessages as &$value) {
                $value = addslashes($value);
            }
        }

        // sort the arrays (just to make it look beautiful)
        ksort($realErrors);
        ksort($realLabels);
        ksort($realMessages);

        // assign errors
        $this->assignArray($realErrors, 'err');

        // assign labels
        $this->assignArray($realLabels, 'lbl');

        // assign messages
        $this->assignArray($realMessages, 'msg');
    }

    /**
     * Parse the locale (things like months, days, ...)
     */
    private function parseLocale()
    {
        // init vars
        $localeToAssign = array();

        // get months
        $monthsLong = \SpoonLocale::getMonths(BL::getInterfaceLanguage(), false);
        $monthsShort = \SpoonLocale::getMonths(BL::getInterfaceLanguage(), true);

        // get days
        $daysLong = \SpoonLocale::getWeekDays(BL::getInterfaceLanguage(), false, 'sunday');
        $daysShort = \SpoonLocale::getWeekDays(BL::getInterfaceLanguage(), true, 'sunday');

        // build labels
        foreach ($monthsLong as $key => $value) {
            $localeToAssign['locMonthLong' . \SpoonFilter::ucfirst($key)] = $value;
        }
        foreach ($monthsShort as $key => $value) {
            $localeToAssign['locMonthShort' . \SpoonFilter::ucfirst(
                $key
            )] = $value;
        }
        foreach ($daysLong as $key => $value) {
            $localeToAssign['locDayLong' . \SpoonFilter::ucfirst($key)] = $value;
        }
        foreach ($daysShort as $key => $value) {
            $localeToAssign['locDayShort' . \SpoonFilter::ucfirst($key)] = $value;
        }

        // assign
        $this->assignArray($localeToAssign);
    }

    /**
     * Parse some vars
     */
    private function parseVars()
    {
        // assign a placeholder var
        $this->assign('var', '');

        // assign current timestamp
        $this->assign('timestamp', time());

        // check on url object
        if (Model::getContainer()->has('url')) {
            $url = Model::get('url');

            if ($url instanceof Url) {
                $this->assign('bodyID', \SpoonFilter::toCamelCase($url->getModule(), '_', true));

                // build classes
                $bodyClass = \SpoonFilter::toCamelCase($url->getModule() . '_' . $url->getAction(), '_', true);

                // special occasions
                if ($url->getAction() == 'add' || $url->getAction() == 'edit'
                ) {
                    $bodyClass = $url->getModule() . 'AddEdit';
                }

                // assign
                $this->assign('bodyClass', $bodyClass);
            }
        }

        if (Model::has('navigation')) {
            $navigation = Model::get('navigation');
            if ($navigation instanceof Navigation) {
                $navigation->parse($this);
            }
        }

        foreach ($this->forms as $form) {
            if ($form->isSubmitted() && !$form->isCorrect()) {
                $this->assign('form_error', true);
                break;
            }
        }

        $this->assign(
            'cookies',
            Model::get('request')->cookies->all()
        );
    }
}
