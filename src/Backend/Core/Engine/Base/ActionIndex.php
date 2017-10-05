<?php

namespace Backend\Core\Engine\Base;

use Backend\Core\Engine\DataGridDatabase;

/**
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the index action
 */
class ActionIndex extends Action
{
    /**
     * A datagrid instance
     *
     * @var DataGridDatabase
     */
    protected $dataGrid;
}
