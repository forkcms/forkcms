<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * A datagrid with an array as source
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class DataGridArray extends DataGrid
{
    /**
     * @param array $array The data.
     */
    public function __construct(array $array)
    {
        $source = new \SpoonDataGridSourceArray($array);
        parent::__construct($source);
    }
}
