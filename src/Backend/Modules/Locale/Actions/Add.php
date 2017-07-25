<?php

namespace Backend\Modules\Locale\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Uri as CommonUri;
use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;

/**
 * This is the add action, it will display a form to add an item to the locale.
 */
class Add extends BackendBaseActionAdd
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
        parent::execute();
        $this->setFilter();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function loadForm(): void
    {
        if ($this->getRequest()->query->getInt('id') !== 0) {
            // get the translation
            $translation = BackendLocaleModel::get($this->getRequest()->query->getInt('id'));

            // if not empty, set the filter
            if (!empty($translation)) {
                // we are copying the given translation
                $isCopy = true;
            } else {
                $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing' . $this->filterQuery);
            }
        } else {
            $isCopy = false;
        }

        // create form
        $this->form = new BackendForm('add', BackendModel::createUrlForAction() . $this->filterQuery);

        // create and add elements
        $this->form->addDropdown('application', ['Backend' => 'Backend', 'Frontend' => 'Frontend'], $isCopy ? $translation['application'] : $this->filter['application']);
        $this->form->addDropdown('module', BackendModel::getModulesForDropDown(), $isCopy ? $translation['module'] : $this->filter['module']);
        $this->form->addDropdown('type', BackendLocaleModel::getTypesForDropDown(), $isCopy ? $translation['type'] : $this->filter['type'][0]);
        $this->form->addText('name', $isCopy ? $translation['name'] : $this->filter['name']);
        $this->form->addTextarea('value', $isCopy ? $translation['value'] : $this->filter['value'], null, null, true);
        $this->form->addDropdown('language', BL::getWorkingLanguages(), $isCopy ? $translation['language'] : $this->filter['language'][0]);
    }

    protected function parse(): void
    {
        parent::parse();

        // prevent XSS
        $filter = \SpoonFilter::arrayMapRecursive('htmlspecialchars', $this->filter);

        $this->template->assignArray($filter);
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
        $this->filterQuery = '&' . http_build_query($this->filter, null, '&', PHP_QUERY_RFC3986);
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
                        // this name already exists in this language
                        if (BackendLocaleModel::existsByName(
                            $txtName->getValue(),
                            $this->form->getField('type')->getValue(),
                            $this->form->getField('module')->getValue(),
                            $this->form->getField('language')->getValue(),
                            $this->form->getField('application')->getValue()
                        )
                        ) {
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

            // module should be 'core' for any other application than backend
            if ($this->form->getField('application')->getValue() != 'Backend' && $this->form->getField('module')->getValue() != 'Core') {
                $this->form->getField('module')->setError(BL::err('ModuleHasToBeCore'));
            }

            if ($this->form->isCorrect()) {
                // build item
                $item = [];
                $item['user_id'] = BackendAuthentication::getUser()->getUserId();
                $item['language'] = $this->form->getField('language')->getValue();
                $item['application'] = $this->form->getField('application')->getValue();
                $item['module'] = $this->form->getField('module')->getValue();
                $item['type'] = $this->form->getField('type')->getValue();
                $item['name'] = $this->form->getField('name')->getValue();
                $item['value'] = $this->form->getField('value')->getValue();
                $item['edited_on'] = BackendModel::getUTCDate();

                // update item
                $item['id'] = BackendLocaleModel::insert($item);

                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createUrlForAction('Index', null, null, null) . '&report=added&var=' . rawurlencode($item['name']) . '&highlight=row-' . $item['id'] . $this->filterQuery);
            }
        }
    }
}
