<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model as BackendModel;
use Common\Exception\RedirectException;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is our extended version of SpoonFileCSV
 */
class Csv extends \SpoonFileCSV
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
        // convert into CSV
        $csv = \SpoonFileCSV::arrayToString(
            $array,
            $columns,
            $excludeColumns,
            Authentication::getUser()->getSetting('csv_split_character'),
            '"',
            self::getLineEnding()
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
}
