<?php

namespace Backend\Core\Engine;

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
