<?php

namespace Backend\Modules\Profiles\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridDatabase as BackendDataGridDatabase;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

/**
 * This is the groups-action, it will display the overview of profile groups.
 */
class Groups extends BackendBaseActionIndex
{
    /**
     * Filter variables.
     *
     * @var array
     */
    private $filter;

    /**
     * Form.
     *
     * @var BackendForm
     */
    private $form;

    /**
     * Groups data grid.
     *
     * @var BackendDataGridDatabase
     */
    private $dgGroups;

    /**
     * Builds the query for this datagrid.
     *
     * @return array An array with two arguments containing the query and its parameters.
     */
    private function buildQuery(): array
    {
        $parameters = [];

        /*
         * Start query, as you can see this query is build in the wrong place, because of the
         * filter it is a special case wherein we allow the query to be in the actionfile itself
         */
        $query =
            'SELECT pg.id, pg.name, COUNT(gr.id) AS members_count
             FROM profiles_groups AS pg
             LEFT OUTER JOIN profiles_groups_rights AS gr ON gr.group_id = pg.id AND
                (gr.expires_on IS NULL OR gr.expires_on > NOW())
             GROUP BY pg.id
             HAVING 1';

        // add name
        if ($this->filter['name'] !== null) {
            $query .= ' AND pg.name LIKE ?';
            $parameters[] = '%' . $this->filter['name'] . '%';
        }

        // query
        return [$query, $parameters];
    }

    public function execute(): void
    {
        parent::execute();
        $this->setFilter();
        $this->loadForm();
        $this->loadDataGrid();
        $this->parse();
        $this->display();
    }

    private function loadDataGrid(): void
    {
        // fetch query and parameters
        list($query, $parameters) = $this->buildQuery();

        // create datagrid
        $this->dgGroups = new BackendDataGridDatabase($query, $parameters);

        // overrule default URL
        $this->dgGroups->setURL(
            BackendModel::createUrlForAction(
                null,
                null,
                null,
                [
                     'offset' => '[offset]',
                     'order' => '[order]',
                     'sort' => '[sort]',
                     'name' => $this->filter['name'],
                ],
                false
            )
        );

        // sorting columns
        $this->dgGroups->setSortingColumns(['name', 'members_count'], 'name');

        // set the amount of profiles
        $this->dgGroups->setColumnFunction(
            [__CLASS__, 'parseNumProfilesInDataGrid'],
            ['[id]', '[members_count]'],
            'members_count'
        );

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Index')) {
            $this->dgGroups->setColumnURL(
                'members_count',
                BackendModel::createUrlForAction('Index') . '&amp;group=[id]'
            );
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('EditGroup')) {
            $this->dgGroups->setColumnURL('name', BackendModel::createUrlForAction('EditGroup') . '&amp;id=[id]');
            $this->dgGroups->addColumn(
                'edit',
                null,
                BL::getLabel('Edit'),
                BackendModel::createUrlForAction('EditGroup') . '&amp;id=[id]'
            );
        }
    }

    private function loadForm(): void
    {
        // create form
        $this->form = new BackendForm('filter', BackendModel::createUrlForAction(), 'get');

        // add fields
        $this->form->addText('name', $this->filter['name']);

        // manually parse fields
        $this->form->parse($this->template);
    }

    protected function parse(): void
    {
        parent::parse();

        // parse datagrid
        $this->template->assign('dgGroups', ($this->dgGroups->getNumResults() != 0) ? $this->dgGroups->getContent() : false);

        // parse paging & sorting
        $this->template->assign('offset', (int) $this->dgGroups->getOffset());
        $this->template->assign('order', (string) $this->dgGroups->getOrder());
        $this->template->assign('sort', (string) $this->dgGroups->getSort());

        // parse filter
        $this->template->assignArray($this->filter);
    }

    public static function parseNumProfilesInDataGrid(int $groupId, int $numProfiles): string
    {
        // 1 item
        if ($numProfiles == 1) {
            $output = '1 ' . BL::getLabel('Profile');
        } else {
            // no items
            $output = $numProfiles . ' ' . BL::getLabel('Profiles');
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            // complete output
            $output = '<a href="' .
                      BackendModel::createUrlForAction(
                          'Index'
                      ) . '&amp;group=' . $groupId . '" title="' . $output . '">' . $output . '</a>';
        }

        return $output;
    }

    /**
     * Sets the filter based on the $_GET array.
     */
    private function setFilter(): void
    {
        $this->filter['name'] = $this->getRequest()->query->get('name');
    }
}
