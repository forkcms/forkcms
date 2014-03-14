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
class BackendPartnerModuleIndex extends BackendBaseActionIndex
{
    /**
     * datagrid with partners
     *
     * @var    SpoonDataGrid
     */
    private $dgPartners;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->dgPartners = $this->loadDataGrid();

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
        $dg = new BackendDataGridDB(BackendPartnerModuleModel::QRY_DATAGRID_BROWSE);

        // set headers
        $dg->setHeaderLabels(array('created_by' => ucfirst(BL::lbl('Author'))));
        $dg->setHeaderLabels(array('url' => ucfirst(BL::lbl('website'))));
        $dg->setHeaderLabels(array('img' => ucfirst(BL::lbl('image'))));

        // sorting columns
        $dg->setSortingColumns(array('name', 'created_by', 'created_on', 'edited_on'), 'name');
        $dg->setSortParameter('asc');

        // set colum URLs
        $dg->setColumnURL('name', BackendModel::createURLForAction('edit') . '&amp;id=[id]');

        // set column functions
        $dg->setColumnFunction(array('BackendDatagridFunctions', 'getUser'), array('[created_by]'), 'created_by', true);
        $dg->setColumnFunction(array('BackendDataGridFunctions', 'showImage'), array(FRONTEND_FILES_URL . '/' . FrontendPartnerModuleModel::THUMBNAIL_PATH, '[img]'), 'img', true);

        // add edit column
        $dg->addColumn(
            'edit',
            null,
            BL::lbl('Edit'),
            BackendModel::createURLForAction('edit') . '&amp;id=[id]',
            BL::lbl('Edit')
        );

        return $dg;
    }

    /**
     * Parse datagrid
     */
    protected function parse()
    {
        // parse the datagrid for all blogposts
        if ($this->dgPartners->getNumResults() != 0) {
            $this->tpl->assign('dgPartners', $this->dgPartners->getContent());
        }
        if ($this->dgPartners->getNumResults() == 0) {
            $this->tpl->assign('noItems', 1);
        }
    }
}
