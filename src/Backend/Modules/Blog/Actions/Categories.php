<?php

namespace Backend\Modules\Blog\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Modules\Blog\Engine\Model as BackendBlogModel;

/**
 * This is the categories-action, it will display the overview of blog categories
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Categories extends BackendBaseActionIndex
{
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
     * Loads the datagrids
     */
    private function loadDataGrid()
    {
        // create datagrid
        $this->dataGrid = new BackendDataGridDB(
            BackendBlogModel::QRY_DATAGRID_BROWSE_CATEGORIES,
            array('active', BL::getWorkingLanguage())
        );

        // set headers
        $this->dataGrid->setHeaderLabels(array(
            'num_items' => \SpoonFilter::ucfirst(BL::lbl('Amount'))
        ));

        // sorting columns
        $this->dataGrid->setSortingColumns(array('title', 'num_items'), 'title');

        // convert the count into a readable and clickable one
        $this->dataGrid->setColumnFunction(
            array(__CLASS__, 'setClickableCount'),
            array('[num_items]', BackendModel::createURLForAction('Index') . '&amp;category=[id]'),
            'num_items',
            true
        );

        // disable paging
        $this->dataGrid->setPaging(false);

        // add attributes, so the inline editing has all the needed data
        $this->dataGrid->setColumnAttributes('title', array('data-id' => '{id:[id]}'));

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('EditCategory')) {
            // set column URLs
            $this->dataGrid->setColumnURL(
                'title',
                BackendModel::createURLForAction('EditCategory') . '&amp;id=[id]'
            );

            // add column
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
    }

    /**
     * Convert the count in a human readable one.
     *
     * @param int $count The count.
     * @param string $link The link for the count.
     * @return string
     */
    public static function setClickableCount($count, $link)
    {
        $count = (int) $count;
        $link = (string) $link;
        $return = '';

        if ($count > 1) {
            $return = '<a href="' . $link . '">' . $count . ' ' . BL::getLabel('Articles') . '</a>';
        } elseif ($count == 1) {
            $return = '<a href="' . $link . '">' . $count . ' ' . BL::getLabel('Article') . '</a>';
        }

        return $return;
    }
}
