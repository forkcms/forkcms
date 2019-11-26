<?php

namespace Backend\Modules\FormBuilder\Installer;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraRepository;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraType;
use Backend\Modules\Pages\Domain\Page\Page;

/**
 * Installer for the form_builder module
 */
class Installer extends ModuleInstaller
{
    public function install(): void
    {
        $this->addModule('FormBuilder');
        $this->importSQL(__DIR__ . '/Data/install.sql');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureSettings();
        $this->configureBackendRights();
        $this->configureBackendNavigation();
        $this->configureFrontendPages();
    }

    private function configureSettings(): void
    {
        $this->setSetting($this->getModule(), 'requires_google_recaptcha', true);
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Modules"
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation($navigationModulesId, 'FormBuilder', 'form_builder/index', [
            'form_builder/add',
            'form_builder/edit',
            'form_builder/data',
            'form_builder/data_details',
        ]);
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Data');
        $this->setActionRights(1, $this->getModule(), 'DataDetails');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'DeleteField'); // AJAX
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'ExportData');
        $this->setActionRights(1, $this->getModule(), 'GetField'); // AJAX
        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'MassDataAction');
        $this->setActionRights(1, $this->getModule(), 'SaveField'); // AJAX
        $this->setActionRights(1, $this->getModule(), 'Sequence'); // AJAX
    }

    private function configureFrontendPages(): void
    {
        $searchWidgetId = $this->getSearchWidgetId();

        // loop languages
        foreach ($this->getLanguages() as $language) {
            // create form
            $form = [];
            $form['language'] = $language;
            $form['user_id'] = $this->getDefaultUserID();
            $form['name'] = \SpoonFilter::ucfirst($this->getLocale('Contact', 'Core', $language, 'lbl', 'Frontend'));
            $form['method'] = 'database_email';
            $form['email'] = serialize([$this->getVariable('email')]);
            $form['success_type'] = 'message';
            $form['success_message'] = $this->getLocale('ContactMessageSent', 'Core', $language, 'msg', 'Frontend');
            $form['identifier'] = 'contact-' . $language;
            $form['created_on'] = gmdate('Y-m-d H:i:s');
            $form['edited_on'] = gmdate('Y-m-d H:i:s');
            $formId = $this->getDatabase()->insert('forms', $form);

            // create submit button
            $field = [];
            $field['form_id'] = $formId;
            $field['type'] = 'submit';
            $field['settings'] = serialize(
                [
                    'values' => \SpoonFilter::ucfirst($this->getLocale('Send', 'Core', $language, 'lbl', 'Frontend')),
                ]
            );
            $this->getDatabase()->insert('forms_fields', $field);

            // create name field
            $field['form_id'] = $formId;
            $field['type'] = 'textbox';
            $field['settings'] = serialize(
                [
                    'label' => \SpoonFilter::ucfirst($this->getLocale('Name', 'Core', $language, 'lbl', 'Frontend')),
                ]
            );
            $nameId = $this->getDatabase()->insert('forms_fields', $field);

            // name validation
            $validate = [];
            $validate['field_id'] = $nameId;
            $validate['type'] = 'required';
            $validate['error_message'] = $this->getLocale('NameIsRequired', 'Core', $language, 'err', 'Frontend');
            $this->getDatabase()->insert('forms_fields_validation', $validate);

            // create email field
            $field['form_id'] = $formId;
            $field['type'] = 'textbox';
            $field['settings'] = serialize(
                [
                    'label' => \SpoonFilter::ucfirst($this->getLocale('Email', 'Core', $language, 'lbl', 'Frontend')),
                ]
            );
            $emailId = $this->getDatabase()->insert('forms_fields', $field);

            // email validation
            $validate['field_id'] = $emailId;
            $validate['type'] = 'email';
            $validate['error_message'] = $this->getLocale('EmailIsInvalid', 'Core', $language, 'err', 'Frontend');
            $this->getDatabase()->insert('forms_fields_validation', $validate);

            // create message field
            $field['form_id'] = $formId;
            $field['type'] = 'textarea';
            $field['settings'] = serialize(
                [
                    'label' => \SpoonFilter::ucfirst($this->getLocale('Message', 'Core', $language, 'lbl', 'Frontend')),
                ]
            );
            $messageId = $this->getDatabase()->insert('forms_fields', $field);

            // name validation
            $validate['field_id'] = $messageId;
            $validate['type'] = 'required';
            $validate['error_message'] = $this->getLocale('MessageIsRequired', 'Core', $language, 'err', 'Frontend');
            $this->getDatabase()->insert('forms_fields_validation', $validate);

            // insert extra
            $extraId = $this->insertExtra(
                'FormBuilder',
                ModuleExtraType::widget(),
                'FormBuilder',
                'Form',
                [
                    'language' => $form['language'],
                    'extra_label' => $form['name'],
                    'id' => $formId,
                ],
                false,
                '400' . $formId
            );

            // insert contact page
            $this->insertPage(
                [
                    'title' => \SpoonFilter::ucfirst($this->getLocale('Contact', 'Core', $language, 'lbl', 'Frontend')),
                    'parent_id' => Page::HOME_PAGE_ID,
                    'language' => $language,
                ],
                null,
                ['html' => PATH_WWW . '/src/Backend/Modules/Pages/Installer/Data/' . $language . '/contact.txt'],
                ['extra_id' => $extraId, 'position' => 'main'],
                ['extra_id' => $searchWidgetId, 'position' => 'top']
            );
        }
    }

    private function getSearchWidgetId(): int
    {
        /** @var ModuleExtraRepository $moduleExtraRepository */
        $moduleExtraRepository = BackendModel::get(ModuleExtraRepository::class);
        $widgetId = $moduleExtraRepository->getModuleExtraId('Search', 'Form', ModuleExtraType::widget());

        if ($widgetId === null) {
            throw new \RuntimeException('Could not find Search Widget');
        }

        return $widgetId;
    }
}
