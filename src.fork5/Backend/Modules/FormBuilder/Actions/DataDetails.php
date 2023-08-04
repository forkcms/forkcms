<?php

namespace Backend\Modules\FormBuilder\Actions;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;

/**
 * This is the data-action it will display the details of a sent data item
 */
class DataDetails extends BackendBaseActionIndex
{
    /**
     * @var array
     */
    private $data;
    private $record;

    /**
     * Filter variables
     *
     * @var array
     */
    private $filter;

    /**
     * @var int
     */
    private $id;

    public function execute(): void
    {
        // get parameters
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exist
        if ($this->id !== 0 && BackendFormBuilderModel::existsData($this->id)) {
            parent::execute();
            $this->setFilter();
            $this->getData();
            $this->parse();
            $this->display();
        } else {
            // no item found, redirect with an error, because somebody is fucking with our url
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
    }

    private function getData(): void
    {
        $this->data = BackendFormBuilderModel::getData($this->id);
        $this->record = BackendFormBuilderModel::get($this->data['form_id']);
    }

    protected function parse(): void
    {
        parent::parse();

        // form info
        $this->template->assign('name', $this->record['name']);
        $this->template->assign('formId', $this->record['id']);

        // sent info
        $this->template->assign('id', $this->data['id']);
        $this->template->assign('sentOn', $this->data['sent_on']);

        // init
        $data = [];

        // prepare data
        foreach ($this->data['fields'] as $field) {
            // implode arrays
            if (is_array($field['value'])) {
                $field['value'] = implode(', ', $field['value']);
            } else {
                // new lines to line breaks
                $field['value'] = nl2br($field['value']);
            }

            // add to data
            $data[] = $field;
        }

        // assign
        $this->template->assign('data', $data);
        $this->template->assign('filter', $this->filter);

        $this->header->appendDetailToBreadcrumbs($this->record['name']);
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
            $endDate = $this->getRequest()->query->get('end_date', '');

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
