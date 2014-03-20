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
class BackendPartnersWidget extends BackendBaseActionIndex
{
    /**
     * datagrid with partners
     *
     * @var    SpoonDataGrid
     */
    private $dgPartners;

    /**
     * current id
     *
     * @var    int
     */
    private $id;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->id = $this->getParameter('id', 'int');
        if (!BackendPartnersModel::widgetExists($this->id)) {
            $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
        }
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
        $dg = new BackendDataGridDB(BackendPartnersModel::QRY_DATAGRID_BROWSE_PARTNERS, $this->id);

        // set headers
        $dg->setHeaderLabels(array('url' => ucfirst(BL::lbl('URL'))));
        $dg->setHeaderLabels(array('img' => ucfirst(BL::lbl('image'))));

        // hide columns
        $dg->setColumnHidden('widget');

        // set colum URLs
        $dg->setColumnURL('name', BackendModel::createURLForAction('edit') . '&amp;id=[id]');

        // set column function
        $dg->setColumnFunction(
            array('BackendDataGridFunctions', 'showImage'),
            array(FRONTEND_FILES_URL . '/' . FrontendPartnersModel::IMAGE_PATH . '/[widget]/48x48', '[img]'),
            'img',
            true
        );

        // add edit column
        $dg->addColumn(
            'edit',
            null,
            BL::lbl('Edit'),
            BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;widget_id=' . $this->id,
            BL::lbl('Edit')
        );
        $dg->enableSequenceByDragAndDrop();

        return $dg;
    }

    /**
     * Parse datagrid
     */
    protected function parse()
    {
        $this->tpl->assign('widgetId', $this->id);
        // parse the datagrid for all blogposts
        if ($this->dgPartners->getNumResults() != 0) {
            $this->tpl->assign('dgPartners', $this->dgPartners->getContent());
        }
        if ($this->dgPartners->getNumResults() == 0) {
            $this->tpl->assign('noItems', true);
        }
    }
}
