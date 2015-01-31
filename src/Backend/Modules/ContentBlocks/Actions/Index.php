<?php

namespace Backend\Modules\ContentBlocks\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

/**
 * This is the index-action (default), it will display the overview
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Wouter Sioen <wouter@woutersioen.be>
 */
class Index extends BackendBaseActionIndex
{

    /**
     * Filter variables.
     *
     * @var    array
     */
    private $filter;

    /**
     * Form.
     *
     * @var BackendForm
     */
    private $frm;

    /**
     * Builds the query for this datagrid.
     *
     * @return array        An array with two arguments containing the query and its parameters.
     */
    private function buildQuery()
    {
        // init var
        $parameters = array('active', BL::getWorkingLanguage());

        // construct the query in the controller instead of the model as an allowed exception for data grid usage
        $query = 'SELECT i.id, i.title, i.hidden
                  FROM content_blocks AS i
                  WHERE i.status = ? AND i.language = ?';

        // add email
        if (isset($this->filter['title'])) {
            $query .= ' AND i.title LIKE ?';
            $parameters[] = '%' . $this->filter['title'] . '%';
        }

        // query with matching parameters
        return array($query, $parameters);
    }

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->loadDataGrid();
        if ($this->dataGrid->getNumResults() > $this->dataGrid->getPagingLimit())
        {
            $this->setFilter();
            $this->loadForm();
        }
        $this->parse();
        $this->display();
    }

    /**
     * Load the datagrids
     */
    private function loadDataGrid()
    {
        // fetch query and parameters
        list($query, $parameters) = $this->buildQuery();

        $this->dataGrid = new BackendDataGridDB($query, $parameters);

        $this->dataGrid->setSortingColumns(array('title'));

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            $this->dataGrid->setColumnURL(
                'title',
                BackendModel::createURLForAction('Edit') . '&amp;id=[id]'
            );
            $this->dataGrid->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createURLForAction('Edit') . '&amp;id=[id]',
                BL::lbl('Edit')
            );
        }
    }

    /**
     * Load the form.
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('filter', BackendModel::createURLForAction(), 'get');

        // add fields
        $this->frm->addText('title', $this->filter['title']);

        // manually parse fields
        $this->frm->parse($this->tpl);
    }

    /**
     * Parse the datagrid and the reports
     */
    protected function parse()
    {
        parent::parse();

        // parse data grid
        $this->tpl->assign('dataGrid', (string) $this->dataGrid->getContent());

        if ($this->dataGrid->getNumResults() > $this->dataGrid->getPagingLimit())
        {
            // parse filter
            $this->tpl->assign($this->filter);
        }
    }

    /**
     * Sets the filter based on the $_GET array.
     */
    private function setFilter()
    {
        $this->filter['title'] = $this->getParameter('title');
    }
}
