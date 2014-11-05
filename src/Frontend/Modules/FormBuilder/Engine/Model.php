<?php

namespace Frontend\Modules\FormBuilder\Engine;

use Frontend\Core\Engine\Model as FrontendModel;

/**
 * In this file we store all generic functions that we will be using in the form_builder module
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Jeroen Desloovere <info@jeroendesloovere.be>
 */
class Model
{
    /**
     * Get an item.
     *
     * @param string $id The id of the item to fetch.
     * @return array
     */
    public static function get($id)
    {
        $id = (int) $id;

        // get form
        $form = (array) FrontendModel::getContainer()->get('database')->getRecord(
            'SELECT i.id, i.method, i.name, i.email, i.success_message, i.identifier
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
     * @return int
     */
    public static function insertDataField(array $data)
    {
        return FrontendModel::getContainer()->get('database')->insert('forms_data_fields', $data);
    }

    /**
     * Notify the admin
     *
     * @param array $data
     */
    public static function notifyAdmin(array $data)
    {
        $alert = array(
            'loc-key' => 'FORMBUILDER_SUBMISSION'
        );

        // build data
        $data = array(
            'api' => SITE_URL . '/api/1.0',
            'form_id' => $data['form_id'],
            'id' => $data['entry_id']
        );

        // push it
        FrontendModel::pushToAppleApp($alert, 1, 'default', $data);
    }
}
