<?php

namespace Backend\Modules\Locale\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Form\Type\DeleteType;
use Common\Uri as CommonUri;
use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;

/**
 * This is the edit action, it will display a form to edit an existing locale item.
 */
class Edit extends BackendBaseActionEdit
{
    /**
     * Filter variables
     *
     * @var array
     */
    private $filter;

    /**
     * @var string
     */
    private $filterQuery;

    public function execute(): void
    {
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exists
        if ($this->id !== 0 && BackendLocaleModel::exists($this->id)) {
            parent::execute();
            $this->setFilter();
            $this->getData();
            $this->loadForm();
            $this->validateForm();
            $this->loadDeleteForm();
            $this->parse();
            $this->display();
        } else {
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
    }

    private function getData(): void
    {
        $this->record = BackendLocaleModel::get($this->id);
    }

    private function loadForm(): void
    {
        $this->frm = new BackendForm('edit', BackendModel::createUrlForAction(null, null, null, ['id' => $this->id]) . $this->filterQuery);
        $this->frm->addDropdown('application', ['Backend' => 'Backend', 'Frontend' => 'Frontend'], $this->record['application']);
        $this->frm->addDropdown('module', BackendModel::getModulesForDropDown(), $this->record['module']);
        $this->frm->addDropdown('type', BackendLocaleModel::getTypesForDropDown(), $this->record['type']);
        $this->frm->addText('name', $this->record['name']);
        $this->frm->addTextarea('value', $this->record['value'], null, null, true);
        $this->frm->addDropdown('language', BL::getWorkingLanguages(), $this->record['language']);
    }

    protected function parse(): void
    {
        parent::parse();

        // prevent XSS
        $filter = \SpoonFilter::arrayMapRecursive('htmlspecialchars', $this->filter);

        // parse filter
        $this->tpl->assignArray($filter);
        $this->tpl->assign('filterQuery', $this->filterQuery);

        // assign id, name
        $this->tpl->assign('name', $this->record['name']);
        $this->tpl->assign('id', $this->record['id']);
    }

    /**
     * Sets the filter based on the $_GET array.
     */
    private function setFilter(): void
    {
        $this->filter['language'] = $this->getRequest()->query->get('language', []);
        if (empty($this->filter['language'])) {
            $this->filter['language'] = BL::getWorkingLanguage();
        }
        $this->filter['application'] = $this->getRequest()->query->get('application');
        $this->filter['module'] = $this->getRequest()->query->get('module');
        $this->filter['type'] = $this->getRequest()->query->get('type', '');
        if ($this->filter['type'] === '') {
            $this->filter['type'] = null;
        }
        $this->filter['name'] = $this->getRequest()->query->get('name');
        $this->filter['value'] = $this->getRequest()->query->get('value');

        // build query for filter
        $this->filterQuery = BackendLocaleModel::buildUrlQueryByFilter($this->filter);
    }

    private function validateForm(): void
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // redefine fields
            $txtName = $this->frm->getField('name');
            $txtValue = $this->frm->getField('value');

            // name checks
            if ($txtName->isFilled(BL::err('FieldIsRequired'))) {
                // allowed regex (a-z and 0-9)
                if ($txtName->isValidAgainstRegexp('|^([a-z0-9])+$|i', BL::err('InvalidName'))) {
                    // first letter does not seem to be a capital one
                    if (!in_array(mb_substr($txtName->getValue(), 0, 1), range('A', 'Z'))) {
                        $txtName->setError(BL::err('InvalidName'));
                    } else {
                        // check if exists
                        if (BackendLocaleModel::existsByName($txtName->getValue(), $this->frm->getField('type')->getValue(), $this->frm->getField('module')->getValue(), $this->frm->getField('language')->getValue(), $this->frm->getField('application')->getValue(), $this->id)) {
                            $txtName->setError(BL::err('AlreadyExists'));
                        }
                    }
                }
            }

            // value checks
            if ($txtValue->isFilled(BL::err('FieldIsRequired'))) {
                // in case this is a 'act' type, there are special rules concerning possible values
                if ($this->frm->getField('type')->getValue() == 'act') {
                    if (rawurlencode($txtValue->getValue()) != CommonUri::getUrl($txtValue->getValue())) {
                        $txtValue->addError(BL::err('InvalidValue'));
                    }
                }
            }

            // module should be 'Core' for any other application than backend
            if ($this->frm->getField('application')->getValue() != 'Backend' && $this->frm->getField('module')->getValue() != 'Core') {
                $this->frm->getField('module')->setError(BL::err('ModuleHasToBeCore', $this->getModule()));
            }

            if ($this->frm->isCorrect()) {
                // build item
                $item = [];
                $item['id'] = $this->id;
                $item['user_id'] = BackendAuthentication::getUser()->getUserId();
                $item['language'] = $this->frm->getField('language')->getValue();
                $item['application'] = $this->frm->getField('application')->getValue();
                $item['module'] = $this->frm->getField('module')->getValue();
                $item['type'] = $this->frm->getField('type')->getValue();
                $item['name'] = $this->frm->getField('name')->getValue();
                $item['value'] = $this->frm->getField('value')->getValue();
                $item['edited_on'] = BackendModel::getUTCDate();

                // update item
                BackendLocaleModel::update($item);

                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createUrlForAction('Index', null, null, null) . '&report=edited&var=' . rawurlencode($item['name']) . '&highlight=row-' . $item['id'] . $this->filterQuery);
            }
        }
    }

    private function loadDeleteForm(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $this->record['id']],
            ['module' => $this->getModule()]
        );
        $this->tpl->assign('deleteForm', $deleteForm->createView());
    }
}
