<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model as BackendModel;
use Doctrine\ORM\Query;
use SpoonDatagridSourceDB;

/**
 * A datagrid that show the result of a doctrine query
 */
class DataGridDoctrineQuery extends DataGrid
{
    /**
     * @param Query $query The query to retrieve the data.
     * @param Query $resultsQuery The optional count query, used to calculate the number of results.
     */
    public function __construct(Query $query, Query $resultsQuery = null)
    {
        // create a new source-object
        $source = new SpoonDatagridSourceDB(
            BackendModel::get('database'),
            [
                $this->getQueryString($query),
                $this->getQueryParameters($query),
            ],
            is_null($resultsQuery) ? null : [
                $this->getQueryString($resultsQuery),
                $this->getQueryParameters($resultsQuery),
            ]
        );

        parent::__construct($source);
    }

    /**
     * @param Query $query
     *
     * @return array
     */
    private function getQueryParameters(Query $query)
    {
        return array_map(
            function (Query\Parameter $parameter) {
                return (string) $parameter->getValue();
            },
            $query->getParameters()->toArray()
        );
    }

    /**
     * @param Query $query
     *
     * @return string
     */
    private function getQueryString(Query $query)
    {
        return preg_replace('#(AS\s)(.+?)_\d+(,|\sFROM)#', '$1$2$3', $query->getSQL());
    }
}
