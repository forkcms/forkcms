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
class Emails extends BackendBaseActionIndex
{
    public function execute(): void
    {
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exist
        if ($this->id !== 0 && BackendFormBuilderModel::exists($this->id)) {
            parent::execute();
            $this->getData();
            $this->loadDataGrid();
            $this->parse();
            $this->display();
        } else {
            // no item found, throw an exceptions, because somebody is fucking with our url
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
    }

    private function getData(): void
    {
        $this->record = BackendFormBuilderModel::get($this->id);
    }

    private function loadDataGrid(): void
    {
        $this->dataGrid = new BackendDataGridDatabase(
            BackendFormBuilderModel::QUERY_BROWSE_EMAILS,
            [BL::getWorkingLanguage(), $this->id]
        );
        $this->dataGrid->setHeaderLabels([
            'email_to_addresses' => \SpoonFilter::ucfirst(BL::getLabel('Recipient'))
        ]);
        $this->dataGrid->setColumnFunction(
            [new BackendFormBuilderModel(), 'formatRecipients'],
            ['[email_to_addresses]'],
            'email_to_addresses'
        );

        // check if edit action is allowed
        if (BackendAuthentication::isAllowedAction('EditEmail')) {
            $this->dataGrid->setColumnURL(
                'email_subject',
                BackendModel::createUrlForAction('EditEmail') . '&amp;id=[id]&amp;formId=' . $this->record['id']
            );
            $this->dataGrid->addColumn(
                'edit',
                null,
                BL::getLabel('Edit'),
                BackendModel::createUrlForAction('EditEmail') . '&amp;id=[id]&amp;formId=' . $this->record['id'],
                BL::getLabel('Edit')
            );
        }
    }

    protected function parse(): void
    {
        parent::parse();

        $this->template->assign('item', $this->record);

        // add datagrid
        $this->template->assign('dataGrid', (string) $this->dataGrid->getContent());

        // add form name to the breadcrumb
        $this->header->appendDetailToBreadcrumbs($this->record['name']);
    }
}
