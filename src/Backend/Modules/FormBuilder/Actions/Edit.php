<?php

namespace Backend\Modules\FormBuilder\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Form\Type\DeleteType;
use Frontend\Core\Language\Language as FL;
use Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;
use Backend\Modules\FormBuilder\Engine\Helper as FormBuilderHelper;

/**
 * This is the edit-action, it will display a form to edit an existing item
 */
class Edit extends BackendBaseActionEdit
{
    /**
     * The available templates
     *
     * @var array
     */
    private $templates = [];

    public function execute(): void
    {
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exist
        if ($this->id !== 0 && BackendFormBuilderModel::exists($this->id)) {
            parent::execute();
            $this->getData();
            $this->loadForm();
            $this->validateForm();
            $this->loadDeleteForm();
            $this->parse();
            $this->display();
        } else {
            // no item found, throw an exceptions, because somebody is fucking with our url
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
    }

    private function getData(): void
    {
        $this->record = BackendFormBuilderModel::get($this->id);
        $this->templates = BackendFormBuilderModel::getTemplates();
    }

    private function loadForm(): void
    {
        $this->form = new BackendForm('edit');
        $this->form->addText('name', $this->record['name']);
        $this->form->addDropdown(
            'method',
            [
                'database' => BL::getLabel('MethodDatabase'),
                'database_email' => BL::getLabel('MethodDatabaseEmail'),
                'email' => BL::getLabel('MethodEmail'),
            ],
            $this->record['method']
        );
        $this->form->addText('email', implode(',', (array) $this->record['email']));
        $this->form->addText('email_subject', $this->record['email_subject']);

        // if we have multiple templates, add a dropdown to select them
        if (count($this->templates) > 1) {
            $this->form->addDropdown(
                'template',
                array_combine($this->templates, $this->templates),
                $this->record['email_template']
            );
        }
        $this->form->addText('identifier', $this->record['identifier']);
        $this->form->addEditor('success_message', $this->record['success_message']);

        // textfield dialog
        $this->form->addText('textbox_label');
        $this->form->addText('textbox_value');
        $this->form->addText('textbox_placeholder');
        $this->form->addText('textbox_classname');
        $this->form->addCheckbox('textbox_required');
        $this->form->addCheckbox('textbox_reply_to');
        $this->form->addText('textbox_required_error_message');
        $this->form->addDropdown(
            'textbox_validation',
            [
                '' => '',
                'email' => BL::getLabel('Email'),
                'number' => BL::getLabel('Numeric'),
            ]
        );
        $this->form->addCheckbox('textbox_send_confirmation_mail_to');
        $this->form->addText('textbox_confirmation_mail_subject');
        $this->form->addText('textbox_validation_parameter');
        $this->form->addText('textbox_error_message');

        // textarea dialog
        $this->form->addText('textarea_label');
        $this->form->addTextarea('textarea_value');
        $this->form->getField('textarea_value')->setAttribute('cols', 30);
        $this->form->addText('textarea_placeholder');
        $this->form->addText('textarea_classname');
        $this->form->addCheckbox('textarea_required');
        $this->form->addText('textarea_required_error_message');
        $this->form->addDropdown('textarea_validation', ['' => '']);
        $this->form->addText('textarea_validation_parameter');
        $this->form->addText('textarea_error_message');

        // datetime dialog
        $this->form->addText('datetime_label');
        $this->form->addDropdown(
            'datetime_value_amount',
            [
                '' => '',
                '1' => '+1',
                '2' => '+2',
                '3' => '+3',
                '4' => '+4',
                '5' => '+5',
            ]
        );
        $this->form->addDropdown(
            'datetime_value_type',
            [
                '' => '',
                'today' => BL::getLabel('Today'),
                'day' => BL::getLabel('Day'),
                'week' => BL::getLabel('Week'),
                'month' => BL::getLabel('Month'),
                'year' => BL::getLabel('Year'),
            ]
        );
        $this->form->addDropdown(
            'datetime_type',
            [
                'date' => BL::getLabel('Date'),
                'time' => BL::getLabel('Time'),
            ]
        );
        $this->form->addCheckbox('datetime_required');
        $this->form->addText('datetime_required_error_message');
        $this->form->addDropdown(
            'datetime_type',
            [
                'date' => BL::getLabel('Date'),
                'time' => BL::getLabel('Time'),
            ]
        );
        $this->form->addDropdown(
            'datetime_validation',
            [
                '' => '',
                'time' => BL::getLabel('Time'),
            ]
        );
        $this->form->addText('datetime_classname');
        $this->form->addText('datetime_error_message');

        // dropdown dialog
        $this->form->addText('dropdown_label');
        $this->form->addText('dropdown_values');
        $this->form->addDropdown('dropdown_default_value', ['' => ''])->setAttribute('rel', 'dropDownValues');
        $this->form->addCheckbox('dropdown_required');
        $this->form->addText('dropdown_required_error_message');
        $this->form->addText('dropdown_classname');

        // radiobutton dialog
        $this->form->addText('radiobutton_label');
        $this->form->addText('radiobutton_values');
        $this->form->addDropdown('radiobutton_default_value', ['' => ''])->setAttribute('rel', 'radioButtonValues');
        $this->form->addCheckbox('radiobutton_required');
        $this->form->addText('radiobutton_required_error_message');
        $this->form->addText('radiobutton_classname');

        // checkbox dialog
        $this->form->addText('checkbox_label');
        $this->form->addText('checkbox_values');
        $this->form->addDropdown('checkbox_default_value', ['' => ''])->setAttribute('rel', 'checkBoxValues');
        $this->form->addCheckbox('checkbox_required');
        $this->form->addText('checkbox_required_error_message');
        $this->form->addText('checkbox_classname');

        // heading dialog
        $this->form->addText('heading');

        // paragraph dialog
        $this->form->addEditor('paragraph');
        $this->form->getField('paragraph')->setAttribute('cols', 30);

        // submit dialog
        $this->form->addText('submit');
    }

    protected function parse(): void
    {
        $this->parseFields();

        parent::parse();

        $this->template->assign('id', $this->record['id']);
        $this->template->assign('name', $this->record['name']);
        $recaptchaSiteKey = BackendModel::get('fork.settings')->get('Core', 'google_recaptcha_site_key');
        $recaptchaSecretKey = BackendModel::get('fork.settings')->get('Core', 'google_recaptcha_secret_key');

        if (!($recaptchaSiteKey || $recaptchaSecretKey)) {
            $this->template->assign('recaptchaMissing', true);
        }

        // parse error messages
        $this->parseErrorMessages();
    }

    /**
     * Parse the default error messages
     */
    private function parseErrorMessages(): void
    {
        // set frontend locale
        FL::setLocale(BL::getWorkingLanguage(), true);

        // assign error messages
        $this->template->assign('errors', BackendFormBuilderModel::getErrors());
    }

    private function parseFields(): void
    {
        $fieldsHTML = [];

        // get fields
        $fields = BackendFormBuilderModel::getFields($this->id);

        // loop fields
        foreach ($fields as $field) {
            // submit button
            if ($field['type'] == 'submit') {
                // assign
                $this->template->assign('submitId', $field['id']);

                // add field
                $btn = $this->form->addButton(
                    'submit_field',
                    \SpoonFilter::htmlspecialcharsDecode($field['settings']['values']),
                    'button',
                    'btn btn-default'
                );
                $btn->setAttribute('disabled', 'disabled');

                // skip
                continue;
            }

            // parse field to html
            $fieldsHTML[]['field'] = FormBuilderHelper::parseField($field);
        }

        // assign iteration
        $this->template->assign('fields', $fieldsHTML);
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            $this->form->cleanupFields();

            // shorten the fields
            $txtName = $this->form->getField('name');
            $txtEmail = $this->form->getField('email');
            $txtEmailSubject = $this->form->getField('email_subject');
            $ddmMethod = $this->form->getField('method');
            $txtSuccessMessage = $this->form->getField('success_message');
            $txtIdentifier = $this->form->getField('identifier');

            $emailAddresses = (array) explode(',', $txtEmail->getValue());

            // validate fields
            $txtName->isFilled(BL::getError('NameIsRequired'));
            $txtSuccessMessage->isFilled(BL::getError('SuccessMessageIsRequired'));
            if ($ddmMethod->isFilled(BL::getError('NameIsRequired')) && $ddmMethod->getValue() == 'database_email') {
                $error = false;

                // check the addresses
                foreach ($emailAddresses as $address) {
                    $address = trim($address);

                    if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
                        $error = true;
                        break;
                    }
                }

                // add error
                if ($error) {
                    $txtEmail->addError(BL::getError('EmailIsInvalid'));
                }
            }

            // identifier
            if ($txtIdentifier->isFilled()) {
                // invalid characters
                if (!\SpoonFilter::isValidAgainstRegexp('/^[a-zA-Z0-9\.\_\-]+$/', $txtIdentifier->getValue())) {
                    $txtIdentifier->setError(BL::getError('InvalidIdentifier'));
                } elseif (BackendFormBuilderModel::existsIdentifier($txtIdentifier->getValue(), $this->id)) {
                    $txtIdentifier->setError(BL::getError('UniqueIdentifier'));
                }
            }

            if ($this->form->isCorrect()) {
                // build array
                $values = [];
                $values['name'] = $txtName->getValue();
                $values['method'] = $ddmMethod->getValue();
                $values['email'] = ($ddmMethod->getValue() == 'database_email' || $ddmMethod->getValue() === 'email')
                    ? serialize($emailAddresses) : null;
                $values['email_template'] = count($this->templates) > 1
                    ? $this->form->getField('template')->getValue() : $this->templates[0];
                $values['email_subject'] = empty($txtEmailSubject->getValue()) ? null : $txtEmailSubject->getValue();
                $values['success_message'] = $txtSuccessMessage->getValue(true);
                $values['identifier'] = ($txtIdentifier->isFilled() ?
                    $txtIdentifier->getValue() :
                    BackendFormBuilderModel::createIdentifier()
                );
                $values['edited_on'] = BackendModel::getUTCDate();

                // insert the item
                $id = (int) BackendFormBuilderModel::update($this->id, $values);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createUrlForAction('Index') . '&report=edited&var=' .
                    rawurlencode($values['name']) . '&highlight=row-' . $id
                );
            }
        }
    }

    private function loadDeleteForm(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $this->record['id']],
            ['module' => $this->getModule()]
        );
        $this->template->assign('deleteForm', $deleteForm->createView());
    }
}
