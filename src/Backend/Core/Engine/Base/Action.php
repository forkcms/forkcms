<?php

namespace Backend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\TwigTemplate;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Backend\Core\Engine\Header;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Url;

/**
 * This class implements a lot of functionality that can be extended by a specific action
 */
class Action extends Object
{
    /**
     * The parameters (urldecoded)
     *
     * @var array
     */
    protected $parameters = array();

    /**
     * The header object
     *
     * @var Header
     */
    protected $header;

    /**
     * A reference to the current template
     *
     * @var TwigTemplate
     */
    public $tpl;

    /**
     * A reference to the URL-instance
     *
     * @var Url
     */
    public $URL;

    /**
     * The constructor will set some properties. It populates the parameter array with urldecoded
     * values for easy-use.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        // get objects from the reference so they are accessible from the action-object
        $this->tpl = $this->getContainer()->get('template');
        $this->URL = $this->getContainer()->get('url');
        $this->header = $this->getContainer()->get('header');

        // store the current module and action (we grab them from the URL)
        $this->setModule($this->URL->getModule());
        $this->setAction($this->URL->getAction());

        // populate the parameter array
        $this->parameters = $this->get('request')->query->all();
    }

    /**
     * Check if the token is ok
     */
    public function checkToken()
    {
        $fromSession = (\SpoonSession::exists('csrf_token')) ? \SpoonSession::get('csrf_token') : '';
        $fromGet = $this->getContainer()->get('request')->query->get('token');

        if ($fromSession != '' && $fromGet != '' && $fromSession == $fromGet) {
            return;
        }

        // clear the token
        \SpoonSession::set('csrf_token', '');

        $this->redirect(
            BackendModel::createURLForAction(
                'Index',
                null,
                null,
                array(
                    'error' => 'csrf',
                )
            )
        );
    }

    /**
     * @return string
     */
    protected function getBackendModulePath()
    {
        if ($this->URL->getModule() == 'Core') {
            return BACKEND_PATH . '/' . $this->URL->getModule();
        } else {
            return BACKEND_MODULES_PATH . '/' . $this->URL->getModule();
        }
    }

    /**
     * Display, this wil output the template to the browser
     * If no template is specified we build the path form the current module and action
     *
     * @param string $template The template to use, if not provided it will be based on the action.
     */
    public function display($template = null)
    {
        // parse header
        $this->header->parse();

        /*
         * If no template is specified, we have to build the path ourself. The default template is
         * based on the name of the current action
         */
        if ($template === null) {
            $template = '/'. $this->getModule() . '/Layout/Templates/' . $this->URL->getAction() . '.html.twig';
        }

        $this->content = $this->tpl->getContent($template);
    }

    /**
     * Execute the action
     */
    public function execute()
    {
        // add jquery, we will need this in every action, so add it globally
        $this->header->addJS('/js/vendors/jquery.min.js', 'Core', false, true);
        $this->header->addJS('/js/vendors/jquery-migrate.min.js', 'Core', false, true);
        $this->header->addJS('/js/vendors/jquery-ui.min.js', 'Core', false, true);
        $this->header->addJS('/js/vendors/bootstrap.min.js', 'Core', false, true);
        $this->header->addJS('/js/vendors/typeahead.bundle.min.js', 'Core', false, true);
        $this->header->addJS('/js/vendors/bootstrap-tagsinput.min.js', 'Core', false, true);
        $this->header->addJS('jquery/jquery.backend.js', 'Core');

        // add items that always need to be loaded
        $this->header->addJS('utils.js', 'Core', true, false, true);
        $this->header->addJS('backend.js', 'Core', true, false, true);

        // add module js
        if (is_file($this->getBackendModulePath() . '/Js/' . $this->getModule() . '.js')) {
            $this->header->addJS($this->getModule() . '.js', null, true, false, true);
        }

        // add action js
        if (is_file($this->getBackendModulePath() . '/Js/' . $this->getAction() . '.js')) {
            $this->header->addJS($this->getAction() . '.js', null, true, false, true);
        }

        // add core css files
        $this->header->addCSS('/css/vendors/bootstrap-tagsinput.css', 'Core', true);
        $this->header->addCSS('/css/vendors/bootstrap-tagsinput-typeahead.css', 'Core', true);
        $this->header->addCSS('screen.css', 'Core');
        $this->header->addCSS('debug.css', 'Core');

        // add module specific css
        if (is_file($this->getBackendModulePath() . '/Layout/Css/' . $this->getModule() . '.css')) {
            $this->header->addCSS($this->getModule() . '.css');
        }

        // store var so we don't have to call this function twice
        $var = array_map('strip_tags', $this->getParameter('var', 'array', array()));

        // is there a report to show?
        if ($this->getParameter('report') !== null) {
            // show the report
            $this->tpl->assign('report', true);

            // camelcase the string
            $messageName = strip_tags(\SpoonFilter::toCamelCase($this->getParameter('report'), '-'));

            // if we have data to use it will be passed as the var parameter
            if (!empty($var)) {
                $this->tpl->assign('reportMessage', vsprintf(BL::msg($messageName), $var));
            } else {
                $this->tpl->assign('reportMessage', BL::msg($messageName));
            }

            // highlight an element with the given id if needed
            if ($this->getParameter('highlight')) {
                $this->tpl->assign('highlight', strip_tags($this->getParameter('highlight')));
            }
        }

        // is there an error to show?
        if ($this->getParameter('error') !== null) {
            // camelcase the string
            $errorName = strip_tags(\SpoonFilter::toCamelCase($this->getParameter('error'), '-'));

            // if we have data to use it will be passed as the var parameter
            if (!empty($var)) {
                $this->tpl->assign('errorMessage', vsprintf(BL::err($errorName), $var));
            } else {
                $this->tpl->assign('errorMessage', BL::err($errorName));
            }
        }
    }

    /**
     * Get a parameter for a given key
     * The function will return null if the key is not available
     * By default we will cast the return value into a string, if you want
     * something else specify it by passing the wanted type.
     *
     * @param string $key          The name of the parameter.
     * @param string $type         The return-type, possible values are: bool,
     *                             boolean, int, integer, float, double,
     *                             string, array.
     * @param mixed  $defaultValue The value that should be returned if the key
     *                             is not available.
     *
     * @return mixed
     */
    public function getParameter($key, $type = 'string', $defaultValue = null)
    {
        $key = (string) $key;

        // parameter exists
        if (isset($this->parameters[$key]) && $this->parameters[$key] != '') {
            return \SpoonFilter::getValue($this->parameters[$key], null, null, $type);
        }

        return $defaultValue;
    }

    /**
     * Parse to template
     */
    protected function parse()
    {
    }

    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string|FormTypeInterface $type The built type of the form
     * @param mixed $data The initial data for the form
     * @param array $options Options for the form
     *
     * @return Form
     */
    public function createForm($type, $data = null, array $options = array())
    {
        return $this->get('form.factory')->create($type, $data, $options);
    }
}
