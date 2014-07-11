<?php

namespace Backend\Modules\Faq\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;

/**
 * This is the categories-action, it will display the overview of faq categories
 *
 * @author Lester Lievens <lester.lievens@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author SIESQO <info@siesqo.be>
 */
class Categories extends BackendBaseActionIndex
{
    /**
     * Deny the use of multiple categories
     *
     * @param bool
     */
    private $multipleCategoriesAllowed;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->loadDataGrid();

        $this->parse();
        $this->display();
    }

    /**
     * Loads the dataGrid
     */
    private function loadDataGrid()
    {
        // are multiple categories allowed?
        $this->multipleCategoriesAllowed = BackendModel::getModuleSetting('Faq', 'allow_multiple_categories', true);

        // create dataGrid
        $this->dataGrid = new BackendDataGridDB(
            BackendFaqModel::QRY_DATAGRID_BROWSE_CATEGORIES,
            BL::getWorkingLanguage()
        );
        $this->dataGrid->setHeaderLabels(array('num_items' => \SpoonFilter::ucfirst(BL::lbl('Amount'))));
        if ($this->multipleCategoriesAllowed) {
            $this->dataGrid->enableSequenceByDragAndDrop();
        } else {
            $this->dataGrid->setColumnsHidden(array('sequence'));
        }
        $this->dataGrid->setRowAttributes(array('id' => '[id]'));
        $this->dataGrid->setPaging(false);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Index')) {
            $this->dataGrid->setColumnFunction(
                array(__CLASS__, 'setClickableCount'),
                array('[num_items]', BackendModel::createURLForAction('Index') . '&amp;category=[id]'),
                'num_items',
                true
            );
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('EditCategory')) {
            $this->dataGrid->setColumnURL('title', BackendModel::createURLForAction('EditCategory') . '&amp;id=[id]');
            $this->dataGrid->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createURLForAction('EditCategory') . '&amp;id=[id]',
                BL::lbl('Edit')
            );
        }
    }

    /**
     * Parse & display the page
     */
    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('dataGrid', (string) $this->dataGrid->getContent());

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('AddCategory') && $this->multipleCategoriesAllowed) {
            $this->tpl->assign('showFaqAddCategory', true);
        } else {
            $this->tpl->assign('showFaqAddCategory', false);
        }
    }

    /**
     * Convert the count in a human readable one.
     *
     * @param int $count
     * @param string $link
     * @return string
     */
    public static function setClickableCount($count, $link)
    {
        // redefine
        $count = (int) $count;
        $link = (string) $link;

        // return link in case of more than one item, one item, other
        if ($count > 1) {
            return '<a href="' . $link . '">' . $count . ' ' . BL::getLabel('Questions') . '</a>';
        } elseif ($count == 1) {
            return '<a href="' . $link . '">' . $count . ' ' . BL::getLabel('Question') . '</a>';
        }

        return '';
    }
}
