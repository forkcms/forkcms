<?php

namespace Backend\Modules\Locale\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridArray as BackendDataGridArray;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;
use Backend\Modules\Multisite\Engine\Model as BackendMultisiteModel;

/**
 * This is the index-action, it will display an overview of all the translations with an inline edit option.
 *
 * @author Lowie Benoot <lowie.benoot@netlash.com>
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class Index extends BackendBaseActionIndex
{
    /**
     * @var BackendDataGridArray
     */
    private $dgActions, $dgErrors, $dgLabels, $dgMessages;

    /**
     * Filter variables
     *
     * @var	array
     */
    private $filter;

    /**
     * @var string
     */
    private $filterQuery;

    /**
     * Is God?
     *
     * @var	bool
     */
    private $isGod;

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
        $langWidth = (80 / count(array_unique(array_merge($this->filter['language'], array('en')))));
        $translations = $this->getTranslations();

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
            'act' => &$this->dgActions
        );

        // loop the datagrids (as references)
        foreach ($dataGrids as $type => &$dataGrid) {
            // set sorting
            /** @var $dataGrid BackendDataGridArray */
            $dataGrid->setSortingColumns(array('module', 'name'), 'name');

            // disable paging
            $dataGrid->setPaging(false);

            // set header label for reference code
            $dataGrid->setHeaderLabels(array('name' => \SpoonFilter::ucfirst(BL::lbl('ReferenceCode'))));

            // hide translation_id column (only if only one language is selected because the key doesn't exist if more than 1 language is selected)
            if ($dataGrid->hasColumn('translation_id')) {
                $dataGrid->setColumnHidden('translation_id');
            }

            // hide translation_id column (only if only one language is selected because the key doesn't exist if more than 1 language is selected)
            if ($dataGrid->hasColumn('en')) {
                $dataGrid->setHeaderLabels(array(
                    'en' => \SpoonFilter::ucfirst(BL::lbl(strtoupper('en')))
                ));
            }

            // only 1 language selected?
            if (count($this->filter['language']) == 1) {
                // check if this action is allowed
                if (BackendAuthentication::isAllowedAction('Edit')) {
                    // add edit button
                    $dataGrid->addColumn(
                        'edit', null, BL::lbl('Edit'),
                        BackendModel::createURLForAction('Edit') . '&amp;id=[translation_id]' . $this->filterQuery
                    );
                }

                // check if this action is allowed
                if (BackendAuthentication::isAllowedAction('Add')) {
                    // add copy button
                    $dataGrid->addColumnAction(
                        'copy', null, BL::lbl('Copy'),
                        BackendModel::createURLForAction('Add') . '&amp;id=[translation_id]' . $this->filterQuery
                    );
                }
            }

            // set column attributes for each language
            foreach ($this->filter['language'] as $lang) {
                // add a class for the inline edit
                $dataGrid->setColumnAttributes($lang, array('class' => 'translationValue'));

                // add attributes, so the inline editing has all the needed data
                $dataGrid->setColumnAttributes(
                    $lang,
                    array(
                        'data-id' => '{language: \'' . $lang . '\', application: \'' .
                                     $this->filter['application'] .
                                     '\', module: \'[module]\',name: \'[name]\', type: \'' .
                                     $type . '\'}'
                    )
                );

                // escape the double quotes
                $dataGrid->setColumnFunction(array('SpoonFilter', 'htmlentities'), array('[' . $lang . ']', null, ENT_QUOTES), $lang, true);
                if ($type == 'act') {
                    $dataGrid->setColumnFunction('urldecode', array('[' . $lang . ']'), $lang, true);
                }

                // set header labels
                $dataGrid->setHeaderLabels(array($lang => \SpoonFilter::ucfirst(BL::lbl(strtoupper($lang)))));

                // set column attributes
                $dataGrid->setColumnAttributes($lang, array('style' => 'width: ' . $langWidth . '%'));
            }
        }
    }

    private function getTranslations()
    {
        // get fallback translations (just like in the frontend)
        $fallbackTranslations = BackendLocaleModel::getTranslations(
            $this->filter['application'],
            $this->filter['module'],
            $this->filter['type'],
            array_unique(array_merge($this->filter['language'], array('en'))),
            array($this->get('multisite')->getMainSiteId()),
            $this->filter['name'],
            $this->filter['value']
        );

        // get all the translations for the selected languages
        $translations = BackendLocaleModel::getTranslations(
            $this->filter['application'],
            $this->filter['module'],
            $this->filter['type'],
            $this->filter['language'],
            array($this->get('current_site')->getId()),
            $this->filter['name'],
            $this->filter['value']
        );

        // overwrite the fallbacktranslations with the translations for the current domain
        foreach ($fallbackTranslations as $type => $typeTranslations) {
            if (isset($translations[$type])) {
                foreach ($fallbackTranslations[$type] as $name => $value) {
                    if (isset($translations[$type][$name])) {
                        $fallbackTranslations[$type][$name] = $translations[$type][$name];
                    }
                }
            }
        }

        // set translations that are not in the fallback
        foreach ($translations as $type => $typeTranslations) {
            foreach ($translations[$type] as $name => $value) {
                if (!isset($fallbackTranslations[$type][$name])) {
                    $fallbackTranslations[$type][$name] = $translations[$type][$name];
                }
            }
        }

        // our fallback translations array is not really the fallback array anymore.
        return $fallbackTranslations;
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        if ($this->get('current_site')->isMainSite()) {
            $languages = BackendLocaleModel::getLanguagesForMultiCheckbox($this->isGod);
        } else {
            $languages = BackendMultisiteModel::getWorkingLanguagesForDropdown();
        }
        $this->frm = new BackendForm('filter', BackendModel::createURLForAction(), 'get');
        $this->frm->addDropdown('application', array('Backend' => 'Backend', 'Frontend' => 'Frontend'), $this->filter['application']);
        $this->frm->addText('name', $this->filter['name']);
        $this->frm->addText('value', $this->filter['value']);
        $this->frm->addMultiCheckbox(
            'language',
            $languages,
            $this->filter['language'],
            'noFocus'
        );
        $this->frm->addMultiCheckbox('type', BackendLocaleModel::getTypesForMultiCheckbox(), $this->filter['type'], 'noFocus');
        $this->frm->addDropdown('module', BackendModel::getModulesForDropDown(false), $this->filter['module']);
        $this->frm->getField('module')->setDefaultElement(\SpoonFilter::ucfirst(BL::lbl('ChooseAModule')));

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
        $this->tpl->assign('dgLabels', ($this->dgLabels->getNumResults() != 0) ? $this->dgLabels->getContent() : false);
        $this->tpl->assign('dgMessages', ($this->dgMessages->getNumResults() != 0) ? $this->dgMessages->getContent() : false);
        $this->tpl->assign('dgErrors', ($this->dgErrors->getNumResults() != 0) ? $this->dgErrors->getContent() : false);
        $this->tpl->assign('dgActions', ($this->dgActions->getNumResults() != 0) ? $this->dgActions->getContent() : false);

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
        $this->filter['application'] = $this->getParameter('application') == null ? 'Frontend' : $this->getParameter('application');
        $this->filter['module'] = $this->getParameter('module', 'string', null);
        $this->filter['type'] = $this->getParameter('type', 'array');
        $this->filter['language'] = $this->getParameter('language', 'array');
        $this->filter['name'] = $this->getParameter('name') == null ? '' : $this->getParameter('name');
        $this->filter['value'] = $this->getParameter('value') == null ? '' : $this->getParameter('value');

        // build query for filter
        $this->filterQuery = BackendLocaleModel::buildURLQueryByFilter($this->filter);
    }
}
