<?php

namespace Frontend\Core\Engine;

use Backend\Core\Engine\Language as BL;
use Frontend\Core\Engine\Language as FL;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a twig template wrapper
 * that glues spoon libraries and code standards with twig
 *
 * @author <thijs@wijs.be>
 */

Class TwigTemplate
{
    /**
     * Should we add slashes to each value?
     *
     * @var bool
     */
    private $addSlashes = false;

    /**
     * Debug mode
     *
     * @var bool
     */
    private $debugMode = false;

    /**
     * List of form objects
     *
     * @var array
     */
    private $forms = array();

    /**
     * List of assigned variables
     *
     * @var array
     */
    private $variables = array();

    /**
     * List of passed templates
     *
     * @var array
     */
    private $templates = array();

    /**
     * theme path location
     *
     * @var string
     */
    private $themePath;

    /**
     * module path location
     *
     * @var string
     */
    private $modulePath;

    /**
     * Base file location
     *
     * @var string
     */
    private $baseFile;

    /**
     * The constructor will store the instance in the reference, preset some settings and map the custom modifiers.
     *
     * @param bool $addToReference Should the instance be added into the reference.
     */
    function __construct($addToReference = true)
    {
        if ($addToReference) {
            Model::getContainer()->set('template', $this);
        }

        $this->themePath = FRONTEND_PATH . '/Themes/' . Model::getModuleSetting('Core', 'theme', 'default');
        $this->modulePath = FRONTEND_MODULES_PATH;
        $this->debugMode = Model::getContainer()->getParameter('kernel.debug');

        // move to kernel parameter
        require_once PATH_WWW . '/vendor/twig/twig/lib/Twig/Autoloader.php';

        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem(array($this->themePath, $this->modulePath));
        $this->twig = new \Twig_Environment($loader, array(
            'cache' => FRONTEND_CACHE_PATH . '/CachedTemplates/Twig',
            'debug' => ($this->debugMode === false)
        ));

        // debug options
        if ($this->debugMode === true) {
            $this->twig->addExtension(new \Twig_Extension_Debug());
        }
    }

    /**
     * Returns the template type
     *
     * @return string Returns the template type
     */
    public function getTemplateType()
    {
        return 'twig';
    }

    /**
     * Spoon assign method
     *
     * @param string $key
     * @param mixed $values
     *
     */
    public function assign($key, $values = null)
    {
        // page hook
        // last call
        if ($key === 'page') {
            $this->baseFile = $this->convertExtension($values['template_path']);
            $this->positions = $values['positions'];
            return;
        }

        // in all other cases
        $this->variables[$key] = $values;
    }

    /**
     * From assign we capture the Core Page assign
     * so we could rebuild the positions with included
     * module Path for widgets and actions
     *
     * @param array postitions
     */
    private function setPositions(array $positions)
    {
        foreach ($positions as &$blocks)
        {
            foreach ($blocks as &$block)
            {
                if ($block['extra_type'] == 'widget') {
                    $block['include_path'] = $this->getPath(
                        $this->modulePath . '/' .
                        $block['extra_module'] . '/Layout/Widgets/' . $block['extra_action'] . '.twig'
                    );
                }
                elseif ($block['extra_type'] == 'block') {
                    $block['extra_action'] = ($block['extra_action']) ?: 'Index';
                    $block['include_path'] = $this->getPath(
                        $this->modulePath . '/' .
                        $block['extra_module'] . '/Layout/Templates/' . $block['extra_action'] . '.twig'
                    );
                }
            }
        }
        $this->twig->addGlobal('positions', $positions);
    }

    /**
     * Convert a filename extension
     *
     * @param string template
    */
    private function convertExtension($template)
    {
        return str_replace('.tpl', '.twig', $template);
    }

    /**
     * Convert a filename extension
     *
     * @param string template
    */
    public function getPath($template)
    {
        $template = Theme::getPath($this->convertExtension($template));

        if (strpos($template, $this->modulePath) !== false) {
            return str_replace($this->modulePath . '/', '', $template);
        }

        // remove theme Path
        return str_replace($this->themePath . '/', '', $template);
    }

    /**
     * Assign an entire array with keys & values.
     *
     * @param   array $values               This array with keys and values will be used to search and replace in the template file.
     * @param   string[optional] $prefix    An optional prefix eg. 'lbl' that can be used.
     * @param   string[optional] $suffix    An optional suffix eg. 'msg' that can be used.
     */
    public function assignArray(array $variables, $index = null)
    {
        // artifacts?
        if (!empty($index) && isset($variables['Core'])) {
            unset($variables['Core']);
            $tmp[$index] = $variables;
            $variables = $tmp;
        }

        // store the variables
        $this->variables = array_merge($this->variables, $variables);
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
    public function getContent($template, $customHeaders = false, $parseCustom = false)
    {
        // bounce back trick because Pages calls getContent Method
        // 2 times on every action
        if (!$template || in_array($template, $this->templates)) {
            return;
        }
        $this->templates[] = $template;

        // only baseFile can render
        $template = $this->getPath($template);
        if ($this->baseFile === $template) {
            $this->setPositions($this->positions, $this->templates);
            $this->render($this->baseFile);
        }
    }

    /**
     * Renders the Page
     *
     * @param  string $template path to render
     */
    private function render($template)
    {
        if (!empty($this->forms)) {
            foreach ($this->forms as $form)
            {
                // using assign to pass the form as global
                $this->twig->addGlobal('form_' . $form->getName(), $form);
            }
            new FormExtension($this->twig);
        }

        // start the filters / globals
        $this->twigFilters();
        $this->twigFrontend();
        $this->startGlobals();
        $this->parseLabels();

        // template
        $this->template = $this->twig->loadTemplate($template);
        echo $this->template->render($this->variables);
    }

    /**
     * Adds a form to this template.
     *
     * @param   SpoonForm $form     The form-instance to add.
     */
    public function addForm($form)
    {
        $this->forms[$form->getName()] = $form;
    }

    /**
     * Setup Frontend for the Twig environment.
     */
    private function twigFrontend()
    {
        // locale object
        $this->twig->addGlobal('lng', new FL());

        // settings
        $this->twig->addGlobal(
            'SITE_TITLE',
            Model::getModuleSetting('Core', 'site_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE)
        );

        // facebook stuff
        if (Model::getModuleSetting('Core', 'facebook_admin_ids', null) !== null) {
            $this->twig->addGlobal(
                'FACEBOOK_ADMIN_IDS',
                Model::getModuleSetting('Core', 'facebook_admin_ids', null)
            );
        }
        if (Model::getModuleSetting('Core', 'facebook_app_id', null) !== null) {
            $this->twig->addGlobal(
                'FACEBOOK_APP_ID',
                Model::getModuleSetting('Core', 'facebook_app_id', null)
            );
        }
        if (Model::getModuleSetting('Core', 'facebook_app_secret', null) !== null) {
            $this->twig->addGlobal(
                'FACEBOOK_APP_SECRET',
                Model::getModuleSetting('Core', 'facebook_app_secret', null)
            );
        }

        // twitter stuff
        if (Model::getModuleSetting('Core', 'twitter_site_name', null) !== null) {
            // strip @ from twitter username
            $this->twig->addGlobal(
                'TWITTER_SITE_NAME',
                ltrim(Model::getModuleSetting('Core', 'twitter_site_name', null), '@')
            );
        }

        /** Filters list converted to twig filters
         - ucfirst -> capitalize
         -
        */

        // Frontend Specific
        $this->twig->addFilter(new \Twig_SimpleFilter('geturlforblock', 'Frontend\Core\Engine\TemplateModifiers::getURLForBlock'));
        $this->twig->addFilter(new \Twig_SimpleFilter('geturlforextraid', 'Frontend\Core\Engine\TemplateModifiers::getURLForExtraId'));
        $this->twig->addFilter(new \Twig_SimpleFilter('getpageinfo', 'Frontend\Core\Engine\TemplateModifiers::getPageInfo'));
        $this->twig->addFilter(new \Twig_SimpleFilter('getsubnavigation', 'Frontend\Core\Engine\TemplateModifiers::getSubNavigation'));
        $this->twig->addFilter(new \Twig_SimpleFilter('parsewidget', 'Frontend\Core\Engine\TemplateModifiers::parseWidget'));
        $this->twig->addFilter(new \Twig_SimpleFilter('highlight', 'Frontend\Core\Engine\TemplateModifiers::highlightCode'));
        $this->twig->addFilter(new \Twig_SimpleFilter('urlencode', 'urlencode'));
        $this->twig->addFilter(new \Twig_SimpleFilter('profilesetting', 'Frontend\Core\Engine\TemplateModifiers::profileSetting'));
        $this->twig->addFilter(new \Twig_SimpleFilter('striptags', 'strip_tags'));
        $this->twig->addFilter(new \Twig_SimpleFilter('formatcurrency', 'Frontend\Core\Engine\TemplateModifiers::formatCurrency'));
        $this->twig->addFilter(new \Twig_SimpleFilter('usersetting', 'Frontend\Core\Engine\TemplateModifiers::userSetting'));
    }

    /**
     * Retrieves the already assigned variables.
     *
     * @return array
     */
    public function getAssignedVariables()
    {
        return $this->variables;
    }

    /**
     * Setup Global filters for the Twig environment.
     */
    private function twigFilters()
    {
        $this->twig->addFilter(new \Twig_SimpleFilter('addslashes', 'addslashes'));
        $this->twig->addFilter(new \Twig_SimpleFilter('geturl', 'Frontend\Core\Engine\TemplateModifiers::getURL'));
        $this->twig->addFilter(new \Twig_SimpleFilter('getnavigation', 'Frontend\Core\Engine\TemplateModifiers::getNavigation'));
        $this->twig->addFilter(new \Twig_SimpleFilter('getmainnavigation', 'Frontend\Core\Engine\TemplateModifiers::getMainNavigation'));
        //$this->twig->addFilter(new \Twig_SimpleFilter('rand', 'Frontend\Core\Engine\TemplateModifiers::random'));
        //$this->twig->addFilter(new \Twig_SimpleFilter('formatfloat', 'Frontend\Core\Engine\TemplateModifiers::formatFloat'));
        $this->twig->addFilter(new \Twig_SimpleFilter('truncate', 'Frontend\Core\Engine\TemplateModifiers::truncate'));
        //$this->twig->addFilter(new \Twig_SimpleFilter('camelcase', '\SpoonFilter::toCamelCase'));
        //$this->twig->addFilter(new \Twig_SimpleFilter('stripnewlines', 'Frontend\Core\Engine\TemplateModifiers::stripNewlines'));
        $this->twig->addFilter(new \Twig_SimpleFilter('formatdate', 'Frontend\Core\Engine\TemplateModifiers::formatDate'));
        //$this->twig->addFilter(new \Twig_SimpleFilter('formattime', 'Frontend\Core\Engine\TemplateModifiers::formatTime'));
        $this->twig->addFilter(new \Twig_SimpleFilter('formatdatetime', 'Frontend\Core\Engine\TemplateModifiers::formatDateTime'));
        //$this->twig->addFilter(new \Twig_SimpleFilter('formatnumber', 'Frontend\Core\Engine\TemplateModifiers::formatNumber'));
        $this->twig->addFilter(new \Twig_SimpleFilter('tolabel', 'Frontend\Core\Engine\TemplateModifiers::toLabel'));
    }


    private function startGlobals()
    {
        // some old globals
        $this->twig->addGlobal('var', '');
        $this->twig->addGlobal('timestamp', time());
        $this->twig->addGlobal('CRLF', "\n");
        $this->twig->addGlobal('TAB', "\t");
        $this->twig->addGlobal('now', time());
        $this->twig->addGlobal('LANGUAGE', FRONTEND_LANGUAGE);
        $this->twig->addGlobal('is' . strtoupper(FRONTEND_LANGUAGE), true);
        $this->twig->addGlobal('debug', $this->debugMode);

        // // old theme checker
        // if (Model::getModuleSetting('Core', 'theme') !== null) {
        //     $this->twig->addGlobal('THEME', Model::getModuleSetting('Core', 'theme', 'default'));
        //     $this->twig->addGlobal(
        //         'THEME_URL',
        //         '/src/Frontend/Themes/' . Model::getModuleSetting('Core', 'theme', 'default')
        //     );
        // }

        // Refactor out constants #1106

        // constants that should be protected from usage in the template
        // $notPublicConstants = array('DB_TYPE', 'DB_DATABASE', 'DB_HOSTNAME', 'DB_USERNAME', 'DB_PASSWORD');

        // // get all defined constants
        // $constants = get_defined_constants(true);

        // // init var
        // $realConstants = array();

        // // remove protected constants aka constants that should not be used in the template
        // foreach ($constants['user'] as $key => $value) {
        //     if (!in_array($key, $notPublicConstants)) {
        //         $realConstants[$key] = $value;
        //     }
        // }

        // // we should only assign constants if there are constants to assign
        // if (!empty($realConstants)) {
        //     $this->assignArray($realConstants);
        // }
    }

    /**
     * Assign the labels
     */
    private function parseLabels()
    {
        $actions = Language::getActions();
        $errors = Language::getErrors();
        $labels = Language::getLabels();
        $messages = Language::getMessages();

        // execute addslashes on the values for the locale, will be used in JS
        if ($this->addSlashes) {
            foreach ($actions as &$value) {
                if (!is_array($value)) {
                    $value = addslashes($value);
                }
            }
            foreach ($errors as &$value) {
                if (!is_array($value)) {
                    $value = addslashes($value);
                }
            }
            foreach ($labels as &$value) {
                if (!is_array($value)) {
                    $value = addslashes($value);
                }
            }
            foreach ($messages as &$value) {
                if (!is_array($value)) {
                    $value = addslashes($value);
                }
            }
        }

        // assign actions errors labels messages
        $this->twig->addGlobal('lbl', $labels);
        $this->twig->addGlobal('err', $errors);
        $this->twig->addGlobal('msg', $messages);
        $this->twig->addGlobal('act', $actions);

        // $this->assignArray($actions, 'act');
        // $this->assignArray($errors, 'err');
        // $this->assignArray($labels, 'lbl');
        // $this->assignArray($messages, 'msg');
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

    /* BC placeholders */
    public function setPlugin(){}
    public function setForceCompile(){}
}
