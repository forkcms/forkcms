<?php

namespace Backend\Modules\FormBuilder\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Common\ModuleExtraType;
use Frontend\Core\Language\Language as FL;
use Symfony\Component\Finder\Finder;

/**
 * In this file we store all generic functions that we will be using in the form_builder module
 */
class Model
{
    const QUERY_BROWSE =
        'SELECT i.id, i.name, i.email, i.method,
         (SELECT COUNT(fd.form_id) FROM forms_data AS fd WHERE fd.form_id = i.id) AS sent_forms
         FROM forms AS i
         WHERE i.language = ?';

    /**
     * Calculate time ago.
     *
     * @param int $timestamp Unix timestamp from the past.
     *
     * @return string
     */
    public static function calculateTimeAgo(int $timestamp): string
    {
        $secondsBetween = time() - $timestamp;

        // calculate
        $hours = floor($secondsBetween / (60 * 60));
        $minutes = floor($secondsBetween / 60);
        $seconds = floor($secondsBetween);

        // today start
        $todayStart = (int) strtotime(date('d F Y'));

        // today
        if ($timestamp >= $todayStart) {
            // today
            if ($hours >= 1) {
                return BL::getLabel('Today') . ' ' . date('H:i', $timestamp);
            } elseif ($minutes > 1) {
                // more than one minute
                return sprintf(BL::getLabel('MinutesAgo'), $minutes);
            } elseif ($minutes == 1) {
                // one minute
                return BL::getLabel('OneMinuteAgo');
            } elseif ($seconds > 1) {
                // more than one second
                return sprintf(BL::getLabel('SecondsAgo'), $seconds);
            } elseif ($seconds <= 1) {
                // one second
                return BL::getLabel('OneSecondAgo');
            }
        } elseif ($timestamp < $todayStart && $timestamp >= ($todayStart - 86400)) {
            // yesterday
            return BL::getLabel('Yesterday') . ' ' . date('H:i', $timestamp);
        } else {
            // older
            return date('d/m/Y H:i', $timestamp);
        }
    }

