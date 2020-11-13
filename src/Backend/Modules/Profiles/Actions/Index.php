<?php

namespace Backend\Modules\Profiles\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\DataGridDatabase as BackendDataGridDatabase;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Profiles\Engine\Model as BackendProfilesModel;

/**
 * This is the index-action, it will display the overview of profiles.
 */
class Index extends BackendBaseActionIndex
{
    /**
     * Filter variables.
     *
     * @var array
     */
    private $filter;

    /**
     * Form.
     *
     * @var BackendForm
     */
    private $form;

    /**
     * @var BackendDataGridDatabase
     */
    private $dgProfiles;

    /**
     * Builds the query for this datagrid.
     *
     * @return array        An array with two arguments containing the query and its parameters.
     */
    private function buildQuery(): array
    {
        // init var
        $parameters = [];

        // construct the query in the controller instead of the model as an allowed exception for data grid usage
        $query = 'SELECT p.id, p.email, p.displayName, p.status,
                  UNIX_TIMESTAMP(p.registeredOn) AS registeredOn FROM ProfilesProfile AS p';
        $where = [];

        // add status
        if (isset($this->filter['status']) && $this->filter['status']) {
            $where[] = 'p.status = ?';
            $parameters[] = $this->filter['status'];
        }

        // add email
        if (isset($this->filter['email']) && $this->filter['email']) {
            $where[] = 'p.email LIKE ?';
            $parameters[] = '%' . $this->filter['email'] . '%';
        }

        // add group
        if (isset($this->filter['group']) && $this->filter['group']) {
            $query .= ' INNER JOIN ProfilesGroupRight AS pgr ON pgr.profile_id = p.id AND
                        (pgr.expiresOn IS NULL OR pgr.expiresOn > NOW())';
            $where[] = 'pgr.group_id = ?';
            $parameters[] = $this->filter['group'];
        }

        // query
        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        // group by profile (might have doubles because of the join on groups_rights)
        $query .= ' GROUP BY p.id';

        // query with matching parameters
        return [$query, $parameters];
    }

    public function execute(): void
    {
        parent::execute();
        $this->setFilter();
        $this->loadForm();
        $this->loadDataGrid();
        $this->parse();
        $this->display();
    }

    private function loadDataGrid(): void
    {
        // fetch query and parameters
        list($query, $parameters) = $this->buildQuery();

        // create datagrid
        $this->dgProfiles = new BackendDataGridDatabase($query, $parameters);

        // overrule default URL
        $this->dgProfiles->setURL(
            BackendModel::createUrlForAction(
                null,
                null,
                null,
                [
                    'offset' => '[offset]',
                    'order' => '[order]',
                    'sort' => '[sort]',
                    'email' => $this->filter['email'],
                    'status' => $this->filter['status'],
                    'group' => $this->filter['group'],
                ],
                false
            )
        );

        // sorting columns
        $this->dgProfiles->setSortingColumns(['email', 'displayName', 'status', 'registeredOn'], 'email');

        // set column function
        $this->dgProfiles->setColumnFunction(
            [new BackendDataGridFunctions(), 'getLongDate'],
            ['[registeredOn]'],
            'registeredOn',
            true
        );
        $this->dgProfiles->setColumnFunction('htmlspecialchars', ['[display_name]'], 'display_name', false);

        // add the mass action controls
        $this->dgProfiles->setMassActionCheckboxes('check', '[id]');
        $ddmMassAction = new \SpoonFormDropdown(
            'action',
            [
                'addToGroup' => BL::getLabel('AddToGroup'),
                'delete' => BL::getLabel('Delete'),
            ],
            'addToGroup',
            false,
            'form-control form-control-sm',
            'form-control form-control-sm danger'
        );
        $ddmMassAction->setAttribute('id', 'massAction');
        $ddmMassAction->setOptionAttributes('addToGroup', [
            'data-target' => '#confirmAddToGroup',
        ]);
        $ddmMassAction->setOptionAttributes('delete', [
            'data-target' => '#confirmDelete',
        ]);
        $this->dgProfiles->setMassAction($ddmMassAction);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            // set column URLs
            $this->dgProfiles->setColumnURL('email', BackendModel::createUrlForAction('Edit') . '&amp;id=[id]');

            // add columns
            $this->dgProfiles->addColumn(
                'edit',
                null,
                BL::getLabel('Edit'),
                BackendModel::createUrlForAction('Edit', null, null, null) . '&amp;id=[id]',
                BL::getLabel('Edit')
            );
        }
    }

    private function loadForm(): void
    {
        // create form
        $this->form = new BackendForm('filter', BackendModel::createUrlForAction(), 'get');

        // values for dropdowns
        $status = BackendProfilesModel::getStatusForDropDown();
        $groups = BackendProfilesModel::getGroups();

        // add fields
        $this->form->addText('email', $this->filter['email']);
        $this->form->addDropdown('status', $status, $this->filter['status']);
        $this->form->getField('status')->setDefaultElement('');

        // add a group filter if wa have groups
        if (!empty($groups)) {
            $this->form->addDropdown('group', $groups, $this->filter['group']);
            $this->form->getField('group')->setDefaultElement('');
        }

        // manually parse fields
        $this->form->parse($this->template);
    }

    protected function parse(): void
    {
        parent::parse();

        // parse data grid
        $this->template->assign(
            'dgProfiles',
            ($this->dgProfiles->getNumResults() != 0) ? $this->dgProfiles->getContent() : false
        );

        // parse paging & sorting
        $this->template->assign('offset', (int) $this->dgProfiles->getOffset());
        $this->template->assign('order', (string) $this->dgProfiles->getOrder());
        $this->template->assign('sort', (string) $this->dgProfiles->getSort());

        // parse filter
        $this->template->assignArray($this->filter);
    }

    /**
     * Sets the filter based on the $_GET array.
     */
    private function setFilter(): void
    {
        $this->filter['email'] = $this->getRequest()->query->get('email');
        $this->filter['status'] = $this->getRequest()->query->get('status');
        $this->filter['group'] = $this->getRequest()->query->get('group');
    }
}
