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
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;

/**
 * This action will update a translation using AJAX
 *
 * @author Lowie Benoot <lowie.benoot@netlash.com>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class SaveTranslation extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $isGod = BackendAuthentication::getUser()->isGod();

        // get possible languages
        if ($isGod) $possibleLanguages = array_unique(array_merge(BL::getWorkingLanguages(), BL::getInterfaceLanguages()));
        else $possibleLanguages = BL::getWorkingLanguages();

        // get parameters
        $language = \SpoonFilter::getPostValue('language', array_keys($possibleLanguages), null, 'string');
        $module = \SpoonFilter::getPostValue('module', BackendModel::getModules(), null, 'string');
        $name = \SpoonFilter::getPostValue('name', null, null, 'string');
        $type = \SpoonFilter::getPostValue('type', BackendModel::getContainer()->get('database')->getEnumValues('locale', 'type'), null, 'string');
        $application = \SpoonFilter::getPostValue('application', array('Backend', 'Frontend'), null, 'string');
        $value = \SpoonFilter::getPostValue('value', null, null, 'string');

        // validate values
        if (trim($value) == '' || $language == '' || $module == '' || $type == '' || $application == '' || ($application == 'Frontend' && $module != 'Core')) $error = BL::err('InvalidValue');

        // in case this is a 'act' type, there are special rules concerning possible values
        if ($type == 'act' && !isset($error)) if (urlencode($value) != CommonUri::getUrl($value)) $error = BL::err('InvalidActionValue', $this->getModule());

        // no error?
        if (!isset($error)) {
            // build item
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

                // update in db
                BackendLocaleModel::update($item);
            }

            // doesn't exist yet
            else {
                // insert in db
                BackendLocaleModel::insert($item);
            }

            // output OK
            $this->output(static::OK);
        }

        // output the error
        else $this->output(static::ERROR, null, $error);
    }
}
