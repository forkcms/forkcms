<?php

namespace ForkCMS\Backend\Core\Engine;

use ForkCMS\Component\Application\ApplicationInterface;
use ForkCMS\Component\Application\KernelLoader;
use Symfony\Component\HttpFoundation\Response;
use ForkCMS\Backend\Core\Engine\Base\AjaxAction;
use ForkCMS\Backend\Core\Language\Language as BackendLanguage;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * This class will handle AJAX-related stuff
 */
class Ajax extends KernelLoader implements ApplicationInterface
{
    /**
     * @var AjaxAction
     */
    private $ajaxAction;

    public function display(): Response
    {
        if ($this->ajaxAction instanceof AjaxAction) {
            return $this->ajaxAction->display();
        }

        return $this->ajaxAction->getContent();
    }

    public function initialize(): void
    {
        try {
            // check if the user is logged in
            $this->validateLogin();
        } catch (UnauthorizedHttpException $e) {
            $this->ajaxAction = new BackendBaseAjaxAction($this->getKernel());
            $this->ajaxAction->output(Response::HTTP_UNAUTHORIZED, null, $e->getMessage());

            return;
        }

        // named application
        if (!defined('NAMED_APPLICATION')) {
            define('NAMED_APPLICATION', 'BackendAjax');
        }

        try {
            // process the query string
            $url = new Url($this->getKernel());

            // create a new action
            $this->ajaxAction = new AjaxAction($this->getKernel(), $url->getModule(), $url->getAction());
        } catch (Exception $e) {
            $this->ajaxAction = new BackendBaseAjaxAction($this->getKernel());
            $this->ajaxAction->output(Response::HTTP_INTERNAL_SERVER_ERROR, null, $e->getMessage());
        }
    }

    /**
     * Do authentication stuff
     * This method could end the script by throwing an exception
     */
    private function validateLogin(): void
    {
        // check if the user is logged on, if not he shouldn't load any JS-file
        if (!Authentication::isLoggedIn()) {
            throw new UnauthorizedHttpException('Backend', 'Not logged in.');
        }

        // set interface language
        BackendLanguage::setLocale(Authentication::getUser()->getSetting('interface_language'));
    }
}
