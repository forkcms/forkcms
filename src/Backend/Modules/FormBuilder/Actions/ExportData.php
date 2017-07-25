<?php

namespace Backend\Modules\FormBuilder\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Csv as BackendCSV;
use Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;

/**
 * This action is used to export submissions of a form.
 */
class ExportData extends BackendBaseAction
{
    /**
     * CSV column headers.
     *
     * @var array
     */
    private $columnHeaders = [];

    /**
     * The filter.
     *
     * @var array
     */
    private $filter;

    /**
     * Form id.
     *
     * @var int
     */
    private $id;

    /**
     * CSV rows.
     *
     * @var array
     */
    private $rows = [];

    /**
     * Builds the query for this datagrid.
     *
     * @return array An array with two arguments containing the query and its parameters.
     */
    private function buildQuery(): array
    {
        // init var
        $parameters = [$this->id];

        /*
         * Start query, as you can see this query is build in the wrong place, because of the filter
         * it is a special case wherein we allow the query to be in the actionfile itself
         */
        $query =
            'SELECT i.*, UNIX_TIMESTAMP(i.sent_on) AS sent_on, d.*
             FROM forms_data AS i
             INNER JOIN forms_data_fields AS d ON i.id = d.data_id
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

        return [$query, $parameters];
    }

    public function execute(): void
    {
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exist
        if ($this->id !== 0 && BackendFormBuilderModel::exists($this->id)) {
            parent::execute();
            $this->setFilter();
            $this->setItems();
            BackendCSV::outputCSV(date('Ymd_His') . '.csv', $this->rows, $this->columnHeaders);
        } else {
            // no item found, redirect to index, because somebody is fucking with our url
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
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

    /**
     * Fetch data for this form from the database and reformat to csv rows.
     */
    private function setItems(): void
    {
        // init header labels
        $lblSessionId = \SpoonFilter::ucfirst(BL::lbl('SessionId'));
        $lblSentOn = \SpoonFilter::ucfirst(BL::lbl('SentOn'));
        $this->columnHeaders = [$lblSessionId, $lblSentOn];

        // fetch query and parameters
        list($query, $parameters) = $this->buildQuery();

        // get the data
        $records = (array) $this->get('database')->getRecords($query, $parameters);
        $data = [];

        // reformat data
        foreach ($records as $row) {
            // first row of a submission
            if (!isset($data[$row['data_id']])) {
                $data[$row['data_id']][$lblSessionId] = $row['session_id'];
                $data[$row['data_id']][$lblSentOn] = \SpoonDate::getDate(
                    'Y-m-d H:i:s',
                    $row['sent_on'],
                    BL::getWorkingLanguage()
                );
            }

            // value is serialized
            $value = unserialize($row['value']);

            // flatten arrays
            if (is_array($value)) {
                $value = implode(', ', $value);
            }

            // group submissions
            $data[$row['data_id']][$row['label']] = \SpoonFilter::htmlentitiesDecode($value, null, ENT_QUOTES);

            // add into headers if not yet added
            if (!in_array($row['label'], $this->columnHeaders)) {
                $this->columnHeaders[] = $row['label'];
            }
        }

        // reorder data so they are in the correct column
        foreach ($data as $id => $row) {
            foreach ($this->columnHeaders as $header) {
                // submission has this field so add it
                if (isset($row[$header])) {
                    $this->rows[$id][] = $row[$header];
                } else {
                    // submission does not have this field so add a placeholder
                    $this->rows[$id][] = '';
                }
            }
        }

        // remove the keys
        $this->rows = array_values($this->rows);
    }
}
