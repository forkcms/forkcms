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
 * A datagrid with a Doctrine-connection as source
 *
 * @author Wouter Sioen <wouter@woutersioen.be>
 */
class DataGridDoctrine extends DataGrid
{
    /**
     * @param string $repository        The repository to fetch data from.
     * @param array  $parameters        The parameters to be used
     * @param array  $columns           The columns to fetch
     */
    public function __construct($repository, $parameters = array(), $columns = array())
    {
        // create a new source-object
        $source = new DataGridSourceDoctrine(
            BackendModel::get('doctrine.orm.entity_manager'),
            $repository,
            $parameters,
            $columns
        );

        parent::__construct($source);
    }
}
