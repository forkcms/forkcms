<?php

namespace Backend\Modules\Locale\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Uri as CommonUri;
use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * This action will update a translation using AJAX
 */
class SaveTranslation extends BackendBaseAJAXAction
{
    public function execute(): void
    {
        parent::execute();
        $isGod = BackendAuthentication::getUser()->isGod();

        // get possible languages
        if ($isGod) {
            $possibleLanguages = array_unique(array_merge(BL::getWorkingLanguages(), BL::getInterfaceLanguages()));
        } else {
            $possibleLanguages = BL::getWorkingLanguages();
        }

        // get parameters
        $language = $this->getRequest()->request->get('language');
        if (!array_key_exists($language, $possibleLanguages)) {
            $language = '';
        }
        $module = $this->getRequest()->request->get('module');
        if (!in_array($module, BackendModel::getModules())) {
            $module = '';
        }
        $name = $this->getRequest()->request->get('name', '');
        $type = $this->getRequest()->request->get('type');
        if (!in_array($type, BackendModel::get('database')->getColumn('SELECT type FROM locale GROUP BY type'))) {
            $type = '';
        }
        $application = $this->getRequest()->request->get('application');
        if (!in_array($application, ['Backend', 'Frontend'])) {
            $application = '';
        }
        $value = $this->getRequest()->request->get('value');

        // validate values
        if (trim($value) === ''
            || $language === ''
            || $module === ''
            || $type === ''
            || $application === ''
            || ($application === 'Frontend' && $module !== 'Core')
        ) {
            $error = BL::err('InvalidValue');
        }

        // in case this is a 'act' type, there are special rules concerning possible values
        if ($type === 'act' && !isset($error)) {
            if (rawurlencode($value) != CommonUri::getUrl($value)) {
                $error = BL::err('InvalidActionValue', $this->getModule());
            }
        }

        if (isset($error)) {
            $this->output(Response::HTTP_INTERNAL_SERVER_ERROR, null, $error);
        }

        // build item
        $item = [];
        $item['language'] = $language;
        $item['module'] = $module;
        $item['name'] = $name;
        $item['type'] = $type;
        $item['application'] = $application;
        $item['value'] = $value;
        $item['edited_on'] = BackendModel::getUTCDate();
        $item['user_id'] = BackendAuthentication::getUser()->getUserId();

        // does the translation exist?
        if (BackendLocaleModel::existsByName($name, $type, $module, $language, $application)) {
            // add the id to the item
            $item['id'] = (int) BackendLocaleModel::getByName($name, $type, $module, $language, $application);

            // update in database
            BackendLocaleModel::update($item);
        } else {
            // insert in database
            BackendLocaleModel::insert($item);
        }

        // output OK
        $this->output(Response::HTTP_OK);
    }
}
