<?php

namespace Backend\Modules\Locale\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\DataGridArray as BackendDataGridArray;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;

/**
 * This is the index-action, it will display an overview of all the translations with an inline edit option.
 */
class Index extends BackendBaseActionIndex
{
    /**
     * @var BackendDataGridArray
     */
    private $dgActions;

    /**
     * @var BackendDataGridArray
     */
    private $dgErrors;

    /**
     * @var BackendDataGridArray
     */
    private $dgLabels;

    /**
     * @var BackendDataGridArray
     */
    private $dgMessages;

    /**
     * The form instance
     *
     * @var BackendForm
     */
    protected $frm;

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

    /**
     * Is God?
     *
     * @var bool
     */
    private $isGod;

    /**
     * Has Submissions?
     *
     * @var bool
     */
    private $hasSubmissions;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->isGod = BackendAuthentication::getUser()->isGod();
        $this->setFilter();
        $this->loadForm();
        $this->loadDataGrid();
        $this->parse();
        $this->display();
    }

    /**
     * Load the datagrid
     */
    private function loadDataGrid()
    {
        // init vars
        $langWidth = (60 / count($this->filter['language']));

        // if nothing is submitted
        // we don't need to fetch all the locale
        $this->hasSubmissions =
        (
            '' !== $this->filter['application'].$this->filter['module'].
            $this->filter['name'].$this->filter['value']
        );

        if ($this->hasSubmissions) {
            // get all the translations for the selected languages
            $translations = BackendLocaleModel::getTranslations(
                $this->filter['application'],
                $this->filter['module'],
                $this->filter['type'],
                $this->filter['language'],
                $this->filter['name'],
                $this->filter['value']
            );
        }

        // create datagrids
        $this->dgLabels = new BackendDataGridArray(isset($translations['lbl']) ? $translations['lbl'] : array());
        $this->dgMessages = new BackendDataGridArray(isset($translations['msg']) ? $translations['msg'] : array());
        $this->dgErrors = new BackendDataGridArray(isset($translations['err']) ? $translations['err'] : array());
        $this->dgActions = new BackendDataGridArray(isset($translations['act']) ? $translations['act'] : array());

        // put the datagrids (references) in an array so we can loop them
        $dataGrids = array(
            'lbl' => &$this->dgLabels,
            'msg' => &$this->dgMessages,
            'err' => &$this->dgErrors,
            'act' => &$this->dgActions,
        );

        // loop the datagrids (as references)
        foreach ($dataGrids as $type => &$dataGrid) {
            /** @var $dataGrid BackendDataGridArray */
            $dataGrid->setSortingColumns(array('module', 'name', 'application'), 'name');

            // disable paging
            $dataGrid->setPaging(false);

            // set header label for reference code
            $dataGrid->setHeaderLabels(array('name' => \SpoonFilter::ucfirst(BL::lbl('ReferenceCode'))));

            // hide the application when only one application is shown
            if ($this->filter['application'] != '') {
                $dataGrid->setColumnHidden('application');
            }

            // hide edite_on
            $dataGrid->setColumnHidden('edited_on');

            // set column attributes for each language
            foreach ($this->filter['language'] as $lang) {
                // add a class for the inline edit
                $dataGrid->setColumnAttributes($lang, array('class' => 'translationValue'));

                // add attributes, so the inline editing has all the needed data
                $dataGrid->setColumnAttributes(
                    $lang,
                    array(
                        'data-id' => '{language: \'' .
                            $lang . '\',application: \'[application]\',module: \'[module]\',name: \'[name]\',type: \'' .
                            $type . '\'}',
                    )
                );

                // escape the double quotes
                $dataGrid->setColumnFunction(
                    array('SpoonFilter', 'htmlentities'),
                    array('[' . $lang . ']', null, ENT_QUOTES),
                    $lang,
                    true
                );
                if ($type == 'act') {
                    $dataGrid->setColumnFunction('urldecode', array('[' . $lang . ']'), $lang, true);
                }

                // set header labels
                $dataGrid->setHeaderLabels(array($lang => \SpoonFilter::ucfirst(BL::lbl(mb_strtoupper($lang)))));

                // only 1 language selected?
                if (count($this->filter['language']) == 1) {
                    $dataGrid->setColumnAttributes($lang, array('style' => 'width: ' . $langWidth . '%'));

                    // add id of translation for the export
                    $dataGrid->setColumnAttributes($lang, array('data-numeric-id' => '[translation_id]'));

                    // Hide translation_id column (only if only one language is selected
                    // because the key doesn't exist if more than 1 language is selected)
                    $dataGrid->setColumnHidden('translation_id');

                    // check if this action is allowed
                    if (BackendAuthentication::isAllowedAction('Add')) {
                        // add copy button
                        $dataGrid->addColumnAction(
                            'copy',
                            null,
                            BL::lbl('Copy'),
                            BackendModel::createURLForAction('Add') . '&amp;id=[translation_id]' . $this->filterQuery
                        );
                    }

                    // check if this action is allowed
                    if (BackendAuthentication::isAllowedAction('Edit')) {
                        // add edit button
                        $dataGrid->addColumn(
                            'edit',
                            null,
                            BL::lbl('Edit'),
                            BackendModel::createURLForAction('Edit') . '&amp;id=[translation_id]' . $this->filterQuery
                        );
                    }
                } else {
                    // add id of translation for the export
                    $dataGrid->setColumnAttributes($lang, array('data-numeric-id' => '[translation_id_' . $lang .']'));
                    $dataGrid->setColumnHidden('translation_id_' . $lang);

                    //ugly fix but the browser does funny things with the percentage when showing lots of languages
                    $dataGrid->setColumnAttributes(
                        $lang,
                        array(
                            'style' => 'width: ' .
                                $langWidth .
                                '%; max-width: '. (600 / count($this->filter['language'])) .'px;',
                        )
                    );
                }
            }
        }
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('filter', BackendModel::createURLForAction(), 'get');
        $this->frm->addDropdown(
            'application',
            array(
                '' => '-',
                'Backend' => 'Backend',
                'Frontend' => 'Frontend',
            ),
            $this->filter['application']
        );
        $this->frm->addText('name', $this->filter['name']);
        $this->frm->addText('value', $this->filter['value']);
        $this->frm->addMultiCheckbox(
            'language',
            BackendLocaleModel::getLanguagesForMultiCheckbox($this->isGod),
            $this->filter['language'],
            'noFocus'
        );
        $this->frm->addMultiCheckbox(
            'type',
            BackendLocaleModel::getTypesForMultiCheckbox(),
            $this->filter['type'],
            'noFocus'
        );
        $this->frm->addDropdown(
            'module',
            BackendModel::getModulesForDropDown(),
            $this->filter['module']
        );
        $this->frm->getField('module')->setDefaultElement('-');

        // manually parse fields
        $this->frm->parse($this->tpl);
    }

    /**
     * Parse & display the page
     */
    protected function parse()
    {
        parent::parse();

        // parse datagrids
        $this->tpl->assign(
            'dgLabels',
            ($this->dgLabels->getNumResults() != 0) ? $this->dgLabels->getContent() : false
        );
        $this->tpl->assign(
            'dgMessages',
            ($this->dgMessages->getNumResults() != 0) ? $this->dgMessages->getContent() : false
        );
        $this->tpl->assign(
            'dgErrors',
            ($this->dgErrors->getNumResults() != 0) ? $this->dgErrors->getContent() : false
        );
        $this->tpl->assign(
            'dgActions',
            ($this->dgActions->getNumResults() != 0) ? $this->dgActions->getContent() : false
        );

        // is filtered?
        if ($this->getParameter('form', 'string', '') == 'filter') {
            $this->tpl->assign('filter', true);
        }

        // parse filter as query
        $this->tpl->assign('filter', $this->filterQuery);

        // parse isGod
        $this->tpl->assign('isGod', $this->isGod);

        // parse noItems, if all the datagrids are empty
        $this->tpl->assign(
            'noItems',
            $this->dgLabels->getNumResults() == 0 &&
            $this->dgMessages->getNumResults() == 0 &&
            $this->dgErrors->getNumResults() == 0 &&
            $this->dgActions->getNumResults() == 0
        );

        $this->tpl->assign(
            'hasSubmissions',
            $this->hasSubmissions
        );

        // parse the add URL
        $this->tpl->assign('addURL', BackendModel::createURLForAction('Add', null, null, null) . $this->filterQuery);
    }

    /**
     * Sets the filter based on the $_GET array.
     */
    private function setFilter()
    {
        // if no language is selected, set the working language as the selected
        if ($this->getParameter('language', 'array') == null) {
            $_GET['language'] = array(BL::getWorkingLanguage());
            $this->parameters['language'] = array(BL::getWorkingLanguage());
        }

        // if no type is selected, set labels as the selected type
        if ($this->getParameter('type', 'array') == null) {
            $_GET['type'] = array('lbl');
            $this->parameters['type'] = array('lbl', 'act', 'err', 'msg');
        }

        // set filter
        $this->filter['application'] = $this->getParameter('application', 'string', null);
        $this->filter['module'] = $this->getParameter('module', 'string', null);
        $this->filter['type'] = $this->getParameter('type', 'array');
        $this->filter['language'] = $this->getParameter('language', 'array');
        $this->filter['name'] = $this->getParameter('name') == null ? '' : $this->getParameter('name');
        $this->filter['value'] = $this->getParameter('value') == null ? '' : $this->getParameter('value');

        // only allow values from our types checkboxes to be set
        $this->filter['type'] = array_filter(
            $this->filter['type'],
            function ($type) {
                return array_key_exists(
                    $type,
                    BackendLocaleModel::getTypesForMultiCheckbox()
                );
            }
        );

        // only allow languages from our language checkboxes to be set
        $isGod = $this->isGod;
        $this->filter['language'] = array_filter(
            $this->filter['language'],
            function ($language) use ($isGod) {
                return array_key_exists(
                    $language,
                    BackendLocaleModel::getLanguagesForMultiCheckbox($isGod)
                );
            }
        );

        // build query for filter
        $this->filterQuery = BackendLocaleModel::buildURLQueryByFilter($this->filter);
    }
}
