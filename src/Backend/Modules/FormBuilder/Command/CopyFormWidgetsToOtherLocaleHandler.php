<?php

namespace Backend\Modules\FormBuilder\Command;

use Backend\Core\Engine\Model as BackendModel;
use Common\ModuleExtraType;
use SpoonDatabase;

final class CopyFormWidgetsToOtherLocaleHandler
{
    /** @var SpoonDatabase */
    private $database;

    public function __construct(SpoonDatabase $database)
    {
        $this->database = $database;
    }

    public function handle(CopyFormWidgetsToOtherLocale $command): void
    {
        $currentWidgets = (array) $this->database->getRecords(
            'SELECT * FROM modules_extras WHERE module = ? AND type = ? AND action = ?',
            [
                'FormBuilder',
                ModuleExtraType::widget(),
                'Form'
            ]
        );

        foreach ($currentWidgets as $currentWidget) {
            $data = unserialize($currentWidget['data'], ['allowed_classes' => false]);

            if (!is_array($data)
                || !isset($data['language'])
                || $data['language'] !== $command->fromLocale->getLocale()
            ) {
                // This is not a widget we want to duplicate
                continue;
            }

            // Duplicate the existing form
            $currentFormId = $data['id'];
            $data['id'] = $this->duplicateForm($currentFormId, $command->toLocale->getLocale());

            // Replace the language of our widget
            $data['language'] = $command->toLocale->getLocale();
            $data['edit_url'] = BackendModel::createUrlForAction('Edit', 'FormBuilder', $command->toLocale->getLocale()) . '&id=' . $data['id'];
            $currentWidget['data'] = serialize($data);

            // Save the old ID
            $oldId = $currentWidget['id'];

            // Unset the ID so we get a new one
            unset($currentWidget['id']);

            // Insert the new widget and save the id
            $newId = $this->database->insert('modules_extras', $currentWidget);

            // Map the new ID
            $command->extraIdMap[$oldId] = $newId;
        }
    }

    private function duplicateForm(int $formId, string $newLanguage): int
    {
        $currentForm = (array) $this->database->getRecord(
            'SELECT * FROM forms WHERE id = ?',
            [
                $formId,
            ]
        );

        unset($currentForm['id']);

        $currentForm['language'] = $newLanguage;

        $newId = $this->database->insert('forms', $currentForm);

        $currentFormFields = (array) $this->database->getRecords(
            'SELECT * FROM forms_fields WHERE form_id = ?',
            [
                $formId,
            ]
        );

        foreach ($currentFormFields as $field) {
            // Save the old field ID
            $oldFieldId = $field['id'];

            // Switch to the new form
            $field['form_id'] = $newId;

            // Unset the ID so we get a new one
            unset($field['id']);

            // Insert the new field
            $newFieldId = $this->database->insert('forms_fields', $field);

            $currentFieldValidations = (array) $this->database->getRecords(
                'SELECT * FROM forms_fields_validation WHERE field_id = ?',
                [
                    $oldFieldId,
                ]
            );

            foreach ($currentFieldValidations as $fieldValidations) {
                $fieldValidations['field_id'] = $newFieldId;

                // Unset the ID so we get a new one
                unset($fieldValidations['id']);

                // Insert the new field validation
                $this->database->insert('forms_fields_validation', $fieldValidations);
            }
        }

        return $newId;
    }
}
