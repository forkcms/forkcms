<?php

namespace Backend\Modules\Locale\Actions;

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
    protected $form;

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

    public function execute(): void
    {
        parent::execute();
        $this->isGod = BackendAuthentication::getUser()->isGod();
        $this->setFilter();
        $this->loadForm();
        $this->loadDataGrid();
        $this->parse();
        $this->display();
    }

    private function loadDataGrid(): void
    {
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
        $this->dgLabels = new BackendDataGridArray($translations['lbl'] ?? []);
        $this->dgMessages = new BackendDataGridArray($translations['msg'] ?? []);
        $this->dgErrors = new BackendDataGridArray($translations['err'] ?? []);
        $this->dgActions = new BackendDataGridArray($translations['act'] ?? []);

        // put the datagrids (references) in an array so we can loop them
        $dataGrids = [
            'lbl' => &$this->dgLabels,
            'msg' => &$this->dgMessages,
            'err' => &$this->dgErrors,
            'act' => &$this->dgActions,
        ];

        // loop the datagrids (as references)
        foreach ($dataGrids as $type => &$dataGrid) {
            /** @var $dataGrid BackendDataGridArray */
            $dataGrid->setSortingColumns(['module', 'name', 'application'], 'name');

            // disable paging
            $dataGrid->setPaging(false);

            // set header label for reference code
            $dataGrid->setHeaderLabels(['name' => \SpoonFilter::ucfirst(BL::lbl('ReferenceCode'))]);

            // hide the application when only one application is shown
            if ($this->filter['application'] != '') {
                $dataGrid->setColumnHidden('application');
            }

            // hide edite_on
            $dataGrid->setColumnHidden('edited_on');

            // set column attributes for each language
            foreach ($this->filter['language'] as $lang) {
                // add a class for the inline edit
                $dataGrid->setColumnAttributes($lang, ['class' => 'translationValue']);

                // add attributes, so the inline editing has all the needed data
                $dataGrid->setColumnAttributes(
                    $lang,
                    [
                        'data-id' => '{\'language\': \'' .
                            $lang . '\',\'application\': \'[application]\',\'module\': \'[module]\',\'name\': \'[name]\',\'type\': \'' .
                            $type . '\'}',
                    ]
                );

                // escape the double quotes
                $dataGrid->setColumnFunction(
                    ['SpoonFilter', 'htmlentities'],
                    ['[' . $lang . ']', null, ENT_QUOTES],
                    $lang,
                    true
                );
                if ($type == 'act') {
                    $dataGrid->setColumnFunction('urldecode', ['[' . $lang . ']'], $lang, true);
                }

                // set header labels
                $dataGrid->setHeaderLabels([$lang => \SpoonFilter::ucfirst(BL::lbl(mb_strtoupper($lang)))]);

                // only 1 language selected?
                if (count($this->filter['language']) == 1) {
                    $dataGrid->setColumnAttributes($lang, ['style' => 'width: ' . $langWidth . '%']);

                    // add id of translation for the export
                    $dataGrid->setColumnAttributes($lang, ['data-numeric-id' => '[translation_id]']);

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
                            BackendModel::createUrlForAction('Add') . '&amp;id=[translation_id]' . $this->filterQuery
                        );
                    }

                    // check if this action is allowed
                    if (BackendAuthentication::isAllowedAction('Edit')) {
                        // add edit button
                        $dataGrid->addColumn(
                            'edit',
                            null,
                            BL::lbl('Edit'),
                            BackendModel::createUrlForAction('Edit') . '&amp;id=[translation_id]' . $this->filterQuery
                        );
                    }
                } else {
                    // add id of translation for the export
                    $dataGrid->setColumnAttributes($lang, ['data-numeric-id' => '[translation_id_' . $lang .']']);
                    $dataGrid->setColumnHidden('translation_id_' . $lang);

                    //ugly fix but the browser does funny things with the percentage when showing lots of languages
                    $dataGrid->setColumnAttributes(
                        $lang,
                        [
                            'style' => 'width: ' .
                                $langWidth .
                                '%; max-width: '. (600 / count($this->filter['language'])) .'px;',
                        ]
                    );
                }
            }
        }
    }

    private function loadForm(): void
    {
        $this->form = new BackendForm('filter', BackendModel::createUrlForAction(), 'get');
        $this->form->addDropdown(
            'application',
            [
                '' => '-',
                'Backend' => 'Backend',
                'Frontend' => 'Frontend',
            ],
            $this->filter['application']
        );
        $this->form->addText('name', $this->filter['name']);
        $this->form->addText('value', $this->filter['value']);
        $this->form->addMultiCheckbox(
            'language',
            BackendLocaleModel::getLanguagesForMultiCheckbox($this->isGod),
            $this->filter['language'],
            'form-check-input noFocus'
        );
        $this->form->addMultiCheckbox(
            'type',
            BackendLocaleModel::getTypesForMultiCheckbox(),
            $this->filter['type'],
            'form-check-input noFocus'
        );
        $this->form->addDropdown(
            'module',
            BackendModel::getModulesForDropDown(),
            $this->filter['module']
        );
        $this->form->getField('module')->setDefaultElement('-');

        // manually parse fields
        $this->form->parse($this->template);
    }

    protected function parse(): void
    {
        parent::parse();

        // parse datagrids
        $this->template->assign(
            'dgLabels',
            ($this->dgLabels->getNumResults() != 0) ? $this->dgLabels->getContent() : false
        );
        $this->template->assign(
            'dgMessages',
            ($this->dgMessages->getNumResults() != 0) ? $this->dgMessages->getContent() : false
        );
        $this->template->assign(
            'dgErrors',
            ($this->dgErrors->getNumResults() != 0) ? $this->dgErrors->getContent() : false
        );
        $this->template->assign(
            'dgActions',
            ($this->dgActions->getNumResults() != 0) ? $this->dgActions->getContent() : false
        );

        // is filtered?
        if ($this->getRequest()->query->get('form') === 'filter') {
            $this->template->assign('filter', true);
        }

        // parse filter as query
        $this->template->assign('filter', $this->filterQuery);

        // parse isGod
        $this->template->assign('isGod', $this->isGod);

        // parse noItems, if all the datagrids are empty
        $this->template->assign(
            'noItems',
            $this->dgLabels->getNumResults() == 0 &&
            $this->dgMessages->getNumResults() == 0 &&
            $this->dgErrors->getNumResults() == 0 &&
            $this->dgActions->getNumResults() == 0
        );

        $this->template->assign(
            'hasSubmissions',
            $this->hasSubmissions
        );

        // parse the add URL
        $this->template->assign('addURL', BackendModel::createUrlForAction('Add', null, null, null) . $this->filterQuery);
    }

    /**
     * Sets the filter based on the $_GET array.
     */
    private function setFilter(): void
    {
        // set filter
        $this->filter['application'] = $this->getRequest()->query->get('application');
        $this->filter['module'] = $this->getRequest()->query->get('module');
        $this->filter['type'] = $this->getRequest()->query->get('type', '');
        if ($this->filter['type'] === '') {
            // if no type is selected, set labels as the selected type
            $_GET['type'] = ['lbl'];
            $this->filter['type'] = ['lbl'];
        }
        $this->filter['language'] = $this->getRequest()->query->get('language', []);
        if (empty($this->filter['language'])) {
            // if no language is selected, set the working language as the selected
            $_GET['language'] = [BL::getWorkingLanguage()];
            $this->filter['language'] = [BL::getWorkingLanguage()];
        }
        $this->filter['language'] = (array) $this->filter['language'];
        $this->filter['name'] = $this->getRequest()->query->get('name');
        $this->filter['value'] = $this->getRequest()->query->get('value');

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
        $this->filterQuery = BackendLocaleModel::buildUrlQueryByFilter($this->filter);
    }
}
