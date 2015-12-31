<?php

namespace Backend\Core\Engine;

use Backend\Core\Engine\Language as BL;
use Frontend\Core\Engine\FormExtension;

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
class TwigTemplate
{
    /** the extension TwigTemplate uses */
    const EXTENSION = '.html.twig';

    /**
     * Language.
     *
     * @var string
     */
    private $language;

    /**
     * Should we add slashes to each value?
     *
     * @var bool
     */
    private $addSlashes = false;

    /**
     * Debug mode.
     *
     * @var bool
     */
    private $debugMode = false;

    /**
     * List of form objects.
     *
     * @var array
     */
    private $forms = array();

    /**
     * List of assigned variables.
     *
     * @var array
     */
    private $variables = array();
    /**
     * Base file location.
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
     * Returns the template type.
     *
     * @return string Returns the template type
     */
    public function getTemplateType()
    {
        return 'twig';
    }

    /**
     * Spoon assign method.
     *
     * @param string $key
     * @param mixed  $values
     */
    public function assign($key, $values = null)
    {
        // page hook, last call
        if ($key === 'page') {
            $this->baseFile = $this->convertExtension($values['template_path']);
            $this->baseSpoonFile = BACKEND_PATH.'/'.$values['template_path'];
        }

        // in all other cases
        $this->variables[$key] = $values;
    }

    /**
     * Convert a filename extension.
     *
     * @param string template
     */
    private function convertExtension($template)
    {
        return str_replace('.tpl', self::EXTENSION, $template);
    }

