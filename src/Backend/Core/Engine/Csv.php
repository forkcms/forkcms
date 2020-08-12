<?php

namespace Backend\Core\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Common\Exception\RedirectException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @deprecated remove this in Fork 6, just use ForkCMS\Utility\Csv\Writer
 */
class Csv extends \SpoonFileCSV
{
    /**
     * Output a CSV-file as a download
     *
     * @deprecated remove this in Fork 6, just use ForkCMS\Utility\Csv\Writer->output()
     *
     * @param string $filename       The name of the file.
     * @param array  $array          The array to convert.
     * @param array  $columns        The column names you want to use.
     * @param array  $excludeColumns The columns you want to exclude.
     *
     * @throws RedirectException
     */
    public static function outputCSV(
        string $filename,
        array $array,
        array $columns = null,
        array $excludeColumns = null
    ) {
        $headers = $columns;
        $data = $array;

        // remove data that should be excluded
        if (!empty($excludeColumns)) {
            $headers = array_filter(
                $columns,
                function ($column) use ($excludeColumns) {
                    return !in_array($column, $excludeColumns);
                }
            );

            foreach ($array as $rowNumber => $row) {
                $data[$rowNumber] = array_filter(
                    $row,
                    function ($key) use ($excludeColumns) {
                        return !in_array($key, $excludeColumns);
                    },
                    ARRAY_FILTER_USE_KEY
                );
            }
        }

        $spreadSheet = new Spreadsheet();
        $sheet = $spreadSheet->getActiveSheet();

        // add data
        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray($data, null, 'A2');

        $writer = new CsvWriter($spreadSheet);
        $writer->setDelimiter(Authentication::getUser()->getSetting('csv_split_character'));
        $writer->setEnclosure('"');
        $writer->setLineEnding(self::getLineEnding());

        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );

        // set headers
        $charset = BackendModel::getContainer()->getParameter('kernel.charset');
        $response->headers->set('Content-type', 'application/csv; charset=' . $charset);
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
        $response->headers->set('Pragma', 'no-cache');

        throw new RedirectException(
            'Return the csv data',
            $response
        );
    }

    /**
     * @deprecated remove this in Fork 6, you should not rely on this.
     */
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
}
