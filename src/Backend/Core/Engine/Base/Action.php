<?php

namespace Backend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\TwigTemplate;
use Common\Exception\RedirectException;
use ForkCMS\App\KernelLoader;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Backend\Core\Engine\Header;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Url;

/**
 * This class implements a lot of functionality that can be extended by a specific action
 */
class Action extends KernelLoader
{
    /**
     * The parameters (urldecoded)
     *
     * @var array
     */
    protected $parameters = [];

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
    protected $template;

    /**
     * A reference to the URL-instance
     *
     * @var Url
     */
    protected $url;

    /**
     * The actual output
     *
     * @var mixed
     */
    protected $content;

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
        $this->template = $this->getContainer()->get('template');
        $this->url = $this->getContainer()->get('url');
        $this->header = $this->getContainer()->get('header');

        // populate the parameter array
        $this->parameters = $this->getRequest()->query->all();
    }

    public function getModule(): string
    {
        return $this->url->getModule();
    }

    public function getAction(): string
    {
        return $this->url->getAction();
    }

    /**
     * Check if the token is ok
     */
    public function checkToken(): void
    {
        $fromSession = BackendModel::getSession()->get('csrf_token', '');
        $fromGet = $this->getRequest()->query->get('token');

        if ($fromSession !== '' && $fromGet !== '' && $fromSession === $fromGet) {
            return;
        }

        // clear the token
        BackendModel::getSession()->set('csrf_token', '');

        $this->redirect(
            BackendModel::createUrlForAction(
                'Index',
                null,
                null,
                [
                    'error' => 'csrf',
                ]
            )
        );
    }

    protected function getBackendModulePath(): string
    {
        if ($this->url->getModule() === 'Core') {
            return BACKEND_PATH . '/' . $this->url->getModule();
        }

        return BACKEND_MODULES_PATH . '/' . $this->url->getModule();
    }

    /**
     * Display, this wil output the template to the browser
     * If no template is specified we build the path form the current module and action
     *
     * @param string $template The template to use, if not provided it will be based on the action.
     */
    public function display(string $template = null): void
    {
        // parse header
        $this->header->parse();

        /*
         * If no template is specified, we have to build the path ourself. The default template is
         * based on the name of the current action
         */
        if ($template === null) {
            $template = '/' . $this->getModule() . '/Layout/Templates/' . $this->url->getAction() . '.html.twig';
        }

        $this->content = $this->template->getContent($template);
    }

    public function execute(): void
    {
        // add module js
        if (is_file($this->getBackendModulePath() . '/Js/' . $this->getModule() . '.js')) {
            $this->header->addJS($this->getModule() . '.js', null, true, false, true);
        }

        // add action js
        if (is_file($this->getBackendModulePath() . '/Js/' . $this->getAction() . '.js')) {
            $this->header->addJS($this->getAction() . '.js', null, true, false, true);
        }

        // add module specific css
        if (is_file($this->getBackendModulePath() . '/Layout/Css/' . $this->getModule() . '.css')) {
            $this->header->addCSS($this->getModule() . '.css');
        }

        // store var so we don't have to call this function twice
        $var = $this->getRequest()->query->get('var', []);
        if ($var === '') {
            $var = [];
        }
        $var = array_map('strip_tags', (array) $var);

        // is there a report to show?
        if ($this->getRequest()->query->get('report', '') !== '') {
            // show the report
            $this->template->assign('report', true);

            // camelcase the string
            $messageName = strip_tags(\SpoonFilter::toCamelCase($this->getRequest()->query->get('report'), '-'));

            // if we have data to use it will be passed as the var parameter
            if (!empty($var)) {
                $this->template->assign('reportMessage', vsprintf(BL::msg($messageName), $var));
            } else {
                $this->template->assign('reportMessage', BL::msg($messageName));
            }

            // highlight an element with the given id if needed
            if ($this->getRequest()->query->get('highlight')) {
                $this->template->assign('highlight', strip_tags($this->getRequest()->query->get('highlight')));
            }
        }

        // is there an error to show?
        if ($this->getRequest()->query->get('error', '') !== '') {
            // camelcase the string
            $errorName = strip_tags(\SpoonFilter::toCamelCase($this->getRequest()->query->get('error'), '-'));

            // if we have data to use it will be passed as the var parameter
            if (!empty($var)) {
                $this->template->assign('errorMessage', vsprintf(BL::err($errorName), $var));
            } else {
                $this->template->assign('errorMessage', BL::err($errorName));
            }
        }
    }

    /**
     * Parse to template
     */
    protected function parse(): void
    {
    }

    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string $type FQCN of the form type class i.e: MyClass::class
     * @param mixed $data The initial data for the form
     * @param array $options Options for the form
     *
     * @return Form
     */
    public function createForm(string $type, $data = null, array $options = []): Form
    {
        return $this->get('form.factory')->create($type, $data, $options);
    }

    /**
     * Get the request from the container.
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return BackendModel::getRequest();
    }

    /**
     * Since the display action in the backend is rather complicated and we
     * want to make this work with our Kernel, I've added this getContent
     * method to extract the output from the actual displaying.
     *
     * With this function we'll be able to get the content and return it as a
     * Symfony output object.
     *
     * @return Response
     */
    public function getContent(): Response
    {
        return new Response(
            $this->content,
            Response::HTTP_OK
        );
    }

    /**
     * Redirect to a given URL
     *
     * This is a helper method as the actual implementation is located in the url class
     *
     * @param string $url The URL to redirect to.
     * @param int $code The redirect code, default is 302 which means this is a temporary redirect.
     *
     * @throws RedirectException
     */
    public function redirect(string $url, int $code = Response::HTTP_FOUND): void
    {
        $this->get('url')->redirect($url, $code);
    }
}
