<?php

namespace Backend\Modules\FormBuilder\Actions;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\DataGridDatabase as BackendDataGridDatabase;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;

/**
 * This is the data-action it will display the overview of sent data
 */
class Data extends BackendBaseActionIndex
{
    /**
     * Filter variables
     *
     * @var array
     */
    private $filter;

    /**
     * The form instance
     *
     * @var Form
     */
    protected $form;

    /**
     * Form id.
     *
     * @var int
     */
    private $id;

    /**
     * @var array
     */
    private $record;

    /**
     * Builds the query for this datagrid
     *
     * @return array An array with two arguments containing the query and its parameters.
     */
    private function buildQuery(): array
    {
        $parameters = [$this->id];

        // start query, as you can see this query is build in the wrong place,
        // because of the filter it is a special case
        // wherein we allow the query to be in the actionfile itself
        $query =
            'SELECT i.id, UNIX_TIMESTAMP(i.sent_on) AS sent_on
             FROM forms_data AS i
             WHERE i.form_id = ?';

        // add start date
        if ($this->filter['start_date'] !== '') {
            // explode date parts
            $chunks = explode('/', $this->filter['start_date']);

            // add condition
            $query .= ' AND i.sent_on >= ?';
            $parameters[] = BackendModel::getUTCDate(null, gmmktime(23, 59, 59, $chunks[1], $chunks[0], $chunks[2]));
        }

        // add end date
        if ($this->filter['end_date'] !== '') {
            // explode date parts
            $chunks = explode('/', $this->filter['end_date']);

            // add condition
            $query .= ' AND i.sent_on <= ?';
            $parameters[] = BackendModel::getUTCDate(null, gmmktime(23, 59, 59, $chunks[1], $chunks[0], $chunks[2]));
        }

        // new query
        return [$query, $parameters];
    }

    public function execute(): void
    {
        // get parameters
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exist
        if ($this->id !== 0 && BackendFormBuilderModel::exists($this->id)) {
            parent::execute();
            $this->setFilter();
            $this->loadForm();
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

        if ($this->record['method'] === 'email') {
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
    }

    private function loadDataGrid(): void
    {
        list($query, $parameters) = $this->buildQuery();

        // create datagrid
        $this->dataGrid = new BackendDataGridDatabase($query, $parameters);

        // overrule default URL
        $this->dataGrid->setURL(
            BackendModel::createUrlForAction(
                null,
                null,
                null,
                [
                    'offset' => '[offset]',
                    'order' => '[order]',
                    'sort' => '[sort]',
                    'start_date' => $this->filter['start_date'],
                    'end_date' => $this->filter['end_date'],
                ],
                false
            ) . '&amp;id=' . $this->id
        );

        // sorting columns
        $this->dataGrid->setSortingColumns(['sent_on'], 'sent_on');
        $this->dataGrid->setSortParameter('desc');

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('DataDetails')) {
            // set colum URLs
            $this->dataGrid->setColumnURL(
                'sent_on',
                BackendModel::createUrlForAction(
                    'DataDetails',
                    null,
                    null,
                    [
                        'start_date' => $this->filter['start_date'],
                        'end_date' => $this->filter['end_date'],
                    ],
                    false
                ) . '&amp;id=[id]'
            );

            // add edit column
            $this->dataGrid->addColumn(
                'details',
                null,
                BL::getLabel('Details'),
                BackendModel::createUrlForAction(
                    'DataDetails',
                    null,
                    null,
                    [
                        'start_date' => $this->filter['start_date'],
                        'end_date' => $this->filter['end_date'],
                    ]
                ) . '&amp;id=[id]',
                BL::getLabel('Details')
            );
        }

        // date
        $this->dataGrid->setColumnFunction(
            [new BackendFormBuilderModel(), 'calculateTimeAgo'],
            '[sent_on]',
            'sent_on',
            false
        );
        $this->dataGrid->setColumnFunction('ucfirst', '[sent_on]', 'sent_on', false);

        // add the multicheckbox column
        $this->dataGrid->setMassActionCheckboxes('check', '[id]');

        // mass action
        $ddmMassAction = new \SpoonFormDropdown('action', ['delete' => BL::getLabel('Delete')], 'delete');
        $ddmMassAction->setOptionAttributes('delete', ['data-target' => '#confirmDelete']);
        $this->dataGrid->setMassAction($ddmMassAction);
    }

    private function loadForm(): void
    {
        $startDate = '';
        $endDate = '';

        if (isset($this->filter['start_date']) && $this->filter['start_date'] != '') {
            $chunks = explode('/', $this->filter['start_date']);
            $startDate = (int) mktime(0, 0, 0, (int) $chunks[1], (int) $chunks[0], (int) $chunks[2]);
            if ($startDate == 0) {
                $startDate = '';
            }
        }

        if (isset($this->filter['end_date']) && $this->filter['end_date'] != '') {
            $chunks = explode('/', $this->filter['end_date']);
            $endDate = (int) mktime(0, 0, 0, (int) $chunks[1], (int) $chunks[0], (int) $chunks[2]);
            if ($endDate == 0) {
                $endDate = '';
            }
        }

        $this->form = new BackendForm('filter', BackendModel::createUrlForAction(), 'get');
        $this->form->addText('id', $this->id, 255, 'd-none');
        $this->form->addDate('start_date', $startDate);
        $this->form->addDate('end_date', $endDate);

        // manually parse fields
        $this->form->parse($this->template);
    }

    protected function parse(): void
    {
        parent::parse();

        // datagrid
        $this->template->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);

        // form info
        $this->template->assign('name', $this->record['name']);
        $this->template->assign('id', $this->record['id']);
        $this->template->assignArray($this->filter);
    }

    /**
     * Sets the filter based on the $_GET array.
     */
    private function setFilter(): void
    {
        // start date is set
        if ($this->getRequest()->query->has('start_date') && $this->getRequest()->query->get('start_date', '') !== '') {
            // redefine
            $startDate = $this->getRequest()->query->get('start_date', '');

            // explode date parts
            $chunks = explode('/', $startDate);

            // valid date
            if (count($chunks) == 3 && checkdate((int) $chunks[1], (int) $chunks[0], (int) $chunks[2])) {
                $this->filter['start_date'] = $startDate;
            } else {
                // invalid date
                $this->filter['start_date'] = '';
            }
        } else {
            // not set
            $this->filter['start_date'] = '';
        }

        // end date is set
        if ($this->getRequest()->query->has('end_date') && $this->getRequest()->query->get('end_date', '') !== '') {
            // redefine
            $endDate = $this->getRequest()->query->get('end_date');

            // explode date parts
            $chunks = explode('/', $endDate);

            // valid date
            if (count($chunks) == 3 && checkdate((int) $chunks[1], (int) $chunks[0], (int) $chunks[2])) {
                $this->filter['end_date'] = $endDate;
            } else {
                // invalid date
                $this->filter['end_date'] = '';
            }
        } else {
            // not set
            $this->filter['end_date'] = '';
        }
    }
}
