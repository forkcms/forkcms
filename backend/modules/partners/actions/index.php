<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the overview of partners
 *
 * @author Jelmer Prins <jelmer@sumocoders.be>
 */
class BackendPartnersIndex extends BackendBaseActionIndex
{
    /**
     * datagrid with partners
     *
     * @var    SpoonDataGrid
     */
    private $dgWidgets;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->dgWidgets = $this->loadDataGrid();

        $this->parse();
        $this->display();
    }

    /**
     * Loads the datagrid with the post
     * @return BackendDataGridDB
     */
    private function loadDataGrid()
    {
        // create datagrid
        $dg = new BackendDataGridDB(BackendPartnersModel::QRY_DATAGRID_BROWSE_SLIDERS);

        // sorting columns
        $dg->setSortingColumns(array('name'), 'name');
        $dg->setSortParameter('asc');

        // set colum URLs
        $dg->setColumnURL('name', BackendModel::createURLForAction('widget') . '&amp;id=[id]');

        // add edit column
        $dg->addColumn(
            'edit',
            null,
            BL::lbl('Edit'),
            BackendModel::createURLForAction('edit_widget') . '&amp;id=[id]',
            BL::lbl('Edit')
        );

        return $dg;
    }

    /**
     * Parse datagrid
     */
    protected function parse()
    {
        // parse the datagrid for all sliders
        if ($this->dgWidgets->getNumResults() != 0) {
            $this->tpl->assign('dgWidgets', $this->dgWidgets->getContent());
        }
        if ($this->dgWidgets->getNumResults() == 0) {
            $this->tpl->assign('noItems', true);
        }
    }
}
