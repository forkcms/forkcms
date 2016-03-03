<?php

namespace Backend\Core\Engine;

use Backend\Core\Engine\Language as BL;
use Frontend\Core\Engine\FormExtension;
use Common\Core\Twig\BaseTwigTemplate;
use Common\Core\Twig\Extensions\TwigFilters;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a twig template wrapper
 * that glues spoon libraries and code standards with twig.
 *
 * @author <thijs@wijs.be>
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
     * @param string $template      The location of the template file, used to display this template.
     * @param bool   $customHeaders Are custom headers already set?
     * @param bool   $parseCustom   Parse custom template.
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

        $template = str_replace(BACKEND_MODULES_PATH, "", $template);

        // render the compiled File
        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem(array(
            BACKEND_MODULES_PATH,
            BACKEND_CORE_PATH,
        ));

        $twig = new \Twig_Environment($loader, array(
            'cache' => BACKEND_CACHE_PATH.'/CachedTemplates/Twig_'.($this->debugMode ? 'dev/' : 'prod/'),
            'debug' => $this->debugMode,
        ));

        // debug options
        if ($this->debugMode === true) {
            $twig->addExtension(new \Twig_Extension_Debug());
        }

        if (count($this->forms) > 0) {
            foreach ($this->forms as $form) {
                $twig->addGlobal('form_'.$form->getName(), $form);
            }
        }

        // should always be included
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
        $this->assign('LANGUAGE', Language::getWorkingLanguage());

        // adding parameters
        $this->assign(
            'SITE_MULTILANGUAGE',
            Model::getContainer()->getParameter('site.multilanguage')
        );

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
            Model::get('fork.settings')->get('Core', 'site_title_' . Language::getWorkingLanguage(), SITE_DEFAULT_TITLE)
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
        // get allowed actions
        $allowedActions = (array) Model::get('database')->getRecords(
            'SELECT gra.module, gra.action, MAX(gra.level) AS level
             FROM users_sessions AS us
             INNER JOIN users AS u ON us.user_id = u.id
             INNER JOIN users_groups AS ug ON u.id = ug.user_id
             INNER JOIN groups_rights_actions AS gra ON ug.group_id = gra.group_id
             WHERE us.session_id = ? AND us.secret_key = ?
             GROUP BY gra.module, gra.action',
            array(\SpoonSession::getSessionId(), \SpoonSession::get('backend_secret_key'))
        );

        // loop actions and assign to template
        foreach ($allowedActions as $action) {
            if ($action['level'] == '7') {
                $this->assign(
                    'show' . \SpoonFilter::toCamelCase($action['module'], '_') . \SpoonFilter::toCamelCase(
                        $action['action'],
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
        if (Model::getContainer()->has('url')) {
            $currentModule = Model::get('url')->getModule();
        } elseif (isset($_GET['module']) && $_GET['module'] != '') {
            $currentModule = (string) $_GET['module'];
        } else {
            $currentModule = 'Core';
        }

        $errors = Language::getErrors();
        $labels = Language::getLabels();
        $messages = Language::getMessages();

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
        $monthsLong = \SpoonLocale::getMonths(Language::getInterfaceLanguage(), false);
        $monthsShort = \SpoonLocale::getMonths(Language::getInterfaceLanguage(), true);

        // get days
        $daysLong = \SpoonLocale::getWeekDays(Language::getInterfaceLanguage(), false, 'sunday');
        $daysShort = \SpoonLocale::getWeekDays(Language::getInterfaceLanguage(), true, 'sunday');

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

        // assign body ID
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

        foreach ($this->forms as $form) {
            if ($form->isSubmitted() && !$form->isCorrect()) {
                $this->assign('form_error', true);
                break;
            }
        }
    }
}
