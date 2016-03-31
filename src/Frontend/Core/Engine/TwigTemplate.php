<?php

namespace Frontend\Core\Engine;

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
    /** the extension TwigTemplate uses */
    const EXTENSION = '.html.twig';

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
     * List of passed widgets
     *
     * @var array
     */
    private $widgets = array();

    /**
     * main action block
     *
     * @var array
     */
    private $block = '';

    /**
     * theme path location
     *
     * @var string
     */
    private $themePath;

    /**
     * Base file location
     *
     * @var string
     */
    private $baseFile;

    /**
     * @var Fork settings
     */
    private $forkSettings;

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

        $this->forkSettings = Model::get('fork.settings');
        $this->themePath = FRONTEND_PATH . '/Themes/' . $this->forkSettings->get('Core', 'theme', 'default');
        $this->debugMode = Model::getContainer()->getParameter('kernel.debug');
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
        // page hook, last call
        if ($key === 'page') {
            $this->baseFile = $this->convertExtension($values['template_path']);
            $this->baseSpoonFile = $values['template_path'];
            $this->positions = $values['positions'];
        }

        // in all other cases
        $this->variables[$key] = $values;
    }

    /**
     * From assign we capture the Core Page assign
     * so we could rebuild the positions with included
     * module Path for widgets and actions
     *
     * I must admit this is ugly
     *
     * @param array positions
     */
    private function setPositions(array $positions)
    {
        foreach ($positions as &$blocks)
        {
            foreach ($blocks as &$block)
            {
                // skip html
                if (!empty($block['html'])) continue;

                $block['extra_data'] = @unserialize($block['extra_data']);

                // legacy search the correct module path
                if ($block['extra_type'] === 'widget' && $block['extra_action']) {

                    if (isset($block['extra_data']['template'])) {
                        $tpl = substr($block['extra_data']['template'], 0, -4);
                        $block['include_path'] = $this->widgets[$tpl];
                    } else {
                        $block['include_path'] = $this->getPath(
                            $block['extra_module'] . '/Layout/Widgets/' . $block['extra_action'] . '.tpl'
                        );
                    }

                // main action block
                } else {
                    $block['include_path'] = $this->block;
                }
            }
        }
        return $positions;
    }

    /**
     * Convert a filename extension
     *
     * @param string template
    */
    private function convertExtension($template)
    {
        return str_replace('.tpl', self::EXTENSION, $template);
    }

    /**
     * Convert a filename extension
     *
     * @param string template
    */
    public function getPath($template)
    {
        $template = $this->convertExtension($template);
        if (strpos($template, FRONTEND_MODULES_PATH) !== false) {
            return str_replace(FRONTEND_MODULES_PATH . '/', '', $template);
        }
        // else it's in the theme folder
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

        // merge the variables array_merge might be to slow for bigger sites
        // as array_merge tend to slow down at +100 keys
        foreach($variables as $key => $val) {
            $this->variables[$key] = $val;
        }
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
        // bounce back trick because Pages calls getContent Method
        // 2 times on every action
        if (!$template || in_array($template, $this->templates)) {
            return;
        }
        $path = pathinfo($template);
        $this->templates[] = $template;

        // collect the Widgets and Actions, we need them later
        if (strpos($path['dirname'], 'Widgets') !== false) {
            $this->widgets[$path['filename']] = $this->getPath($template);
        } elseif (strpos($path['dirname'], 'Core/Layout/Templates') === false) {
            $this->block = $this->getPath($template);
        }

        // only baseFile can start the render
        if ($this->baseSpoonFile === $template) {

            // turn on output buffering
            ob_start();

            // render the compiled File
            echo $this->render();

            // return template content
            return ob_get_clean();
        }
    }

    /**
     * Renders the Page
     *
     * @param  string $template path to render
     */
    public function render($template = null)
    {
        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem(array(
            $this->themePath,
            $this->themePath.'/Modules',
            FRONTEND_MODULES_PATH,
            FRONTEND_PATH,
            '/'
        ));

        $twig = new \Twig_Environment($loader, array(
            'cache' => FRONTEND_CACHE_PATH . '/CachedTemplates/Twig_' . ($this->debugMode ? 'dev/': 'prod/'),
            'debug' => $this->debugMode
        ));

        // debug options
        if ($this->debugMode === true) {
            $twig->addExtension(new \Twig_Extension_Debug());
        }

        if (!empty($this->forms)) {
            foreach ($this->forms as $form) {
                // using assign to pass the form as global
                $twig->addGlobal('form_' . $form->getName(), $form);
            }
            new FormExtension($twig);
        }

        // start the filters / globals
        $this->twigFilters($twig);
        $this->startGlobals($twig);

        // set the positions array
        if (!empty($this->positions)) {
            $twig->addGlobal('positions', $this->setPositions($this->positions));
        }

        // template
        if ($template === null) {
            $template = $twig->loadTemplate($this->baseFile);
        } else {
            $template = $twig->loadTemplate($template);
        }

        return $template->render($this->variables);
    }

    /**
     * Adds a form to the template.
     *
     * @param   SpoonForm $form     The form-instance to add.
     */
    public function addForm($form)
    {
        $this->forms[$form->getName()] = $form;
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
    private function twigFilters(&$twig)
    {
        /** Filters list converted to twig filters
         - ucfirst -> capitalize
         -
        */
        $twig->addFilter(new \Twig_SimpleFilter('geturlforblock', 'Frontend\Core\Engine\TemplateModifiers::getURLForBlock'));
        $twig->addFilter(new \Twig_SimpleFilter('geturlforextraid', 'Frontend\Core\Engine\TemplateModifiers::getURLForExtraId'));
        $twig->addFilter(new \Twig_SimpleFilter('getpageinfo', 'Frontend\Core\Engine\TemplateModifiers::getPageInfo'));
        $twig->addFilter(new \Twig_SimpleFilter('getsubnavigation', 'Frontend\Core\Engine\TemplateModifiers::getSubNavigation'));
        $twig->addFilter(new \Twig_SimpleFilter('parsewidget', 'Frontend\Core\Engine\TemplateModifiers::parseWidget'));
        $twig->addFilter(new \Twig_SimpleFilter('highlight', 'Frontend\Core\Engine\TemplateModifiers::highlightCode'));
        $twig->addFilter(new \Twig_SimpleFilter('urlencode', 'urlencode'));
        $twig->addFilter(new \Twig_SimpleFilter('profilesetting', 'Frontend\Core\Engine\TemplateModifiers::profileSetting'));
        $twig->addFilter(new \Twig_SimpleFilter('striptags', 'strip_tags'));
        $twig->addFilter(new \Twig_SimpleFilter('formatcurrency', 'Frontend\Core\Engine\TemplateModifiers::formatCurrency'));
        $twig->addFilter(new \Twig_SimpleFilter('usersetting', 'Frontend\Core\Engine\TemplateModifiers::userSetting'));
        $twig->addFilter(new \Twig_SimpleFilter('uppercase', 'Frontend\Core\Engine\TwigTemplate::uppercase'));
        $twig->addFilter(new \Twig_SimpleFilter('trans', 'Frontend\Core\Engine\TwigTemplate::trans'));
        $twig->addFilter(new \Twig_SimpleFilter('sprintf', 'sprintf'));
        $twig->addFilter(new \Twig_SimpleFilter('spoon_date', 'Frontend\Core\Engine\TwigTemplate::spoonDate'));
        $twig->addFilter(new \Twig_SimpleFilter('addslashes', 'addslashes'));
        $twig->addFilter(new \Twig_SimpleFilter('geturl', 'Frontend\Core\Engine\TemplateModifiers::getURL'));
        $twig->addFilter(new \Twig_SimpleFilter('getnavigation', 'Frontend\Core\Engine\TemplateModifiers::getNavigation'));
        $twig->addFilter(new \Twig_SimpleFilter('getmainnavigation', 'Frontend\Core\Engine\TemplateModifiers::getMainNavigation'));
        //$twig->addFilter(new \Twig_SimpleFilter('rand', 'Frontend\Core\Engine\TemplateModifiers::random'));
        //$twig->addFilter(new \Twig_SimpleFilter('formatfloat', 'Frontend\Core\Engine\TemplateModifiers::formatFloat'));
        $twig->addFilter(new \Twig_SimpleFilter('truncate', 'Frontend\Core\Engine\TemplateModifiers::truncate'));
        //$twig->addFilter(new \Twig_SimpleFilter('camelcase', '\SpoonFilter::toCamelCase'));
        //$twig->addFilter(new \Twig_SimpleFilter('stripnewlines', 'Frontend\Core\Engine\TemplateModifiers::stripNewlines'));
        $twig->addFilter(new \Twig_SimpleFilter('formatdate', 'Frontend\Core\Engine\TemplateModifiers::formatDate'));
        //$twig->addFilter(new \Twig_SimpleFilter('formattime', 'Frontend\Core\Engine\TemplateModifiers::formatTime'));
        $twig->addFilter(new \Twig_SimpleFilter('formatdatetime', 'Frontend\Core\Engine\TemplateModifiers::formatDateTime'));
        //$twig->addFilter(new \Twig_SimpleFilter('formatnumber', 'Frontend\Core\Engine\TemplateModifiers::formatNumber'));
        $twig->addFilter(new \Twig_SimpleFilter('tolabel', 'Frontend\Core\Engine\TemplateModifiers::toLabel'));
        $twig->addFilter(new \Twig_SimpleFilter('timeago', 'Frontend\Core\Engine\TemplateModifiers::timeAgo'));
        $twig->addFilter(new \Twig_SimpleFilter('cleanupplaintext', 'Frontend\Core\Engine\TemplateModifiers::cleanupPlainText'));
    }

    /**
     * Transform the string to uppercase.
     *
     * @return  string          The string, completly uppercased.
     * @param   string $string  The string that you want to apply this method on.
     */
    public static function uppercase($string)
    {
        return mb_convert_case($string, MB_CASE_UPPER, \Spoon::getCharset());
    }

    /**
     * Translate a string.
     *
     * @return  string          The string, to translate.
     * @param   string $string  The string that you want to apply this method on.
     */
    public static function trans($string)
    {
        list($action, $string) = explode('.', $string);
        return FL::$action($string);
    }

    /**
     * Formats a language specific date.
     *
     * @return  string                      The formatted date according to the timestamp, format and provided language.
     * @param   mixed $timestamp            The timestamp or date that you want to apply the format to.
     * @param   string[optional] $format    The optional format that you want to apply on the provided timestamp.
     * @param   string[optional] $language  The optional language that you want this format in (Check SpoonLocale for the possible languages).
     */
    public static function spoonDate($timestamp, $format = 'Y-m-d H:i:s', $language = 'en')
    {
        if(is_string($timestamp) && !is_numeric($timestamp)) {
            // use strptime if you want to restrict the input format
            $timestamp = strtotime($timestamp);
        }
        return \SpoonDate::getDate($format, $timestamp, $language);
    }

    /** @todo Refactor out constants #1106
     *
     * We need to deprecate this asap
    */
    private function startGlobals(&$twig)
    {
        // some old globals
        $twig->addGlobal('var', '');
        $twig->addGlobal('CRLF', "\n");
        $twig->addGlobal('TAB', "\t");
        $twig->addGlobal('now', time());
        $twig->addGlobal('LANGUAGE', FRONTEND_LANGUAGE);
        $twig->addGlobal('is' . strtoupper(FRONTEND_LANGUAGE), true);
        $twig->addGlobal('debug', $this->debugMode);

        $twig->addGlobal('timestamp', time());
        $twig->addGlobal('timeFormat', $this->forkSettings->get('Core', 'time_format'));
        $twig->addGlobal('dateFormatShort', $this->forkSettings->get('Core', 'date_format_short'));
        $twig->addGlobal('dateFormatLong', $this->forkSettings->get('Core', 'date_format_long'));

        // old theme checker
        if ($this->forkSettings->get('Core', 'theme') !== null) {
            $twig->addGlobal('THEME', $this->forkSettings->get('Core', 'theme', 'default'));
            $twig->addGlobal(
                'THEME_URL',
                '/src/Frontend/Themes/' . $this->forkSettings->get('Core', 'theme', 'default')
            );
        }

        // constants that should be protected from usage in the template
        $notPublicConstants = array('DB_TYPE', 'DB_DATABASE', 'DB_HOSTNAME', 'DB_USERNAME', 'DB_PASSWORD');

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
            $this->assignArray($realConstants);
        }

        /* Setup Frontend for the Twig environment. */

       // settings
        $twig->addGlobal(
            'SITE_TITLE',
            $this->forkSettings->get('Core', 'site_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE)
        );

        // facebook stuff
        if ($this->forkSettings->get('Core', 'facebook_admin_ids', null) !== null) {
            $twig->addGlobal(
                'FACEBOOK_ADMIN_IDS',
                $this->forkSettings->get('Core', 'facebook_admin_ids', null)
            );
        }
        if ($this->forkSettings->get('Core', 'facebook_app_id', null) !== null) {
            $twig->addGlobal(
                'FACEBOOK_APP_ID',
                $this->forkSettings->get('Core', 'facebook_app_id', null)
            );
        }
        if ($this->forkSettings->get('Core', 'facebook_app_secret', null) !== null) {
            $twig->addGlobal(
                'FACEBOOK_APP_SECRET',
                $this->forkSettings->get('Core', 'facebook_app_secret', null)
            );
        }

        // twitter stuff
        if ($this->forkSettings->get('Core', 'twitter_site_name', null) !== null) {
            // strip @ from twitter username
            $twig->addGlobal(
                'TWITTER_SITE_NAME',
                ltrim($this->forkSettings->get('Core', 'twitter_site_name', null), '@')
            );
        }
    }

    /**
     * Should we execute addSlashed on the locale?
     *
     * @param bool $on Enable addslashes.
     */
    public function setAddSlashes($enabled = true)
    {
        $this->addSlashes = (bool) $enabled;
    }

    /* BC placeholders */
    public function setPlugin(){}
    public function setForceCompile(){}
    public function cache(){}
    public function isCached(){}
    public function compile(){}
    public function display(){}
}
