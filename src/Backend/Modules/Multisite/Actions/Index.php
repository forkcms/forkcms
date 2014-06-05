<?php

namespace Backend\Modules\Multisite\Actions;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\DataGridDB;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Modules\Multisite\Engine\Model as MultisiteModel;

/**
 * The backend index action for the Multisite module.
 *
 * @author <per@wijs.be>
 */
class Index extends ActionIndex
{
    public function execute()
    {
        parent::execute();
        $this->loadDataGrids();
        $this->parse();
        $this->display();
    }

    private function loadDataGrids()
    {
        $this->dataGrid = new DataGridDB(
            MultisiteModel::QRY_ALL_SITES
        );

        if (Authentication::isAllowedAction('Edit')) {
            $this->dataGrid->setColumnURL(
                'domain',
                Model::createURLForAction('Edit') . '&amp;id=[id]'
            );
            $this->dataGrid->addColumn(
                'edit',
                null,
                Language::lbl('Edit'),
                Model::createURLForAction('Edit') . '&amp;id=[id]',
                Language::lbl('Edit')
            );
        }
    }

    /**
     * Assign the template variables.
     */
    protected function parse()
    {
        $this->tpl->assign('dataGrid', (string) $this->dataGrid->getContent());
    }
}
