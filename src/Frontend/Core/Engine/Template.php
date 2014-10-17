<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is our extended version of SpoonTemplate
 * This class will handle a lot of stuff for you, for example:
 *    - it will assign all labels
 *    - it will map some modifiers
 *    - it will assign a lot of constants
 *    - ...
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter@dieterve.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Frederik Heyninck <frederik@figure8.be>
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
     * The constructor will store the instance in the reference, preset some settings and map the custom modifiers.
     *
     * @param bool $addToReference Should the instance be added into the reference.
     */
    public function __construct($addToReference = true)
    {
        parent::__construct();

        if ($addToReference) {
            Model::getContainer()->set('template', $this);
        }

        $this->setCacheDirectory(FRONTEND_CACHE_PATH . '/CachedTemplates');
        $this->setCompileDirectory(FRONTEND_CACHE_PATH . '/CompiledTemplates');
        $this->setForceCompile(SPOON_DEBUG);
        $this->mapCustomModifiers();
    }

    /**
     * Compile a given template.
     *
     * @param string  $path     The path to the template, excluding the template filename.
     * @param string $template The filename of the template within the path.
     * @return bool
     */
    public function compile($path, $template)
    {
        // redefine template
        if (realpath($template) === false) {
            $template = $path . '/' . $template;
        }

        // source file does not exist
        if (!is_file($template)) {
            return false;
        }

        // create object
        $compiler = new TemplateCompiler($template, $this->variables);

        // set some options
        $compiler->setCacheDirectory($this->cacheDirectory);
        $compiler->setCompileDirectory($this->compileDirectory);
        $compiler->setForceCompile($this->forceCompile);
        $compiler->setForms($this->forms);

        // compile & save
        $compiler->parseToFile();

        // status
        return true;
    }

    /**
     * Output the template into the browser
     * Will also assign the labels and all user-defined constants.
     * If you want custom-headers, you should set them yourself, otherwise the content-type and charset will be set
     *
     * @param string $template      The path of the template to use.
     * @param bool   $customHeaders Deprecated variable.
     * @param bool   $parseCustom   Parse custom template.
     */
    public function display($template, $customHeaders = false, $parseCustom = false)
    {
        // do custom stuff
        if ($parseCustom) {
            new TemplateCustom($this);
        }

        // parse constants
        $this->parseConstants();

        // check debug
        $this->parseDebug();

        // parse the label
        $this->parseLabels();

        // parse date/time formats
        $this->parseDateTimeFormats();

        // parse vars
        $this->parseVars();

        // get template path
        $template = Theme::getPath($template);

        /*
         * Code below is exactly the same as from our parent (SpoonTemplate::display), except
         * for the compiler being used. We want our own compiler extension here.
         */

        // redefine
        $template = (string) $template;

        // validate name
        if (trim($template) == '' || !is_file($template)) {
            throw new \SpoonTemplateException('Please provide an existing template.');
        }

        // compiled name
        $compileName = $this->getCompileName((string) $template);

        // compiled if needed
        if ($this->forceCompile || !is_file($this->compileDirectory . '/' . $compileName)) {
            // create compiler
            $compiler = new TemplateCompiler((string) $template, $this->variables);

            // set some options
            $compiler->setCacheDirectory($this->cacheDirectory);
            $compiler->setCompileDirectory($this->compileDirectory);
            $compiler->setForceCompile($this->forceCompile);
            $compiler->setForms($this->forms);

            // compile & save
            $compiler->parseToFile();
        }

        // load template
        require $this->compileDirectory . '/' . $compileName;
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
     * Fetch the parsed content from this template.
     *
     * @param string $template      The location of the template file, used to display this template.
     * @param bool   $customHeaders Are custom headers already set?
     * @param bool   $parseCustom   Parse custom template.
     * @return string The actual parsed content after executing this template.
     */
    public function getContent($template, $customHeaders = false, $parseCustom = false)
    {
        ob_start();
        $this->display($template, $customHeaders, $parseCustom);

        return ob_get_clean();
    }

    /**
     * Is the cache for this item still valid.
     *
     * @param string $name The name of the cached block.
     * @return bool
     */
    public function isCached($name)
    {
        // never cached in debug
        if (SPOON_DEBUG) {
            return false;
        } else {
            // let parent do the actual check
            return parent::isCached($name);
        }
    }

    /**
     * Map the frontend-specific modifiers
     */
    private function mapCustomModifiers()
    {
        // fetch the path for an include (theme file if available, core file otherwise)
        $this->mapModifier('getpath', array('Frontend\Core\Engine\TemplateModifiers', 'getPath'));

        // formatting
        $this->mapModifier('formatcurrency', array('Frontend\Core\Engine\TemplateModifiers', 'formatCurrency'));

        // URL for a specific pageId
        $this->mapModifier('geturl', array('Frontend\Core\Engine\TemplateModifiers', 'getURL'));

        // URL for a specific block/extra
        $this->mapModifier('geturlforblock', array('Frontend\Core\Engine\TemplateModifiers', 'getURLForBlock'));
        $this->mapModifier('geturlforextraid', array('Frontend\Core\Engine\TemplateModifiers', 'getURLForExtraId'));

        // page related
        $this->mapModifier('getpageinfo', array('Frontend\Core\Engine\TemplateModifiers', 'getPageInfo'));

        // convert var into navigation
        $this->mapModifier('getnavigation', array('Frontend\Core\Engine\TemplateModifiers', 'getNavigation'));
        $this->mapModifier('getsubnavigation', array('Frontend\Core\Engine\TemplateModifiers', 'getSubNavigation'));

        // parse a widget
        $this->mapModifier('parsewidget', array('Frontend\Core\Engine\TemplateModifiers', 'parseWidget'));

        // rand
        $this->mapModifier('rand', array('Frontend\Core\Engine\TemplateModifiers', 'random'));

        // string
        $this->mapModifier('formatfloat', array('Frontend\Core\Engine\TemplateModifiers', 'formatFloat'));
        $this->mapModifier('formatnumber', array('Frontend\Core\Engine\TemplateModifiers', 'formatNumber'));
        $this->mapModifier('truncate', array('Frontend\Core\Engine\TemplateModifiers', 'truncate'));
        $this->mapModifier('cleanupplaintext', array('Frontend\Core\Engine\TemplateModifiers', 'cleanupPlainText'));
        $this->mapModifier('camelcase', array('\SpoonFilter', 'toCamelCase'));
        $this->mapModifier('stripnewlines', array('Frontend\Core\Engine\TemplateModifiers', 'stripNewlines'));

        // dates
        $this->mapModifier('timeago', array('Frontend\Core\Engine\TemplateModifiers', 'timeAgo'));

        // users
        $this->mapModifier('usersetting', array('Frontend\Core\Engine\TemplateModifiers', 'userSetting'));

        // highlight
        $this->mapModifier('highlight', array('Frontend\Core\Engine\TemplateModifiers', 'highlightCode'));

        // urlencode
        $this->mapModifier('urlencode', 'urlencode');

        // strip tags
        $this->mapModifier('striptags', 'strip_tags');

        // debug stuff
        $this->mapModifier('dump', array('Frontend\Core\Engine\TemplateModifiers', 'dump'));

        // profiles
        $this->mapModifier('profilesetting', array('Frontend\Core\Engine\TemplateModifiers', 'profileSetting'));
    }

    /**
     * Parse all user-defined constants
     */
    private function parseConstants()
    {
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
            $this->assign($realConstants);
        }

        // aliases
        $this->assign('LANGUAGE', FRONTEND_LANGUAGE);
        $this->assign('is' . strtoupper(FRONTEND_LANGUAGE), true);

        // settings
        $this->assign(
            'SITE_TITLE',
            Model::getModuleSetting('Core', 'site_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE)
        );

        // facebook stuff
        if (Model::getModuleSetting('Core', 'facebook_admin_ids', null) !== null) {
            $this->assign(
                'FACEBOOK_ADMIN_IDS',
                Model::getModuleSetting('Core', 'facebook_admin_ids', null)
            );
        }
        if (Model::getModuleSetting('Core', 'facebook_app_id', null) !== null) {
            $this->assign(
                'FACEBOOK_APP_ID',
                Model::getModuleSetting('Core', 'facebook_app_id', null)
            );
        }
        if (Model::getModuleSetting('Core', 'facebook_app_secret', null) !== null) {
            $this->assign(
                'FACEBOOK_APP_SECRET',
                Model::getModuleSetting('Core', 'facebook_app_secret', null)
            );
        }

        // twitter stuff
        if (Model::getModuleSetting('Core', 'twitter_site_name', null) !== null) {
            // strip @ from twitter username
            $this->assign(
                'TWITTER_SITE_NAME',
                ltrim(Model::getModuleSetting('Core', 'twitter_site_name', null), '@')
            );
        }

        // theme
        if (Model::getModuleSetting('Core', 'theme') !== null) {
            $this->assign('THEME', Model::getModuleSetting('Core', 'theme', 'default'));
            $this->assign(
                'THEME_PATH',
                FRONTEND_PATH . '/Themes/' . Model::getModuleSetting('Core', 'theme', 'default')
            );
            $this->assign(
                'THEME_URL',
                '/src/Frontend/Themes/' . Model::getModuleSetting('Core', 'theme', 'default')
            );
        }
    }

    /**
     * Parses the general date and time formats
     */
    private function parseDateTimeFormats()
    {
        // time format
        $this->assign('timeFormat', Model::getModuleSetting('Core', 'time_format'));

        // date formats (short & long)
        $this->assign('dateFormatShort', Model::getModuleSetting('Core', 'date_format_short'));
        $this->assign('dateFormatLong', Model::getModuleSetting('Core', 'date_format_long'));
    }

    /**
     * Assigns an option if we are in debug-mode
     */
    private function parseDebug()
    {
        if (SPOON_DEBUG) {
            $this->assign('debug', true);
        }
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

        // assign actions
        $this->assignArray($actions, 'act');

        // assign errors
        $this->assignArray($errors, 'err');

        // assign labels
        $this->assignArray($labels, 'lbl');

        // assign messages
        $this->assignArray($messages, 'msg');
    }

    /**
     * Assign some default vars
     */
    private function parseVars()
    {
        // assign a placeholder var
        $this->assign('var', '');

        // assign current timestamp
        $this->assign('timestamp', time());
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
