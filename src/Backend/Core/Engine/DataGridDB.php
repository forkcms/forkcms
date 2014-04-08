<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model as BackendModel;

/**
 * A datagrid with a DB-connection as source
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class DataGridDB extends DataGrid
{
    /**
     * @param string $query             The query to retrieve the data.
     * @param array  $parameters        The parameters to be used inside the query.
     * @param string $resultsQuery      The optional count query, used to calculate the number of results.
     * @param array  $resultsParameters The parameters to be used inside the results query.
     */
    public function __construct($query, $parameters = array(), $resultsQuery = null, $resultsParameters = array())
    {
        // results query?
        $results = ($resultsQuery !== null) ? array($resultsQuery, $resultsParameters) : null;

        // create a new source-object
        $source = new \SpoonDataGridSourceDB(BackendModel::get('database'), array(
            $query,
            (array) $parameters
        ), $results);

        parent::__construct($source);
    }
}
