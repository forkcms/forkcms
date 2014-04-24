<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model as BackendModel;

/**
 * This is our extended version of \SpoonTemplate
 * This class will handle a lot of stuff for you, for example:
 *    - it will assign all labels
 *    - it will map some modifiers
 *    - it will assign a lot of constants
 *    - ...
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Template extends \SpoonTemplate
{
    /**
     * Should we add slashes to each value?
     *
     * @var bool
     */
    private $addSlashes = false;

    /**
     * URL instance
     *
     * @var    Url
     */
    private $URL;

    /**
     * The constructor will store the instance in the reference, preset some settings and map the custom modifiers.
     *
     * @param bool $addToReference Should the instance be added into the reference.
     */
    public function __construct($addToReference = true)
    {
        parent::__construct();

        // get URL instance
        if (BackendModel::getContainer()->has('url')) {
            $this->URL = BackendModel::getContainer()->get('url');
        }

        // store in reference so we can access it from everywhere
        if ($addToReference) {
            BackendModel::getContainer()->set('template', $this);
        }

        // set cache directory
        $this->setCacheDirectory(BACKEND_CACHE_PATH . '/CachedTemplates');

        // set compile directory
        $this->setCompileDirectory(BACKEND_CACHE_PATH . '/CompiledTemplates');

        // when debugging, the template should be recompiled every time
        $this->setForceCompile(SPOON_DEBUG);

        // map custom modifiers
        $this->mapCustomModifiers();

        // parse authentication levels
        $this->parseAuthentication();
    }

    /**
     * Output the template into the browser
     * Will also assign the interfacelabels and all user-defined constants.
     *
     * @param string $template The path for the template.
     */
    public function display($template)
    {
        $this->parseConstants();
        $this->parseAuthenticatedUser();
        $this->parseDebug();
        $this->parseLabels();
        $this->parseLocale();
        $this->parseVars();

        parent::display($template);
    }

    /**
     * Map the fork-specific modifiers
     */
    private function mapCustomModifiers()
    {
        // convert var into an URL, syntax {$var|geturl:<pageId>}
        $this->mapModifier('geturl', array('Backend\Core\Engine\TemplateModifiers', 'getURL'));

        // convert var into navigation, syntax {$var|getnavigation:<startdepth>:<enddepth>}
        $this->mapModifier('getnavigation', array('Backend\Core\Engine\TemplateModifiers', 'getNavigation'));

        // convert var into navigation, syntax {$var|getmainnavigation}
        $this->mapModifier('getmainnavigation', array('Backend\Core\Engine\TemplateModifiers', 'getMainNavigation'));

        // rand
        $this->mapModifier('rand', array('Backend\Core\Engine\TemplateModifiers', 'random'));

        // string
        $this->mapModifier('formatfloat', array('Backend\Core\Engine\TemplateModifiers', 'formatFloat'));
        $this->mapModifier('truncate', array('Backend\Core\Engine\TemplateModifiers', 'truncate'));
        $this->mapModifier('camelcase', array('\SpoonFilter', 'toCamelCase'));
        $this->mapModifier('stripnewlines', array('Backend\Core\Engine\TemplateModifiers', 'stripNewlines'));

        // debug stuff
        $this->mapModifier('dump', array('Backend\Core\Engine\TemplateModifiers', 'dump'));

        // dates
        $this->mapModifier('formatdate', array('Backend\Core\Engine\TemplateModifiers', 'formatDate'));
        $this->mapModifier('formattime', array('Backend\Core\Engine\TemplateModifiers', 'formatTime'));
        $this->mapModifier('formatdatetime', array('Backend\Core\Engine\TemplateModifiers', 'formatDateTime'));

        // numbers
        $this->mapModifier('formatnumber', array('Backend\Core\Engine\TemplateModifiers', 'formatNumber'));

        // label (locale)
        $this->mapModifier('tolabel', array('Backend\Core\Engine\TemplateModifiers', 'toLabel'));
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
                    BackendModel::createURLForAction(
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
        // init var
        $db = BackendModel::getContainer()->get('database');

        // get allowed actions
        $allowedActions = (array) $db->getRecords(
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

        if ($this->URL instanceof Url) {
            // assign the current module
            $this->assign('MODULE', $this->URL->getModule());

            // assign the current action
            if ($this->URL->getAction() != '') {
                $this->assign('ACTION', $this->URL->getAction());
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
            BackendModel::getModuleSetting('Core', 'site_title_' . Language::getWorkingLanguage(), SITE_DEFAULT_TITLE)
        );
    }

    /**
     * Assigns an option if we are in debug-mode
     */
    private function parseDebug()
    {
        $this->assign('debug', SPOON_DEBUG);
    }

    /**
     * Assign the labels
     */
    private function parseLabels()
    {
        // grab the current module
        if (BackendModel::getContainer()->has('url')) {
            $currentModule = BackendModel::getContainer()->get(
                'url'
            )->getModule();
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
        if ($this->URL instanceof Url) {
            $this->assign('bodyID', \SpoonFilter::toCamelCase($this->URL->getModule(), '_', true));

            // build classes
            $bodyClass = \SpoonFilter::toCamelCase($this->URL->getModule() . '_' . $this->URL->getAction(), '_', true);

            // special occasions
            if ($this->URL->getAction() == 'add' || $this->URL->getAction() == 'edit'
            ) {
                $bodyClass = $this->URL->getModule() . 'AddEdit';
            }

            // assign
            $this->assign('bodyClass', $bodyClass);
        }
    }

    /**
     * Should we execute addSlashed on the locale?
     *
     * @param bool $on Enable addslashes.
     */
    public function setAddSlashes($on = true)
    {
        $this->addSlashes = (bool) $on;
    }
}
