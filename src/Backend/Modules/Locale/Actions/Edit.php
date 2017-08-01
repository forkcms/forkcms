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
        $this->form = new BackendForm('edit', BackendModel::createUrlForAction(null, null, null, ['id' => $this->id]) . $this->filterQuery);
        $this->form->addDropdown('application', ['Backend' => 'Backend', 'Frontend' => 'Frontend'], $this->record['application']);
        $this->form->addDropdown('module', BackendModel::getModulesForDropDown(), $this->record['module']);
        $this->form->addDropdown('type', BackendLocaleModel::getTypesForDropDown(), $this->record['type']);
        $this->form->addText('name', $this->record['name']);
        $this->form->addTextarea('value', $this->record['value'], null, null, true);
        $this->form->addDropdown('language', BL::getWorkingLanguages(), $this->record['language']);
    }

    protected function parse(): void
    {
        parent::parse();

        // prevent XSS
        $filter = \SpoonFilter::arrayMapRecursive('htmlspecialchars', $this->filter);

        // parse filter
        $this->template->assignArray($filter);
        $this->template->assign('filterQuery', $this->filterQuery);

        // assign id, name
        $this->template->assign('name', $this->record['name']);
        $this->template->assign('id', $this->record['id']);
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
        if ($this->form->isSubmitted()) {
            $this->form->cleanupFields();

            // redefine fields
            $txtName = $this->form->getField('name');
            $txtValue = $this->form->getField('value');

            // name checks
            if ($txtName->isFilled(BL::err('FieldIsRequired'))) {
                // allowed regex (a-z and 0-9)
                if ($txtName->isValidAgainstRegexp('|^([a-z0-9])+$|i', BL::err('InvalidName'))) {
                    // first letter does not seem to be a capital one
                    if (!in_array(mb_substr($txtName->getValue(), 0, 1), range('A', 'Z'))) {
                        $txtName->setError(BL::err('InvalidName'));
                    } else {
                        // check if exists
                        if (BackendLocaleModel::existsByName($txtName->getValue(), $this->form->getField('type')->getValue(), $this->form->getField('module')->getValue(), $this->form->getField('language')->getValue(), $this->form->getField('application')->getValue(), $this->id)) {
                            $txtName->setError(BL::err('AlreadyExists'));
                        }
                    }
                }
            }

            // value checks
            if ($txtValue->isFilled(BL::err('FieldIsRequired'))) {
                // in case this is a 'act' type, there are special rules concerning possible values
                if ($this->form->getField('type')->getValue() == 'act') {
                    if (rawurlencode($txtValue->getValue()) != CommonUri::getUrl($txtValue->getValue())) {
                        $txtValue->addError(BL::err('InvalidValue'));
                    }
                }
            }

            // module should be 'Core' for any other application than backend
            if ($this->form->getField('application')->getValue() != 'Backend' && $this->form->getField('module')->getValue() != 'Core') {
                $this->form->getField('module')->setError(BL::err('ModuleHasToBeCore', $this->getModule()));
            }

            if ($this->form->isCorrect()) {
                // build item
                $item = [];
                $item['id'] = $this->id;
                $item['user_id'] = BackendAuthentication::getUser()->getUserId();
                $item['language'] = $this->form->getField('language')->getValue();
                $item['application'] = $this->form->getField('application')->getValue();
                $item['module'] = $this->form->getField('module')->getValue();
                $item['type'] = $this->form->getField('type')->getValue();
                $item['name'] = $this->form->getField('name')->getValue();
                $item['value'] = $this->form->getField('value')->getValue();
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
        $this->template->assign('deleteForm', $deleteForm->createView());
    }
}
