<?php

namespace Backend\Modules\FormBuilder\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;

/**
 * This is the data-action it will display the details of a sent data item
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
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
     * @var	array
     */
    private $filter;

    /**
     * @var int
     */
    private $id;

    /**
     * Execute the action
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendFormBuilderModel::existsData($this->id)) {
            parent::execute();
            $this->setFilter();
            $this->getData();
            $this->parse();
            $this->display();
        } else {
            // no item found, redirect with an error, because somebody is fucking with our url
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }

    /**
     * Get the data
     */
    private function getData()
    {
        $this->data = BackendFormBuilderModel::getData($this->id);
        $this->record = BackendFormBuilderModel::get($this->data['form_id']);
    }

    /**
     * Parse
     */
    protected function parse()
    {
        parent::parse();

        // form info
        $this->tpl->assign('name', $this->record['name']);
        $this->tpl->assign('formId', $this->record['id']);

        // sent info
        $this->tpl->assign('id', $this->data['id']);
        $this->tpl->assign('sentOn', $this->data['sent_on']);

        // init
        $data = array();

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
        $this->tpl->assign('data', $data);
        $this->tpl->assign('filter', $this->filter);
    }

    /**
     * Sets the filter based on the $_GET array.
     */
    private function setFilter()
    {
        // start date is set
        if (isset($_GET['start_date']) && $_GET['start_date'] != '') {
            // redefine
            $startDate = (string) $_GET['start_date'];

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
        if (isset($_GET['end_date']) && $_GET['end_date'] != '') {
            // redefine
            $endDate = (string) $_GET['end_date'];

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
