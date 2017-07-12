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
 * A datagrid with a database-connection as source
 */
class DataGridDatabase extends DataGrid
{
    /**
     * @param string $query The query to retrieve the data.
     * @param array $parameters The parameters to be used inside the query.
     * @param string $resultsQuery The optional count query, used to calculate the number of results.
     * @param array $resultsParameters The parameters to be used inside the results query.
     */
    public function __construct(
        string $query,
        array $parameters = [],
        string $resultsQuery = null,
        array $resultsParameters = []
    ) {
        parent::__construct(
            new \SpoonDatagridSourceDB(
                BackendModel::get('database'),
                [$query, $parameters],
                $resultsQuery === null ? null : [$resultsQuery, $resultsParameters]
            )
        );
    }
}
