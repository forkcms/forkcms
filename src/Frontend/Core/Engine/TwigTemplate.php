<?php

namespace Frontend\Core\Engine;

use Frontend\Core\Engine\Language as FL;
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
 * that glues spoon libraries and code standards with twig
 *
 * @author <thijs@wijs.be>
 */

class TwigTemplate extends BaseTwigTemplate
{
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
     * The constructor will store the instance in the reference, preset some settings and map the custom modifiers.
     */
    public function __construct()
    {
        parent::__construct(func_get_arg(0), func_get_arg(1), func_get_arg(2));
        $this->debugMode = Model::getContainer()->getParameter('kernel.debug');

        $this->forkSettings = Model::get('fork.settings');
        if ($this->forkSettings) {
            $this->themePath = FRONTEND_PATH . '/Themes/' . $this->forkSettings->get('Core', 'theme', 'default');
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
        // page hook, last call
        if ($key === 'page') {
            $this->baseFile = $values['template_path'];
            $this->baseSpoonFile = FRONTEND_PATH . '/' . $values['template_path'];
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
        foreach ($positions as &$blocks) {
            foreach ($blocks as &$block) {
                // skip html
                if (!empty($block['html'])) {
                    continue;
                }

                $block['extra_data'] = @unserialize($block['extra_data']);

                // legacy search the correct module path
                if ($block['extra_type'] === 'widget' && $block['extra_action']) {
                    if (isset($block['extra_data']['template'])) {
                        $tpl = substr($block['extra_data']['template'], 0, -4);
                        $block['include_path'] = $this->widgets[$tpl];
                    } else {
                        $block['include_path'] = $this->getPath(
                            FRONTEND_MODULES_PATH .
                            '/' . $block['extra_module'] .
                            '/Layout/Widgets/' . $block['extra_action'] . '.html.twig'
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
    public function getPath($template)
    {
        $template = Theme::getPath($template);
        if (strpos($template, FRONTEND_MODULES_PATH) !== false) {
            return str_replace(FRONTEND_MODULES_PATH . '/', '', $template);
        }
        // else it's in the theme folder
        return str_replace($this->themePath . '/', '', $template);
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
        } elseif (strpos($path['filename'], 'Default') === false) {
            $this->block = $this->getPath($template);
        }

        // only baseFile can start the render
        if ($this->baseSpoonFile === $template) {
            return $this->renderTemplate();
        }
    }

    /**
     * Renders the Page
     *
     * @param  string $template path to render
     */
    public function renderTemplate($template = null)
    {
        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem(array(
            $this->themePath,
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
        }

        // init Form extension
        new FormExtension($twig);

        // start the filters / globals
        TwigFilters::getFilters($twig, 'Frontend');
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
}
