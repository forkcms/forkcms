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
    private $forms = array();
    private $variables = array();
    private $themePath;
    private $baseFile;
    private $templates = array();

    function __construct($addToReference = true)
    {
        if ($addToReference) {
            Model::getContainer()->set('template', $this);
        }
        require_once PATH_WWW . '/vendor/twig/twig/lib/Twig/Autoloader.php';
        \Twig_Autoloader::register();
        $this->themePath = FRONTEND_PATH . '/Themes/' . Model::getModuleSetting('Core', 'theme', 'default');
    }

    private function startGlobals()
    {
        // some old globals
        $this->variables['var'] = '';
        $this->variables['timestamp'] = time();
        $this->variables['CRLF'] = "\n";
        $this->variables['TAB'] = "\t";
        $this->variables['now'] = time();
        $this->variables['LANGUAGE'] = FRONTEND_LANGUAGE;
        $this->variables['is' . strtoupper(FRONTEND_LANGUAGE)] = true;
        $this->variables['debug'] = SPOON_DEBUG;

        // // old theme checker
        // if (Model::getModuleSetting('Core', 'theme') !== null) {
        //     $this->assign('THEME', Model::getModuleSetting('Core', 'theme', 'default'));
        //     $this->assign(
        //         'THEME_URL',
        //         '/src/Frontend/Themes/' . Model::getModuleSetting('Core', 'theme', 'default')
        //     );
        // }

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

    public function assign($key, $values = null)
    {
        // bad code
        // key == array in this case
        if (is_array($key)) {
            $this->assignArray($key, $values);
            return;
        }

        // form hook
        $field = substr($key, 0, 3);
        if (in_array($field, array('hid', 'chk', 'ddm', 'txt'))) {
            $this->fields[$field][substr($key, 3)] = $values;
            return;
        }

        // page hook
        // end call
        if ($key === 'page') {
            $this->baseFile = str_replace('.tpl', '.twig', $values['template_path']);
            $this->setPositions($values['positions']);
            return;
        }

        // in all other cases
        $this->variables[$key] = $values;
    }

    private function setPositions($positions)
    {
        foreach ($positions as &$blocks)
        {
            foreach ($blocks as &$block)
            {
                if ($block['extra_type'] == 'widget') {
                    $block['include_path'] = $this->convertToTwig(
                        $this->themePath . '/Modules/' .
                        $block['extra_module'] . '/Layout/Widgets/' . $block['extra_action'] . '.twig'
                    );
                }
                elseif ($block['extra_type'] == 'block') {
                    $block['extra_action'] = ($block['extra_action']) ?: 'Index';
                    $block['include_path'] = $this->convertToTwig(
                        $this->themePath . '/Modules/' .
                        $block['extra_module'] . '/Layout/Templates/' . $block['extra_action'] . '.twig'
                    );
                }
            }
        }
        $this->variables['positions'] = $positions;
    }

    public function convertToTwig($template)
    {
        return str_replace(
            $this->themePath . '/', '',
            Theme::getPath(str_replace('.tpl', '.twig', $template))
        );
    }

    public function assignArray(array $variables, $index = null)
    {
        // artifacts?
        if ($index && isset($variables['Core'])) {
            unset($variables['Core']);
            $tmp[$index] = $variables;
            $variables = $tmp;
        }

        // store the variables
        $this->variables = array_merge($this->variables, $variables);
    }

    public function getContent($template, $customHeaders = false, $parseCustom = false)
    {
        // bounce back trick because Pages calls getContent Method
        // 2 times on every action
        if (!$template || in_array($template, $this->templates)) {
            return;
        }
        $this->templates[] = $template;

        // only baseFile can render
        $template = $this->convertToTwig($template);
        if ($this->baseFile === $template) {
            $this->render($template);
        }
    }

    private function render($template)
    {
        $this->startGlobals();

        if ($this->forms) {
            $this->assign('form', $this->forms);
        }

        $loader = new \Twig_Loader_Filesystem($this->themePath);
        $this->twig = new \Twig_Environment($loader, array(
            'cache' => FRONTEND_CACHE_PATH . '/CachedTemplates/Twig',
            'debug' => SPOON_DEBUG
        ));

        // start the filters
        $this->twigFilters();
        $this->twigFrontend();

        // debug options
        if (SPOON_DEBUG) {
            $this->twig->addExtension(new \Twig_Extension_Debug());
        }

        // template
        $this->template = $this->twig->loadTemplate($template);
        echo $this->template->render($this->variables);
    }

    public function addForm($form)
    {
        $compileForm ='<form accept-charset="UTF-8" action="' . $form->getAction() . '" method="'. $form->getMethod().'" '. $form->getParametersHTML() . '>';
        $compileForm .= $form->getField('form')->parse();
        if($form->getUseToken()) {
            $compileForm .= '<input type="hidden" name="form_token" id="' . $form->getField('form_token')->getAttribute('id'). '" value="' . htmlspecialchars($form->getField('form_token')->getValue()). ' " />';
        }
        $this->forms[$form->getName()] = $this->fields;
        $this->forms[$form->getName()]['form'] = $compileForm;
        $this->forms[$form->getName()]['end'] = "</form>";
    }

    public function getAssignedVariables()
    {
        return $this->variables;
    }

    /**
     * Setup Frontend for the Twig environment.
     */
    private function twigFrontend()
    {
        // locale object
        $this->twig->addGlobal('lng', new FL());

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
     * Setup Global filters for the Twig environment.
     */
    private function twigFilters()
    {
        /** Filters list converted to twig filters
         - ucfirst -> capitalize
        **/

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


    // private function twigGlobals()
    // {
    //     $this->twig->addGlobal('CRLF', "\n");
    //     $this->twig->addGlobal('TAB', "\t");
    //     $this->twig->addGlobal('now', time());
    //     $this->twig->addGlobal('LANGUAGE', BL::getWorkingLanguage());
    //     $this->twig->addGlobal('SITE_MULTILANGUAGE', SITE_MULTILANGUAGE);
    //     $this->twig->addGlobal(
    //         'SITE_TITLE',
    //         BackendModel::getModuleSetting(
    //             'core',
    //             'site_title_' . BL::getWorkingLanguage(), SITE_DEFAULT_TITLE
    //         )
    //     );
    //     // TODO goes up, here we assume the current user is authenticated already.
    //     $this->twig->addGlobal('user', BackendAuthentication::getUser());
    //     $languages = BackendLanguage::getWorkingLanguages();
    //     $workingLanguages = array();
    //     foreach($languages as $abbreviation => $label) $workingLanguages[] = array('abbr' => $abbreviation, 'label' => $label, 'selected' => ($abbreviation == BackendLanguage::getWorkingLanguage()));
    //     $this->twig->addGlobal('workingLanguages', $workingLanguages);
    // }

    /**
     * Should we execute addSlashed on the locale?
     *
     * @param bool $on Enable addslashes.
     */
    public function setAddSlashes($on = true)
    {
        $this->addSlashes = (bool) $on;
    }

    public function setPlugin(){}
    public function setForceCompile(){}
}
