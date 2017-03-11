<?php

namespace Frontend\Core\Engine;

use Common\Core\Twig\BaseTwigTemplate;
use Common\Core\Twig\Extensions\TwigFilters;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bridge\Twig\Extension\FormExtension as SymfonyFormExtension;

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
     * theme path location
     *
     * @var string
     */
    private $themePath;

    /**
     * The constructor will store the instance in the reference, preset some settings and map the custom modifiers.
     */
    public function __construct()
    {
        parent::__construct(func_get_arg(0), func_get_arg(1), func_get_arg(2));

        $this->debugMode = Model::getContainer()->getParameter('kernel.debug');

        $this->forkSettings = Model::get('fork.settings');
        // fork has been installed
        try {
            if ($this->forkSettings) {
                $this->themePath = FRONTEND_PATH . '/Themes/' . $this->forkSettings->get('Core', 'theme', 'default');
                $loader = $this->environment->getLoader();
                $loader = new \Twig_Loader_Chain(
                    array(
                        $loader,
                        new \Twig_Loader_Filesystem($this->getLoadingFolders()),
                    )
                );
                $this->environment->setLoader($loader);

                // connect symphony forms
                $formEngine = new TwigRendererEngine($this->getFormTemplates('FormLayout.html.twig'));
                $formEngine->setEnvironment($this->environment);
                $this->environment->addExtension(
                    new SymfonyFormExtension(
                        new TwigRenderer($formEngine, Model::get('security.csrf.token_manager'))
                    )
                );
            }

            $this->environment->disableStrictVariables();

            // init Form extension
            new FormExtension($this->environment);

            // start the filters / globals
            TwigFilters::getFilters($this->environment, 'Frontend');
            $this->startGlobals($this->environment);
        } catch (\PDOException $exception) {
            // fork is not installed apparently so we need to catch this error
        }
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
     * Adds a global variable to the template
     *
     * @param string $name
     * @param mixed $value
     */
    public function addGlobal($name, $value)
    {
        $this->environment->addGlobal($name, $value);
    }

    /**
     * Fetch the parsed content from this template.
     *
     * @param string $template The location of the template file, used to display this template.
     *
     * @return string The actual parsed content after executing this template.
     */
    public function getContent($template)
    {
        $template = $this->getPath($template);

        $content = $this->render(
            $template,
            $this->variables
        );

        $this->variables = array();

        return $content;
    }

    /**
     * @param string $template
     * @param array $variables
     *
     * @return string
     */
    public function render($template, array $variables = array())
    {
        if (!empty($this->forms)) {
            foreach ($this->forms as $form) {
                // using assign to pass the form as global
                $this->environment->addGlobal('form_' . $form->getName(), $form);
            }
        }

        return $this->environment->render($template, $variables);
    }

    /**
     * @return array
     */
    private function getLoadingFolders()
    {
        $filesystem = new Filesystem();

        return array_filter(
            array(
                $this->themePath . '/Modules',
                $this->themePath,
                FRONTEND_MODULES_PATH,
                FRONTEND_PATH,
            ),
            function ($folder) use ($filesystem) {
                return $filesystem->exists($folder);
            }
        );
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    private function getFormTemplates($fileName)
    {
        $filesystem = new Filesystem();

        return array_filter(
            array(
                FRONTEND_PATH . '/Core/Layout/Templates/' . $fileName,
                $this->themePath . '/Core/Layout/Templates/' . $fileName,
            ),
            function ($folder) use ($filesystem) {
                return $filesystem->exists($folder);
            }
        );
    }
}