    /**
     * Assign an entire array with keys & values.
     *
     * @param array            $values This array with keys and values will be used to search and replace in the template file.
     * @param string[optional] $prefix An optional prefix eg. 'lbl' that can be used.
     * @param string[optional] $suffix An optional suffix eg. 'msg' that can be used.
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
        foreach ($variables as $key => $val) {
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
        // turn on output buffering
        ob_start();

        // render the compiled File
        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem(array(
            BACKEND_MODULES_PATH,
        ));

        $twig = new \Twig_Environment($loader, array(
            'cache' => BACKEND_CACHE_PATH.'/CachedTemplates/Twig_'.($this->debugMode ? 'dev/' : 'prod/'),
            'debug' => $this->debugMode,
        ));

        // debug options
        if ($this->debugMode === true) {
            $twig->addExtension(new \Twig_Extension_Debug());
        }

        if (!empty($this->forms)) {
            foreach ($this->forms as $form) {
                // using assign to pass the form as global
                $twig->addGlobal('form_'.$form->getName(), $form);
            }
            new FormExtension($twig);
        }

        // start the filters / globals
        $this->twigFilters($twig);
        $this->startGlobals($twig);

        // template
        $template = $twig->loadTemplate($this->convertExtension($template));
        echo $template->render($this->variables);

        // return template content
        return ob_get_clean();
    }

    /**
     * Adds a form to the template.
     *
     * @param SpoonForm $form The form-instance to add.
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
        /* Filters list converted to twig filters
         - ucfirst -> capitalize
         -
        */
        $twig->addFilter(new \Twig_SimpleFilter('geturlforblock', 'Backend\Core\Engine\TemplateModifiers::getURLForBlock'));
        $twig->addFilter(new \Twig_SimpleFilter('geturlforextraid', 'Backend\Core\Engine\TemplateModifiers::getURLForExtraId'));
        $twig->addFilter(new \Twig_SimpleFilter('getpageinfo', 'Backend\Core\Engine\TemplateModifiers::getPageInfo'));
        $twig->addFilter(new \Twig_SimpleFilter('getsubnavigation', 'Backend\Core\Engine\TemplateModifiers::getSubNavigation'));
        $twig->addFilter(new \Twig_SimpleFilter('parsewidget', 'Backend\Core\Engine\TemplateModifiers::parseWidget'));
        $twig->addFilter(new \Twig_SimpleFilter('highlight', 'Backend\Core\Engine\TemplateModifiers::highlightCode'));
        $twig->addFilter(new \Twig_SimpleFilter('urlencode', 'urlencode'));
        $twig->addFilter(new \Twig_SimpleFilter('profilesetting', 'Backend\Core\Engine\TemplateModifiers::profileSetting'));
        $twig->addFilter(new \Twig_SimpleFilter('striptags', 'strip_tags'));
        $twig->addFilter(new \Twig_SimpleFilter('formatcurrency', 'Backend\Core\Engine\TemplateModifiers::formatCurrency'));
        $twig->addFilter(new \Twig_SimpleFilter('usersetting', 'Backend\Core\Engine\TemplateModifiers::userSetting'));
        $twig->addFilter(new \Twig_SimpleFilter('uppercase', 'Backend\Core\Engine\TwigTemplate::uppercase'));
        $twig->addFilter(new \Twig_SimpleFilter('trans', 'Backend\Core\Engine\TwigTemplate::trans'));
        $twig->addFilter(new \Twig_SimpleFilter('sprintf', 'sprintf'));
        $twig->addFilter(new \Twig_SimpleFilter('spoon_date', 'Backend\Core\Engine\TwigTemplate::spoonDate'));
        $twig->addFilter(new \Twig_SimpleFilter('addslashes', 'addslashes'));
        $twig->addFilter(new \Twig_SimpleFilter('geturl', 'Backend\Core\Engine\TemplateModifiers::getURL'));
        $twig->addFilter(new \Twig_SimpleFilter('getnavigation', 'Backend\Core\Engine\TemplateModifiers::getNavigation'));
        $twig->addFilter(new \Twig_SimpleFilter('getmainnavigation', 'Backend\Core\Engine\TemplateModifiers::getMainNavigation'));
        //$twig->addFilter(new \Twig_SimpleFilter('rand', 'Backend\Core\Engine\TemplateModifiers::random'));
        //$twig->addFilter(new \Twig_SimpleFilter('formatfloat', 'Backend\Core\Engine\TemplateModifiers::formatFloat'));
        $twig->addFilter(new \Twig_SimpleFilter('truncate', 'Backend\Core\Engine\TemplateModifiers::truncate'));
        //$twig->addFilter(new \Twig_SimpleFilter('camelcase', '\SpoonFilter::toCamelCase'));
        //$twig->addFilter(new \Twig_SimpleFilter('stripnewlines', 'Backend\Core\Engine\TemplateModifiers::stripNewlines'));
        $twig->addFilter(new \Twig_SimpleFilter('formatdate', 'Backend\Core\Engine\TemplateModifiers::formatDate'));
        //$twig->addFilter(new \Twig_SimpleFilter('formattime', 'Backend\Core\Engine\TemplateModifiers::formatTime'));
        $twig->addFilter(new \Twig_SimpleFilter('formatdatetime', 'Backend\Core\Engine\TemplateModifiers::formatDateTime'));
        //$twig->addFilter(new \Twig_SimpleFilter('formatnumber', 'Backend\Core\Engine\TemplateModifiers::formatNumber'));
        $twig->addFilter(new \Twig_SimpleFilter('tolabel', 'Backend\Core\Engine\TemplateModifiers::toLabel'));
        $twig->addFilter(new \Twig_SimpleFilter('timeago', 'Backend\Core\Engine\TemplateModifiers::timeAgo'));
        $twig->addFilter(new \Twig_SimpleFilter('cleanupplaintext', 'Backend\Core\Engine\TemplateModifiers::cleanupPlainText'));
    }

    /**
     * Transform the string to uppercase.
     *
     * @return string The string, completly uppercased.
     *
     * @param string $string The string that you want to apply this method on.
     */
    public static function uppercase($string)
    {
        return mb_convert_case($string, MB_CASE_UPPER, \Spoon::getCharset());
    }

    /**
     * Translate a string.
     *
     * @return string The string, to translate.
     *
     * @param string $string The string that you want to apply this method on.
     */
    public static function trans($string)
    {
        if (strpos($string, '.') === false) {
            throw new Exception('Error Processing Request : '.$string);
        }
        list($action, $string) = explode('.', $string);

        return BL::$action($string);
    }

    /**
     * Formats a language specific date.
     *
     * @return string The formatted date according to the timestamp, format and provided language.
     *
     * @param mixed            $timestamp The timestamp or date that you want to apply the format to.
     * @param string[optional] $format    The optional format that you want to apply on the provided timestamp.
     * @param string[optional] $language  The optional language that you want this format in (Check SpoonLocale for the possible languages).
     */
    public static function spoonDate($timestamp, $format = 'Y-m-d H:i:s', $language = 'en')
    {
        if (is_string($timestamp) && !is_numeric($timestamp)) {
            // use strptime if you want to restrict the input format
            $timestamp = strtotime($timestamp);
        }

        return \SpoonDate::getDate($format, $timestamp, $language);
    }

    /** @todo Refactor out constants #1106
     * We need to deprecate this asap
     */
    private function startGlobals(&$twig)
    {
        // some old globals
        $twig->addGlobal('var', '');
        $twig->addGlobal('CRLF', "\n");
        $twig->addGlobal('TAB', "\t");
        $twig->addGlobal('now', time());
        $twig->addGlobal('LANGUAGE', $this->language);
        $twig->addGlobal('is'.strtoupper($this->language), true);
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
                '/src/Backend/Themes/'.$this->forkSettings->get('Core', 'theme', 'default')
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

        /* Setup Backend for the Twig environment. */

       // settings
        $twig->addGlobal(
            'SITE_TITLE',
            $this->forkSettings->get('Core', 'site_title_'.$this->language, SITE_DEFAULT_TITLE)
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
    public function setPlugin()
    {
    }
    public function setForceCompile()
    {
    }
    public function cache()
    {
    }
    public function isCached()
    {
    }
    public function compile()
    {
    }
    public function display()
    {
    }
}
