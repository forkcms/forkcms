<?php

namespace Backend\Core\Engine;

use Backend\Core\Engine\Language as BL;
use Frontend\Core\Engine\FormExtension;
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
 * that glues spoon libraries and code standards with twig.
 *
 * @author <thijs@wijs.be>
 */
class TwigTemplate extends BaseTwigTemplate
{
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
        $template = str_replace(BACKEND_MODULES_PATH, "", $template);
        // turn on output buffering
        ob_start();

        // render the compiled File
        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem(array(
            BACKEND_MODULES_PATH,
            BACKEND_CORE_PATH,
        ));

        $twig = new \Twig_Environment($loader, array(
            'cache' => BACKEND_CACHE_PATH.'/CachedTemplates/Twig_'.($this->debugMode ? 'dev/' : 'prod/'),
            'debug' => $this->debugMode,
        ));

        // debug options
        if ($this->debugMode === true) {
            $twig->addExtension(new \Twig_Extension_Debug());
        }

        if (count($this->forms) > 0) {
            foreach ($this->forms as $form) {
                $twig->addGlobal('form_'.$form->getName(), $form);
            }
        }

        // should always be included
        new FormExtension($twig);

        // start the filters / globals
        TwigFilters::getFilters($twig, 'Backend');
        $this->startGlobals($twig);

        // template
        $template = $twig->loadTemplate($template);
        echo $template->render($this->variables);

        // return template content
        return ob_get_clean();
    }
}
