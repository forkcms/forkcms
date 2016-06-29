<?php

namespace Frontend\Core\Engine;

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
            $loader = $this->environment->getLoader();
            $loader = new \Twig_Loader_Chain(array(
                $loader,
                new \Twig_Loader_Filesystem(array(
                    $this->themePath . '/Modules',
                    $this->themePath,
                    FRONTEND_MODULES_PATH,
                    FRONTEND_PATH,
                    '/',
                )),
            ));
            $this->environment->setLoader($loader);
        }

        $this->environment->disableStrictVariables();

        // init Form extension
        new FormExtension($this->environment);

        // start the filters / globals
        TwigFilters::getFilters($this->environment, 'Frontend');
        $this->startGlobals($this->environment);
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
     */
    public function assign($key, $values = null)
    {
        // page hook, last call
        if ($key === 'page') {
            $this->baseFile = $values['template_path'];
        }

        parent::assign($key, $values);
    }

    /**
     * Convert a filename extension
     *
     * @param string $template
     *
     * @return string
     */
    public function getPath($template)
    {
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
     *
     * @return string The actual parsed content after executing this template.
     */
    public function getContent($template)
    {
        $template = $this->getPath($template);

        $this->templates[] = $template;

        return $this->render(
            $template,
            $this->variables
        );
    }

    public function render($template, array $variables = array())
    {
        if (!empty($this->forms)) {
            foreach ($this->forms as $form) {
                // using assign to pass the form as global
                $this->environment->addGlobal('form_' . $form->getName(), $form);
            }
        }

        // template
        if ($template === null) {
            $template = $this->baseFile;
        }

        return $this->environment->render($template, $variables);
    }
}
