<?php

namespace Frontend\Core\Engine;

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

Class Template
{
    /**
     * Should we add slashes to each value?
     *
     * @var bool
     */
    private $addSlashes = false;

    public $forms = array();
    public $variables;

    function __construct($addToReference = true)
    {
        if ($addToReference) {
            Model::getContainer()->set('template', $this);
        }
        require_once PATH_WWW . '/vendor/twig/twig/lib/Twig/Autoloader.php';
        \Twig_Autoloader::register();
        $this->themePath = FRONTEND_PATH . '/Themes/' . Model::getModuleSetting('Core', 'theme', 'default');
    }

    function startGlobals()
    {
        // assign a placeholder var
        $this->assign('var', '');

        // assign current timestamp
        $this->assign('timestamp', time());

        // some old globals
        $this->assign('CRLF', "\n");
        $this->assign('TAB', "\t");
        $this->assign('now', time());

        // old theme checker
        if (Model::getModuleSetting('Core', 'theme') !== null) {
            $this->assign('THEME', Model::getModuleSetting('Core', 'theme', 'default'));
            $this->assign(
                'THEME_URL',
                '/src/Frontend/Themes/' . Model::getModuleSetting('Core', 'theme', 'default')
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
    }

    /**
     * Assign the labels
     */
    function parseLabels()
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

    function assign($key, $values = null)
    {
        // bad code
        // key == array in this case
        if (is_array($key)) {
            $this->assignArray($key, $values);
            return;
        }

        switch (true)
        {
            case ($key == 'page'):
                $this->baseFile = $this->convertToTwig($values['template_path']);
                $this->setPositions($values['positions']);
                break;
            case (substr($key, 3) == 'hid'):
                var_dump($key);exit;
                break;
            case (substr($key, 3) == 'chk'):
                var_dump($key);exit;
                break;
            case (substr($key, 3) == 'ddm'):
                var_dump($key);exit;
                break;
            case (substr($key, 3) == 'txt'):
                var_dump($key);exit;
                break;
            default:
                $this->variables[$key] = $values;
                break;
        }
    }

    function setPositions($positions)
    {
        foreach ($positions as &$blocks)
        {
            foreach ($blocks as &$block)
            {
                if ($block['extra_type'] == 'widget') {
                    $block['include_path'] = 'Modules/' . $block['extra_module'] . '/Layout/Widgets/' . $block['extra_action'] . '.twig';
                    $this->actions[] = $block['include_path'];
                }
                elseif ($block['extra_type'] == 'block') {
                    $block['extra_action'] = ($block['extra_action']) ?: 'Index';
                    $block['include_path'] = 'Modules/' . $block['extra_module'] . '/Layout/Templates/' . $block['extra_action'] . '.twig';
                    $this->actions[] = $block['include_path'];
                }

            }
        }
        $this->variables['positions'] = $positions;
    }

    function convertToTwig($path)
    {
        return str_replace('.tpl', '.twig', $path);
    }

    function assignArray(array $variables, $index = null)
    {
        if ($index)
        {
            unset($variables['Core']);
            $tmp[$index] = $variables;
            $variables = $tmp;
        }
        $this->variables = array_merge($this->variables, $variables);
    }

    function getContent($template, $customHeaders = false, $parseCustom = false)
    {
        $template = Theme::getPath($template);
        $template = str_replace($this->themePath . '/', '', $this->convertToTwig($template));

        // block actions/widgets from rendering
        if(in_array($template, $this->actions)) return;

        // render at the end
        if ($this->baseFile == $template)
        {
            if ($this->forms)
            {
                foreach ($this->forms['name'] as $name)
                {
                    $this->assign('form_' . $name, $this->compileForm($name));
                }
            }
            //var_dump($this->actions);exit;
            //var_dump($this->variables['positions']);exit;
            $this->end($template);
            exit;
        }
    }

    function end($template)
    {
        $this->startGlobals();
        $this->parseLabels();
        $this->setNav();
        $loader = new \Twig_Loader_Filesystem($this->themePath);
        $twig = new \Twig_Environment($loader, array(
            'cache' => FRONTEND_CACHE_PATH . '/CachedTemplates',
            //'debug' => (SPOON_DEBUG === true)
        ));
        if (SPOON_DEBUG) {
            // $twig->addExtension(new Twig_Extension_Debug());
            $this->assign('debug', true);
        }
        $this->template = $twig->loadTemplate($template);
        echo $this->template->render($this->variables);
    }

    function setNav()
    {
        // force main nav
        $nav = Navigation::getNavigation();
        $this->assign('navigation', $nav['page'][1]);
    }

    function compileForm($name)
    {
        if(isset($this->forms['data'][$name]))
        {
            $form = $this->forms['data'][$name];
            $compileForm ='<form accept-charset="UTF-8" action="' . $form->getAction() . '" method="'. $form->getMethod().'" '. $form->getParametersHTML() . '>';
            $compileForm .= $form->getField('form')->parse();
            if($form->getUseToken())
            {
                $compileForm .= '<input type="hidden" name="form_token" id="' . $form->getField('form_token')->getAttribute('id'). '" value="' . htmlspecialchars($form->getField('form_token')->getValue()). ' " />';
            }
        }
        return $compileForm;
    }

    function addForm($form)
    {
        $this->forms['name'][] = $form->getName();
        $this->forms['data'][$form->getName()] = $form;
    }

    function setPlugin($pluginPath)
    {
        //var_dump($pluginPath);exit;
    }

    function getAssignedVariables()
    {
        return $this->variables;
    }

    // /**
    //  * Map the frontend-specific modifiers
    //  */
    // private function mapCustomModifiers()
    // {
    //     // fetch the path for an include (theme file if available, core file otherwise)
    //     $this->mapModifier('getpath', array('Frontend\Core\Engine\TemplateModifiers', 'getPath'));

    //     // formatting
    //     $this->mapModifier('formatcurrency', array('Frontend\Core\Engine\TemplateModifiers', 'formatCurrency'));

    //     // URL for a specific pageId
    //     $this->mapModifier('geturl', array('Frontend\Core\Engine\TemplateModifiers', 'getURL'));

    //     // URL for a specific block/extra
    //     $this->mapModifier('geturlforblock', array('Frontend\Core\Engine\TemplateModifiers', 'getURLForBlock'));
    //     $this->mapModifier('geturlforextraid', array('Frontend\Core\Engine\TemplateModifiers', 'getURLForExtraId'));

    //     // page related
    //     $this->mapModifier('getpageinfo', array('Frontend\Core\Engine\TemplateModifiers', 'getPageInfo'));

    //     // convert var into navigation
    //     $this->mapModifier('getnavigation', array('Frontend\Core\Engine\TemplateModifiers', 'getNavigation'));
    //     $this->mapModifier('getsubnavigation', array('Frontend\Core\Engine\TemplateModifiers', 'getSubNavigation'));

    //     // parse a widget
    //     $this->mapModifier('parsewidget', array('Frontend\Core\Engine\TemplateModifiers', 'parseWidget'));

    //     // rand
    //     $this->mapModifier('rand', array('Frontend\Core\Engine\TemplateModifiers', 'random'));

    //     // string
    //     $this->mapModifier('formatfloat', array('Frontend\Core\Engine\TemplateModifiers', 'formatFloat'));
    //     $this->mapModifier('formatnumber', array('Frontend\Core\Engine\TemplateModifiers', 'formatNumber'));
    //     $this->mapModifier('truncate', array('Frontend\Core\Engine\TemplateModifiers', 'truncate'));
    //     $this->mapModifier('cleanupplaintext', array('Frontend\Core\Engine\TemplateModifiers', 'cleanupPlainText'));
    //     $this->mapModifier('camelcase', array('\SpoonFilter', 'toCamelCase'));
    //     $this->mapModifier('stripnewlines', array('Frontend\Core\Engine\TemplateModifiers', 'stripNewlines'));

    //     // dates
    //     $this->mapModifier('timeago', array('Frontend\Core\Engine\TemplateModifiers', 'timeAgo'));

    //     // users
    //     $this->mapModifier('usersetting', array('Frontend\Core\Engine\TemplateModifiers', 'userSetting'));

    //     // highlight
    //     $this->mapModifier('highlight', array('Frontend\Core\Engine\TemplateModifiers', 'highlightCode'));

    //     // urlencode
    //     $this->mapModifier('urlencode', 'urlencode');

    //     // strip tags
    //     $this->mapModifier('striptags', 'strip_tags');

    //     // debug stuff
    //     $this->mapModifier('dump', array('Frontend\Core\Engine\TemplateModifiers', 'dump'));

    //     // profiles
    //     $this->mapModifier('profilesetting', array('Frontend\Core\Engine\TemplateModifiers', 'profileSetting'));
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
}
