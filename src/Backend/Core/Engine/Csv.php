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
 * This is our extended version of SpoonFileCSV
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Csv extends \SpoonFileCSV
{
    /**
     * Output a CSV-file as a download
     *
     * @param string $filename       The name of the file.
     * @param array  $array          The array to convert.
     * @param array  $columns        The column names you want to use.
     * @param array  $excludeColumns The columns you want to exclude.
     */
    public static function outputCSV($filename, array $array, array $columns = null, array $excludeColumns = null)
    {
        // get settings
        $splitCharacter = Authentication::getUser()->getSetting('csv_split_character');
        $lineEnding = Authentication::getUser()->getSetting('csv_line_ending');

        // reformat
        if ($lineEnding == '\n') {
            $lineEnding = "\n";
        }
        if ($lineEnding == '\r\n') {
            $lineEnding = "\r\n";
        }

        // convert into CSV
        $csv = \SpoonFileCSV::arrayToString($array, $columns, $excludeColumns, $splitCharacter, '"', $lineEnding);

        // set headers for download
        $charset = BackendModel::getContainer()->getParameter('kernel.charset');
        header('Content-type: application/csv; charset=' . $charset);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . mb_strlen($csv));
        header('Pragma: no-cache');

        // output the CSV
        echo $csv;
        exit;
    }
}
