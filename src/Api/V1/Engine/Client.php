<?php

namespace Api\V1\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;
use Backend\Core\Engine\Model as BackendModel;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_Template;

/**
 * Client for the Fork CMS API.
 */
class Client extends Api
{
    /**
     * @var Twig_Template
     */
    private $tpl;

    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $languages;

    /**
     * @var array
     */
    private $modules;

    /**
     * This method exists because the service container needs to be set before
     * the rest of API functionality gets loaded.
     */
    public function initialize()
    {
        $this->initializeTwig();

        $this->url = SITE_URL . str_replace('client/', 'index.php', $_SERVER['REQUEST_URI']);

        $this->loadLanguages();
        $this->loadModules();
        $this->display();
    }

    private function initializeTwig()
    {
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../Client/Layout/Templates/');
        $twig = new Twig_Environment($loader);

        $this->tpl = $twig->loadTemplate('index.html.twig');
    }

    protected function loadLanguages()
    {
        $settings = BackendModel::getContainer()->get('fork.settings');

        $this->languages = $settings->get('Core', 'languages', array('en'));
    }

    /**
     * Loops all backend modules, and builds a list of those that have an
     * api.php file in their engine.
     */
    protected function loadModules()
    {
        $modules = BackendModel::getModules();

        foreach ($modules as &$module) {
            // class names of the API file are always based on the name o/t module
            $className = 'Backend\\Modules\\' . $module . '\\Engine\\Api';
            if ($module == 'Core') {
                $className = 'Backend\\Core\\Engine\\Api';
            }

            /*
             * check if the api.php file exists for this module, and load it so our methods are
             * accessible by the Reflection API.
             */
            if (!class_exists($className)) {
                continue;
            }
            $methods = get_class_methods($className);

            // we will need the parameters + PHPDoc to generate our text fields
            foreach ($methods as $key => $method) {
                $methods[$key] = array(
                    'name' => $method,
                    'parameters' => $this->loadParameters($className, $method)
                );
            }

            // properly format so an iteration can do the work for us
            $this->modules[] = array(
                'name' => $module,
                'methods' => $methods
            );
        }
    }

    /**
     * This method is used to return an iteration-friendly list of parameters for a given method.
     *
     * @param string $className
     * @param string $method
     * @return array
     */
    protected function loadParameters($className, $method)
    {
        // dig for data on the chosen method, in the chosen class
        $parameters = array();
        $reflectionMethod = new \ReflectionMethod($className, $method);
        $PHPDoc = $reflectionMethod->getDocComment();

        /*
         * This regex filters out all parameters, along with their PHPDoc. We use this instead
         * of $reflectionMethod->getParameters(), since that returns ReflectionParameter objects
         * that, rather shamefully, do not contain PHPDoc.
         */
        preg_match_all('/@param[\s\t]+(.*)[\s\t]+\$(.*)[\s\t]+(.*)$/Um', $PHPDoc, $matches);
        if (array_key_exists(0, $matches) && empty($matches[0])) {
            return;
        }

        // we have to build up a custom stack of parameters
        foreach ($matches[0] as $i => $row) {
            $name = $matches[2][$i];

            if ($name === 'language') {
                continue;
            }

            $parameters[] = array(
                'name' => $name,
                'label' => $name . '-' . mt_rand(1, 99999),
                'optional' => (mb_substr_count($matches[2][$i], '[optional]') > 0),
                'description' => $matches[3][$i]
            );
        }

        return $parameters;
    }

    /**
     * @return Response
     */
    public function display()
    {
        $content = $this->tpl->render(array(
            'url' => $this->url,
            'languages' => $this->languages,
            'modules' => $this->modules
        ));

        return new Response($content, 200);
    }
}