    /**
     * Create an unique identifier.
     *
     * @return string
     */
    public static function createIdentifier(): string
    {
        // get last id
        $id = (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.id FROM forms AS i ORDER BY i.id DESC LIMIT 1'
        );

        // create identifier
        do {
            ++$id;
            $identifier = 'form' . $id;
        } while (self::identifierExist($identifier));

        return $identifier;
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    private static function identifierExist(string $identifier): bool
    {
        return (int) BackendModel::getContainer()->get('database')
                ->getVar(
                    'SELECT 1
                 FROM forms AS i
                 WHERE i.identifier = ?
                 LIMIT 1',
                    $identifier
                ) > 0;
    }

    /**
     * Delete an item.
     *
     * @param int $id The id of the record to delete.
     */
    public static function delete(int $id): void
    {
        $database = BackendModel::getContainer()->get('database');

        // get field ids
        $fieldIds = (array) $database->getColumn('SELECT i.id FROM forms_fields AS i WHERE i.form_id = ?', $id);

        // we have items to be deleted
        if (!empty($fieldIds)) {
            // delete all fields
            $database->delete('forms_fields', 'form_id = ?', $id);
            $database->delete('forms_fields_validation', 'field_id IN(' . implode(',', $fieldIds) . ')');
        }

        // get data ids
        $dataIds = (array) $database->getColumn('SELECT i.id FROM forms_data AS i WHERE i.form_id = ?', $id);

        // we have items to be deleted
        if (!empty($dataIds)) {
            self::deleteData($dataIds);
        }

        // delete extra
        BackendModel::deleteExtra('FormBuilder', 'widget', ['id' => $id]);

        // delete form
        $database->delete('forms', 'id = ?', $id);
    }

    /**
     * Deletes one or more data items.
     *
     * @param array $ids Ids of data items.
     */
    public static function deleteData(array $ids): void
    {
        $database = BackendModel::getContainer()->get('database');

        $database->delete('forms_data', 'id IN(' . implode(',', $ids) . ')');
        $database->delete('forms_data_fields', 'data_id IN(' . implode(',', $ids) . ')');
    }

    /**
     * Delete a field.
     *
     * @param int $id Id of a field.
     */
    public static function deleteField(int $id): void
    {
        // delete linked validation
        self::deleteFieldValidation($id);

        // delete field
        BackendModel::getContainer()->get('database')->delete('forms_fields', 'id = ?', $id);
    }

    /**
     * Delete all validation of a field.
     *
     * @param int $id Id of a field.
     */
    public static function deleteFieldValidation(int $id): void
    {
        BackendModel::getContainer()->get('database')->delete('forms_fields_validation', 'field_id = ?', $id);
    }

    /**
     * Does the item exist.
     *
     * @param int $id Id of a form.
     *
     * @return bool
     */
    public static function exists(int $id): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM forms AS f
             WHERE f.id = ?
             LIMIT 1',
            $id
        );
    }

    /**
     * Does the data item exist.
     *
     * @param int $id Id of the data item.
     *
     * @return bool
     */
    public static function existsData(int $id): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM forms_data AS fd
             WHERE fd.id = ?
             LIMIT 1',
            $id
        );
    }

    /**
     * Does a field exist (within a form).
     *
     * @param int $id Id of a field.
     * @param int $formId Id of a form.
     *
     * @return bool
     */
    public static function existsField(int $id, int $formId = null): bool
    {
        // exists
        if ($formId === null) {
            return (bool) BackendModel::getContainer()->get('database')->getVar(
                'SELECT 1
                 FROM forms_fields AS ff
                 WHERE ff.id = ?
                 LIMIT 1',
                $id
            );
        }

        // exists and ignore an id
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM forms_fields AS ff
             WHERE ff.id = ? AND ff.form_id = ?
             LIMIT 1',
            [$id, $formId]
        );
    }

    /**
     * Does an identifier exist.
     *
     * @param string $identifier Identifier.
     * @param int $ignoreId Field id to ignore.
     *
     * @return bool
     */
    public static function existsIdentifier(string $identifier, int $ignoreId = null): bool
    {
        // exists
        if ($ignoreId === null) {
            return (bool) BackendModel::getContainer()->get('database')->getVar(
                'SELECT 1
                 FROM forms AS f
                 WHERE f.identifier = ?
                 LIMIT 1',
                $identifier
            );
        }

        // exists and ignore an id
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM forms AS f
             WHERE f.identifier = ? AND f.id != ?
             LIMIT 1',
            [$identifier, $ignoreId]
        );
    }

    /**
     * Formats the recipients based on the serialized string
     *
     * @param string $string The serialized string that should be formatted
     *
     * @return string
     */
    public static function formatRecipients(string $string): string
    {
        return implode(', ', (array) @unserialize((string) $string));
    }

    /**
     * Get all data for a given id.
     *
     * @param int $id The id for the record to get.
     *
     * @return array
     */
    public static function get(int $id): array
    {
        $return = (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT f.* FROM forms AS f WHERE f.id = ?',
            $id
        );

        // unserialize the emailaddresses
        if (isset($return['email'])) {
            $return['email'] = (array) unserialize($return['email']);
        }

        return $return;
    }

    /**
     * Get data for a given id.
     *
     * @param int $id The id for the record to get.
     *
     * @return array
     */
    public static function getData(int $id): array
    {
        // get data
        $data = (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT fd.id, fd.form_id, UNIX_TIMESTAMP(fd.sent_on) AS sent_on
             FROM forms_data AS fd
             WHERE fd.id = ?',
            $id
        );

        // get fields
        $data['fields'] = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT fdf.label, fdf.value
             FROM forms_data_fields AS fdf
             WHERE fdf.data_id = ?
             ORDER BY fdf.id',
            (int) $data['id']
        );

        // unserialize values
        foreach ($data['fields'] as &$field) {
            if ($field['value'] !== null) {
                $field['value'] = unserialize($field['value']);
            }
        }

        return $data;
    }

    /**
     * Get errors (optional by type).
     *
     * @param string $type Type of error.
     *
     * @return mixed
     */
    public static function getErrors(string $type = null)
    {
        $errors = [];
        $errors['required'] = FL::getError('FieldIsRequired');
        $errors['email'] = FL::getError('EmailIsInvalid');
        $errors['number'] = FL::getError('NumericCharactersOnly');
        $errors['time'] = FL::getError('TimeIsInvalid');

        // specific type
        if ($type !== null) {
            return $errors[$type];
        }

        // all errors
        $return = [];

        // loop errors
        foreach ($errors as $key => $error) {
            $return[] = ['type' => $key, 'message' => $error];
        }

        return $return;
    }

    /**
     * Get a field.
     *
     * @param int $id Id of a field.
     *
     * @return array
     */
    public static function getField(int $id): array
    {
        $field = (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT ff.id, ff.form_id, ff.type, ff.settings
             FROM forms_fields AS ff
             WHERE ff.id = ?',
            $id
        );

        // unserialize settings
        if ($field['settings'] !== null) {
            $field['settings'] = unserialize($field['settings']);
        }

        // get validation
        $field['validations'] = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT ffv.type, ffv.parameter, ffv.error_message
             FROM forms_fields_validation AS ffv
             WHERE ffv.field_id = ?',
            $field['id'],
            'type'
        );

        return $field;
    }

    /**
     * Get all fields of a form.
     *
     * @param int $id Id of a form.
     *
     * @return array
     */
    public static function getFields(int $id): array
    {
        $fields = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT ff.id, ff.type, ff.settings
             FROM forms_fields AS ff
             WHERE ff.form_id = ?
             ORDER BY ff.sequence ASC',
            $id
        );

        foreach ($fields as &$field) {
            // unserialize
            if ($field['settings'] !== null) {
                $field['settings'] = unserialize($field['settings']);
            }

            // get validation
            $field['validations'] = (array) BackendModel::getContainer()->get('database')->getRecords(
                'SELECT ffv.type, ffv.parameter, ffv.error_message
                 FROM forms_fields_validation AS ffv
                 WHERE ffv.field_id = ?',
                $field['id'],
                'type'
            );
        }

        return $fields;
    }

    /**
     * Get a label/action/message from locale.
     * Used as datagridfunction.
     *
     * @param string $name Name of the locale item.
     * @param string $type Type of locale item.
     * @param string $application Name of the application.
     *
     * @return string
     */
    public static function getLocale(string $name, string $type = 'label', string $application = 'Backend'): string
    {
        $name = \SpoonFilter::toCamelCase($name);
        $class = \SpoonFilter::ucfirst($application) . '\Core\Language\Language';
        $function = 'get' . \SpoonFilter::ucfirst($type);

        // execute and return value
        return \SpoonFilter::ucfirst(call_user_func_array([$class, $function], [$name]));
    }

    /**
     * Get the maximum sequence for fields in a form.
     *
     * @param int $formId Id of the form.
     *
     * @return int
     */
    public static function getMaximumSequence(int $formId): int
    {
        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT MAX(ff.sequence)
             FROM forms_fields AS ff
             WHERE ff.form_id = ?',
            $formId
        );
    }

    /**
     * Add a new item.
     *
     * @param array $values The data to insert.
     *
     * @return int
     */
    public static function insert(array $values): int
    {
        // define form id
        $formId = BackendModel::getContainer()->get('database')->insert('forms', $values);

        // insert extra
        BackendModel::insertExtra(
            ModuleExtraType::widget(),
            'FormBuilder',
            'Form',
            'FormBuilder',
            [
                'id' => $formId,
                'extra_label' => $values['name'],
                'language' => $values['language'],
                'edit_url' => BackendModel::createUrlForAction('Edit') . '&id=' . $formId,
            ],
            false,
            '400' . $formId
        );

        return $formId;
    }

    /**
     * Add a new field.
     *
     * @param array $values The data to insert.
     *
     * @return int
     */
    public static function insertField(array $values): int
    {
        return BackendModel::getContainer()->get('database')->insert('forms_fields', $values);
    }

    /**
     * Add validation for a field.
     *
     * @param array $values The data to insert.
     *
     * @return int
     */
    public static function insertFieldValidation(array $values): int
    {
        return BackendModel::getContainer()->get('database')->insert('forms_fields_validation', $values);
    }

    /**
     * Update an existing item.
     *
     * @param int $id The id for the item to update.
     * @param array $values The new data.
     *
     * @return int
     */
    public static function update(int $id, array $values): int
    {
        $database = BackendModel::getContainer()->get('database');

        // update item
        $database->update('forms', $values, 'id = ?', $id);

        // build array
        $extra = [
            'data' => serialize(
                [
                    'language' => BL::getWorkingLanguage(),
                    'extra_label' => $values['name'],
                    'id' => $id,
                    'edit_url' => BackendModel::createUrlForAction('Edit') . '&id=' . $id,
                ]
            ),
        ];

        // update extra
        $database->update(
            'modules_extras',
            $extra,
            'module = ? AND type = ? AND sequence = ?',
            ['FormBuilder', 'widget', '400' . $id]
        );

        return $id;
    }

    /**
     * Update a field.
     *
     * @param int $id The id for the item to update.
     * @param array $values The new data.
     *
     * @return int
     */
    public static function updateField(int $id, array $values): int
    {
        BackendModel::getContainer()->get('database')->update('forms_fields', $values, 'id = ?', $id);

        return $id;
    }

    /**
     * Get templates.
     *
     * @return array
     */
    public static function getTemplates(): array
    {
        $templates = [];
        $finder = new Finder();
        $finder->name('*.html.twig');
        $finder->in(FRONTEND_MODULES_PATH . '/FormBuilder/Layout/Templates/Mails');

        // if there is a custom theme we should include the templates there also
        $theme = BackendModel::get('fork.settings')->get('Core', 'theme', 'Fork');
        if ($theme !== 'core') {
            $path = FRONTEND_PATH . '/Themes/' . $theme . '/Modules/FormBuilder/Layout/Templates/Mails';
            if (is_dir($path)) {
                $finder->in($path);
            }
        }

        foreach ($finder->files() as $file) {
            $templates[] = $file->getBasename();
        }

        return array_unique($templates);
    }
}
