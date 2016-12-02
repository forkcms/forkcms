<?php

namespace Frontend\Modules\FormBuilder\Engine;

use Frontend\Core\Engine\Model as FrontendModel;

/**
 * In this file we store all generic functions that we will be using in the form_builder module
 */
class Model
{
    /**
     * Get an item.
     *
     * @param string $id The id of the item to fetch.
     *
     * @return array
     */
    public static function get($id)
    {
        $id = (int) $id;

        // get form
        $form = (array) FrontendModel::getContainer()->get('database')->getRecord(
            'SELECT i.id, i.language, i.method, i.name, i.email, i.success_message, i.identifier
             FROM forms AS i
             WHERE i.id = ?',
            $id
        );

        // unserialize the recipients
        if (isset($form['email'])) {
            $form['email'] = (array) unserialize($form['email']);
        }

        // get validation
        $form['fields'] = self::getFields($id);

        return $form;
    }

    /**
     * Get all fields of a form.
     *
     * @param int $id The id of the form wherefore we fetch the fields.
     *
     * @return array
     */
    public static function getFields($id)
    {
        // get fields
        $fields = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.type, i.settings
             FROM forms_fields AS i
             WHERE i.form_id = ?
             ORDER BY i.sequence ASC',
            (int) $id,
            'id'
        );

        if (empty($fields)) {
            return false;
        }

        // create an array with an equal amount of questionmarks as ids provided
        $idPlaceHolders = array_fill(0, count($fields), '?');

        // get field validations, each field can have multiple 'type' validations
        $fieldValidations = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.field_id, i.type, i.parameter, i.error_message
             FROM forms_fields_validation AS i
             WHERE i.field_id IN (' . implode(', ', $idPlaceHolders) . ')',
            array_keys($fields)
        );

        // loop fields to add extra parameters
        foreach ($fields as &$field) {
            // unserialize
            if ($field['settings'] !== null) {
                $field['settings'] = unserialize($field['settings']);
            }

            // init validations
            $field['validations'] = array();
        }

        // we need to loop because one field can have multiple 'type' validations
        foreach ($fieldValidations as $fieldValidation) {
            // add validation type to our field
            $fields[$fieldValidation['field_id']]['validations'][$fieldValidation['type']] = $fieldValidation;
        }

        return $fields;
    }

    /**
     * Insert data.
     *
     * @param array $data The data to insert.
     *
     * @return int
     */
    public static function insertData(array $data)
    {
        return FrontendModel::getContainer()->get('database')->insert('forms_data', $data);
    }

    /**
     * Insert data fields.
     *
     * @param array $data The data to insert.
     *
     * @return int
     */
    public static function insertDataField(array $data)
    {
        return FrontendModel::getContainer()->get('database')->insert('forms_data_fields', $data);
    }

    /**
     * Convert a PHP Date to jquery date format
     *
     * @param string $php_format The php date format
     *
     * @return string The jQuery date format
     */
    public static function convertPHPDateToJquery($php_format)
    {
        $SYMBOLS_MATCHING = array(
            // Day
            'd' => 'dd',
            'D' => 'D',
            'j' => 'd',
            'l' => 'DD',
            'N' => '',
            'S' => '',
            'w' => '',
            'z' => 'o',
            // Week
            'W' => '',
            // Month
            'F' => 'MM',
            'm' => 'mm',
            'M' => 'M',
            'n' => 'm',
            't' => '',
            // Year
            'L' => '',
            'o' => '',
            'Y' => 'yy',
            'y' => 'y',
            // Time
            'a' => '',
            'A' => '',
            'B' => '',
            'g' => '',
            'G' => '',
            'h' => '',
            'H' => '',
            'i' => '',
            's' => '',
            'u' => '',
        );
        $jqueryui_format = '';
        $escaping = false;
        for ($i = 0; $i < mb_strlen($php_format); ++$i) {
            $char = $php_format[$i];
            if ($char === '\\') {
                // PHP date format escaping character

                ++$i;
                if ($escaping) {
                    $jqueryui_format .= $php_format[$i];
                } else {
                    $jqueryui_format .= '\'' . $php_format[$i];
                }
                $escaping = true;
            } else {
                if ($escaping) {
                    $jqueryui_format .= "'";
                    $escaping = false;
                }
                if (isset($SYMBOLS_MATCHING[$char])) {
                    $jqueryui_format .= $SYMBOLS_MATCHING[$char];
                } else {
                    $jqueryui_format .= $char;
                }
            }
        }

        return $jqueryui_format;
    }
}
