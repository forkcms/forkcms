<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use ForkCMS\App\ApplicationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language as BackendLanguage;

/**
 * This class will handle AJAX-related stuff
 */
class Ajax extends Base\Object implements ApplicationInterface
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

    private function splitUpForkData(array $forkData): array
    {
        $language = $forkData['language'] ?? '';

        if ($language === '') {
            $language = $this->getContainer()->getParameter('site.default_language');
        }

        return [
            $forkData['module'] ?? '',
            $forkData['action'] ?? '',
            $language,
        ];
    }

    private function getForkDataFromRequest(Request $request): array
    {
        if ($request->request->has('fork')) {
            return $this->splitUpForkData((array) $request->request->get('fork'));
        }

        if ($request->query->has('fork')) {
            return $this->splitUpForkData((array) $request->query->get('fork'));
        }

        return $this->splitUpForkData($request->query->all());
    }

    public function initialize(): void
    {
        // check if the user is logged in
        $this->validateLogin();

        // named application
        if (!defined('NAMED_APPLICATION')) {
            define('NAMED_APPLICATION', 'BackendAjax');
        }

        list($module, $action, $language) = $this->getForkDataFromRequest($this->get('request'));

        try {
            // create URL instance, since the template modifiers need this object
            $url = new Url($this->getKernel());
            $url->setModule($module);

            $this->setModule($module);
            $this->setAction($action);
            $this->setLanguage($language);

            // create a new action
            $this->ajaxAction = new AjaxAction($this->getKernel(), $this->getAction(), $this->getModule());
        } catch (Exception $e) {
            $this->ajaxAction = new BackendBaseAJAXAction($this->getKernel());
            $this->ajaxAction->output(BackendBaseAJAXAction::ERROR, null, $e->getMessage());
        }
    }

    public function setLanguage(string $language): void
    {
        if (!array_key_exists($language, BackendLanguage::getWorkingLanguages())) {
            throw new Exception('Language invalid.');
        }

        BackendLanguage::setWorkingLanguage($language);
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
