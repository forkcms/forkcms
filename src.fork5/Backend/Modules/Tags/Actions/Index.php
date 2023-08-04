<?php

namespace Backend\Modules\Tags\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridDatabase as BackendDataGridDatabase;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/**
 * This is the index-action, it will display the overview of tags
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
        // create datagrid
        $this->dataGrid = new BackendDataGridDatabase(
            BackendTagsModel::QUERY_DATAGRID_BROWSE,
            [BL::getWorkingLanguage()]
        );
        $this->dataGrid->setColumnFunction('htmlspecialchars', ['[tag]'], 'tag', false);

        // header labels
        $this->dataGrid->setHeaderLabels([
            'tag' => \SpoonFilter::ucfirst(BL::lbl('Name')),
            'num_tags' => \SpoonFilter::ucfirst(BL::lbl('Amount')),
        ]);

        // sorting columns
        $this->dataGrid->setSortingColumns(['tag', 'num_tags'], 'num_tags');
        $this->dataGrid->setSortParameter('desc');

        // add the multicheckbox column
        $this->dataGrid->setMassActionCheckboxes('check', '[id]');

        // add mass action dropdown
        $ddmMassAction = new \SpoonFormDropdown(
            'action',
            ['delete' => BL::lbl('Delete')],
            'delete',
            false,
            'form-control form-control-sm',
            'form-control form-control-sm danger'
        );
        $ddmMassAction->setOptionAttributes('delete', [
            'data-target' => '#confirmDelete',
        ]);
        $this->dataGrid->setMassAction($ddmMassAction);

        // add attributes, so the inline editing has all the needed data
        $this->dataGrid->setColumnAttributes('tag', ['data-id' => '{\'id\':[id]}']);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            $link = BackendModel::createUrlForAction('Edit') . '&amp;id=[id]';
            $this->dataGrid->setColumnURL('tag', $link);
            $this->dataGrid->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                $link,
                BL::lbl('Edit')
            );
        }
    }

    protected function parse(): void
    {
        parent::parse();

        $this->template->assign('dataGrid', $this->dataGrid->getContent());
    }
}
