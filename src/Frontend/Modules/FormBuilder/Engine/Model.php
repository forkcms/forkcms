<?php

namespace Frontend\Modules\FormBuilder\Engine;

use Frontend\Core\Engine\Model as FrontendModel;

/**
 * In this file we store all generic functions that we will be using in the form_builder module
 */
class Model
{
    public static function get(string $id): array
    {
        // get form
        $form = (array) FrontendModel::getContainer()->get('database')->getRecord(
            'SELECT i.id, i.email_subject, i.email_template, i.language, i.method, i.name, i.email,
                    i.success_message, i.identifier
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

    public static function getFields(int $formId): array
    {
        // get fields
        $fields = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.type, i.settings
             FROM forms_fields AS i
             WHERE i.form_id = ?
             ORDER BY i.sequence ASC',
            $formId,
            'id'
        );

        if (empty($fields)) {
            return [];
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
            $field['validations'] = [];
        }

        // we need to loop because one field can have multiple 'type' validations
        foreach ($fieldValidations as $fieldValidation) {
            // add validation type to our field
            $fields[$fieldValidation['field_id']]['validations'][$fieldValidation['type']] = $fieldValidation;
        }

        return $fields;
    }

    public static function insertData(array $formData): int
    {
        return FrontendModel::getContainer()->get('database')->insert('forms_data', $formData);
    }

    public static function insertDataField(array $dataField): int
    {
        return FrontendModel::getContainer()->get('database')->insert('forms_data_fields', $dataField);
    }

    /**
     * Convert a PHP Date to jquery date format
     *
     * @param string $phpFormat The php date format
     *
     * @return string The jQuery date format
     */
    public static function convertPHPDateToJquery(string $phpFormat): string
    {
        $symbolsMatching = [
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
        ];
        $jqueryuiFormat = '';
        $escaping = false;
        for ($i = 0; $i < mb_strlen($phpFormat); ++$i) {
            $char = $phpFormat[$i];
            if ($char === '\\') {
                // PHP date format escaping character

                ++$i;
                if ($escaping) {
                    $jqueryuiFormat .= $phpFormat[$i];
                } else {
                    $jqueryuiFormat .= '\'' . $phpFormat[$i];
                }
                $escaping = true;
            } else {
                if ($escaping) {
                    $jqueryuiFormat .= "'";
                    $escaping = false;
                }
                if (isset($symbolsMatching[$char])) {
                    $jqueryuiFormat .= $symbolsMatching[$char];
                } else {
                    $jqueryuiFormat .= $char;
                }
            }
        }

        return $jqueryuiFormat;
    }
}
