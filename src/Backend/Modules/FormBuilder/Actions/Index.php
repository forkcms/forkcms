<?php

namespace Backend\Modules\FormBuilder\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\DataGridDatabase as BackendDataGridDatabase;
use Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;

/**
 * This is the index-action (default), it will display the overview
 */
class Index extends BackendBaseActionIndex
{
    public function execute(): void
    {
        parent::execute();
        $this->loadDataGrid();
        $this->parse();
        $this->display();
    }

    private function loadDataGrid(): void
    {
        $this->dataGrid = new BackendDataGridDatabase(
            BackendFormBuilderModel::QUERY_BROWSE,
            [BL::getWorkingLanguage()]
        );
        $this->dataGrid->setHeaderLabels([
            'email' => \SpoonFilter::ucfirst(BL::getLabel('Recipient')),
            'sent_forms' => '',
        ]);

        $this->dataGrid->setColumnFunction('htmlspecialchars', ['[name]'], 'name', false);
        $this->dataGrid->setSortingColumns(['name', 'email', 'method', 'sent_forms'], 'name');
        $this->dataGrid->setColumnFunction(
            [new BackendFormBuilderModel(), 'formatRecipients'],
            ['[email]'],
            'email'
        );
        $this->dataGrid->setColumnFunction(
            [__CLASS__, 'parseNumForms'],
            ['[id]', '[sent_forms]', '[method]'],
            'sent_forms'
        );
        $this->dataGrid->setColumnFunction(
            [new BackendFormBuilderModel(), 'getLocale'],
            ['Method_[method]'],
            'method'
        );

        // check if edit action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            $this->dataGrid->setColumnURL(
                'name',
                BackendModel::createUrlForAction('Edit') . '&amp;id=[id]'
            );
            $this->dataGrid->addColumn(
                'edit',
                null,
                BL::getLabel('Edit'),
                BackendModel::createUrlForAction('Edit') . '&amp;id=[id]',
                BL::getLabel('Edit')
            );
        }
    }

    protected function parse(): void
    {
        parent::parse();

        // add datagrid
        $this->template->assign('dataGrid', (string) $this->dataGrid->getContent());
    }

    /**
     * Parse amount of forms sent for the datagrid
     *
     * @param int $formId Id of the form.
     * @param int $sentForms Amount of sent forms.
     * @param string $method The way the data is handled.
     *
     * @return string
     */
    public static function parseNumForms(int $formId, int $sentForms, string $method): string
    {
        if ($method === 'email') {
            return '';
        }

        // one form sent
        if ($sentForms === 1) {
            $output = BL::getMessage('OneSentForm');
        } elseif ($sentForms > 1) {
            // multiple forms sent
            $output = sprintf(BL::getMessage('SentForms'), $sentForms);
        } else {
            // no forms sent
            $output = sprintf(BL::getMessage('SentForms'), $sentForms);
        }

        // check if data action is allowed
        if (BackendAuthentication::isAllowedAction('Data', 'FormBuilder')) {
            // output
            $output = '<a href="' . BackendModel::createUrlForAction('Data') .
                      '&amp;id=' . $formId . '" title="' . $output . '">' . $output . '</a>';
        }

        return $output;
    }
}
