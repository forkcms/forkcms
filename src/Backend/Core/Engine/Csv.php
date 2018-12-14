<?php

namespace Backend\Core\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Common\Exception\RedirectException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class Csv
{
    /**
     * Output a CSV-file as a download
     *
     * @param string $filename The name of the file.
     * @param array $array The array to convert.
     * @param array $columns The column names you want to use.
     * @param array $excludeColumns The columns you want to exclude.
     *
     * @throws RedirectException
     */
    public static function outputCSV(
        string $filename,
        array $array,
        array $columns = null,
        array $excludeColumns = null
    ) {
        $serializer = new Serializer(
            [
                new ObjectNormalizer()
            ],
            [
                new CsvEncoder(
                    Authentication::getUser()->getSetting('csv_split_character')
                )
            ]
        );

        $csv = $serializer->encode(
            self::addHeadersToData($columns, $array),
            'csv'
        );

        // set headers for download
        $charset = BackendModel::getContainer()->getParameter('kernel.charset');
        throw new RedirectException(
            'Return the csv data',
            new Response(
                $csv,
                Response::HTTP_OK,
                [
                    'Content-type' => 'application/csv; charset=' . $charset,
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Content-Length' => mb_strlen($csv),
                    'Pragma' => 'no-cache',
                ]
            )
        );
    }

    private static function getLineEnding(): string
    {
        $lineEnding = Authentication::getUser()->getSetting('csv_line_ending');

        // reformat
        if ($lineEnding === '\n') {
            return "\n";
        }
        if ($lineEnding === '\r\n') {
            return "\r\n";
        }

        return $lineEnding;
    }

    private static function addHeadersToData(array $headers, array $data): array
    {
        $processedArray = [];

        foreach ($data as $row) {
            $processedRow = [];

            // link the header names to the data keys
            foreach ($row as $key => $value) {
                $processedRow[$headers[$key]] = $value;
            }

            $processedArray[] = $processedRow;
        }

        return $processedArray;
    }
}
