<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use ForkCMS\App\ApplicationInterface;
use ForkCMS\App\KernelLoader;
use Symfony\Component\HttpFoundation\Response;
use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language as BackendLanguage;

/**
 * This class will handle AJAX-related stuff
 */
class Ajax extends KernelLoader implements ApplicationInterface
{
    /**
     * @var AjaxAction|BackendBaseAJAXAction
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
        // check if the user is logged in
        $this->validateLogin();

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
            $this->ajaxAction = new BackendBaseAJAXAction($this->getKernel());
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
            throw new Exception('Not logged in.');
        }

        // set interface language
        BackendLanguage::setLocale(Authentication::getUser()->getSetting('interface_language'));
    }
}
