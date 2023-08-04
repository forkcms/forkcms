<?php

namespace ForkCMS\Utility\PhpSpreadsheet\Reader\Filter;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ColumnsFilter implements IReadFilter
{
    /**
     * @var array
     */
    private $columns;

    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    public function readCell($column, $row, $worksheetName = ''): bool
    {
        if (in_array($column, $this->columns)) {
            return true;
        }

        return false;
    }
}
