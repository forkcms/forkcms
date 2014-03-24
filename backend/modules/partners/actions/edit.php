<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will load a form with the item data and save the changes.
 *
 * @author Jelmer <jelmer@sumocoders.be>
 */
class BackendPartnersEdit extends BackendBaseActionEdit
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
        $this->id = $this->getParameter('id', 'int');

        // does the item exists
        if ($this->id !== null && BackendPartnersModel::widgetExists($this->id)) {
            parent::execute();
            $this->getData();

            $this->loadForm();
            $this->validateForm();

            $this->dgPartners = $this->loadDataGrid();

            $this->parse();
            $this->display();
        } else {
            $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
        }
    }

    /**
     * Get the data
     */
    private function getData()
    {
        $this->record = (array) BackendPartnersModel::getWidget($this->id);

        // no item found, redirect to index
        if (empty($this->record)) {
            $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
        }
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('edit');
        $this->frm->addText('name', $this->record['name'], 255, 'inputText name', 'inputTextError name')->setAttribute(
            'required'
        );
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
        $dg->setColumnURL('name', BackendModel::createURLForAction('edit_partner') . '&amp;id=[id]');

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
            BackendModel::createURLForAction('edit_partner') . '&amp;id=[id]',
            BL::lbl('Edit')
        );
        $dg->enableSequenceByDragAndDrop();

        return $dg;
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        // assign this variable so it can be used in the template
        $this->tpl->assign('item', $this->record);

        // parse the datagrid for all blogposts
        if ($this->dgPartners->getNumResults() != 0) {
            $this->tpl->assign('dgPartners', $this->dgPartners->getContent());
        }
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // validation
            $this->frm->getField('name')->isFilled(BL::err('NameIsRequired'));

            // no errors?
            if ($this->frm->isCorrect()) {
                $item['id'] = $this->record['id'];
                $item['widget_id'] = $this->record['widget_id'];
                $item['name'] = $this->frm->getField('name')->getValue();

                BackendPartnersModel::updateWidget($item);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('index') . '&report=edited&var=' . urlencode(
                        $item['title']
                    ) . '&highlight=row-' . $item['id']
                );
            }
        }
    }
}
