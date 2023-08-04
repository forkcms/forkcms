<?php

namespace ForkCMS\Utility\Csv;

use ForkCMS\Utility\PhpSpreadsheet\Reader\Filter\ChunkReadFilter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;

class Reader
{
    public function findColumnIndexes(string $path, array $columns): array
    {
        $reader = IOFactory::createReader('Csv');
        $reader->setReadDataOnly(true);
        $reader->setReadFilter(new ChunkReadFilter(1, 1));
        $spreadSheet = $reader->load($path);

        $indexes = array_fill_keys(array_values($columns), null);

        foreach ($spreadSheet->getSheet(0)->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                if (in_array($cell->getValue(), $columns)) {
                    $indexes[$cell->getValue()] = $cell->getColumn();
                }
            }
        }

        return $indexes;
    }

    public function convertRowIntoMappedArray(Row $row, array $mapping): array
    {
        $data = array_fill_keys(
            array_values($mapping),
            null
        );

        $cellIterator = $row->getCellIterator();
        foreach ($cellIterator as $cell) {
            if (in_array($cell->getColumn(), array_keys($mapping))) {
                $key = $mapping[$cell->getColumn()];
                $data[$key] = $cell->getValue();
            }
        }

        return $data;
    }
}
