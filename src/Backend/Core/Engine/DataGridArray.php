<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use SpoonDatagridSourceArray;

/**
 * A datagrid with an array as source
 */
class DataGridArray extends DataGrid
{
    public function __construct(array $data)
    {
        $source = new SpoonDatagridSourceArray($data);
        parent::__construct($source);
    }
}
