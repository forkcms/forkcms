<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use ForkCMS\App\ApplicationInterface;
use Symfony\Component\HttpFoundation\Response;
use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language as BackendLanguage;

/**
 * This class will handle AJAX-related stuff
 */

class Ajax extends Base\Object implements ApplicationInterface
{
    /**
     * @var AjaxAction
     */
    private $ajaxAction;

    /**
     * @return Response
     */
    public function display()
    {
        return $this->ajaxAction->execute();
    }

    /**
     * @param array $forkData
     *
     * @return array
     */
    private function splitUpForkData(array $forkData)
    {
        return [
            isset($forkData['module']) ? $forkData['module'] : '',
            isset($forkData['action']) ? $forkData['action'] : '',
            isset($forkData['language']) ? $forkData['language'] : '',
        ];
    }

    public function initialize()
    {
        // check if the user is logged in
        $this->validateLogin();

        // named application
        if (!defined('NAMED_APPLICATION')) {
            define('NAMED_APPLICATION', 'BackendAjax');
        }

        $request = $this->get('request');

        list($module, $action, $language) = $this->splitUpForkData(
            $request->request->has('fork')
                ? (array) $request->request->get('fork')
                : ($request->query->has('fork') ? (array) $request->query->get('fork') : $request->query->all())
        );

        if ($language === '') {
            $language = SITE_DEFAULT_LANGUAGE;
        }

        try {
            // create URL instance, since the template modifiers need this object
            $url = new Url($this->getKernel());
            $url->setModule($module);

            $this->setModule($module);
            $this->setAction($action);
            $this->setLanguage($language);

            // create a new action
            $this->ajaxAction = new AjaxAction($this->getKernel());
            $this->ajaxAction->setModule($this->getModule());
            $this->ajaxAction->setAction($this->getAction());
        } catch (Exception $e) {
            $this->ajaxAction = new BackendBaseAJAXAction($this->getKernel());
            $this->ajaxAction->output(BackendBaseAJAXAction::ERROR, null, $e->getMessage());
        }
    }

    /**
     * @param string $language
     *
     * @throws Exception If the provided language is not valid
     */
    public function setLanguage($language)
    {
        // get the possible languages
        $possibleLanguages = BackendLanguage::getWorkingLanguages();

        // validate
        if (!array_key_exists($language, $possibleLanguages)) {
            throw new Exception('Language invalid.');
        }

        // set working language
        BackendLanguage::setWorkingLanguage($language);
    }

    /**
     * Do authentication stuff
     * This method could end the script by throwing an exception
     */
    private function validateLogin()
    {
        // check if the user is logged on, if not he shouldn't load any JS-file
        if (!Authentication::isLoggedIn()) {
            throw new Exception('Not logged in.');
        }

        // set interface language
        BackendLanguage::setLocale(Authentication::getUser()->getSetting('interface_language'));
    }
}
