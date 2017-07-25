<?php

namespace Backend\Modules\FormBuilder\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language as BL;
use Backend\Modules\FormBuilder\Engine\Helper as FormBuilderHelper;
use Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;
use Common\Uri as CommonUri;
use Symfony\Component\HttpFoundation\Response;

/**
 * Save a field via ajax.
 */
class SaveField extends BackendBaseAJAXAction
{
    public function execute(): void
    {
        parent::execute();

        // get parameters
        $formId = $this->getRequest()->request->getInt('form_id');
        $fieldId = $this->getRequest()->request->getInt('field_id');
        $type = $this->getRequest()->request->get('type');
        if (!in_array(
            $type,
            [
                'checkbox',
                'dropdown',
                'datetime',
                'heading',
                'paragraph',
                'radiobutton',
                'submit',
                'textarea',
                'textbox',
                'recaptcha',
            ]
        )) {
            $type = '';
        }
        $label = trim($this->getRequest()->request->get('label', ''));
        $values = trim($this->getRequest()->request->get('values', ''));

        // this is somewhat a nasty hack, but it makes special chars work.
        $values = \SpoonFilter::htmlspecialcharsDecode($values);

        $defaultValues = trim($this->getRequest()->request->get('default_values', ''));
        $placeholder = trim($this->getRequest()->request->get('placeholder', ''));
        $classname = trim($this->getRequest()->request->get('classname', ''));
        $required = $this->getRequest()->request->getBoolean('required');
        $requiredErrorMessage = trim($this->getRequest()->request->get('required_error_message', ''));
        $validation = $this->getRequest()->request->get('validation');
        if (!in_array($validation, ['email', 'number', 'time'])) {
            $validation = '';
        }
        $validationParameter = trim($this->getRequest()->request->get('validation_parameter', ''));
        $errorMessage = trim($this->getRequest()->request->get('error_message', ''));

        // special field for textbox
        $replyTo = $this->getRequest()->request->getBoolean('reply_to');
        $sendConfirmationMailTo = $this->getRequest()->request->getBoolean('send_confirmation_mail_to');
        $confirmationMailSubject = trim($this->getRequest()->request->get('confirmation_mail_subject'));

        // special fields for datetime
        $inputType = $this->getRequest()->request->get('input_type');
        if (!in_array($inputType, ['date', 'time'])) {
            $inputType = 'date';
        }
        $valueAmount = trim($this->getRequest()->request->get('value_amount'));
        $valueType = trim($this->getRequest()->request->get('value_type'));

        // invalid form id
        if (!BackendFormBuilderModel::exists($formId)) {
            $this->output(Response::HTTP_BAD_REQUEST, null, 'form does not exist');

            return;
        }
        // invalid fieldId
        if ($fieldId !== 0 && !BackendFormBuilderModel::existsField($fieldId, $formId)) {
            $this->output(Response::HTTP_BAD_REQUEST, null, 'field does not exist');

            return;
        }
        // invalid type
        if ($type === '') {
            $this->output(Response::HTTP_BAD_REQUEST, null, 'invalid type provided');

            return;
        }
        // extra validation is only possible for textfields & datetime fields
        if ($type !== 'textbox' && $type !== 'datetime') {
            $validation = '';
            $validationParameter = '';
            $errorMessage = '';
        }

        // init
        $errors = [];

        // validate textbox
        if ($type === 'textbox') {
            if ($label === '') {
                $errors['label'] = BL::getError('LabelIsRequired');
            }
            if ($required && $requiredErrorMessage === '') {
                $errors['required_error_message'] = BL::getError('ErrorMessageIsRequired');
            }
            if ($validation !== '' && $errorMessage === '') {
                $errors['error_message'] = BL::getError('ErrorMessageIsRequired');
            }
            if ($replyTo && $validation !== 'email') {
                $errors['reply_to_error_message'] = BL::getError('EmailValidationIsRequired');
            }
            if ($sendConfirmationMailTo && $validation !== 'email') {
                $errors['send_confirmation_mail_to_error_message'] = BL::getError(
                    'ActivateEmailValidationToUseThisOption'
                );
            }
            if ($sendConfirmationMailTo && empty($confirmationMailSubject)) {
                $errors['confirmation_mail_subject_error_message'] = BL::getError(
                    'ConfirmationSubjectIsEmpty'
                );
            }
        } elseif ($type === 'textarea') {
            // validate textarea
            if ($label === '') {
                $errors['label'] = BL::getError('LabelIsRequired');
            }
            if ($required && $requiredErrorMessage === '') {
                $errors['required_error_message'] = BL::getError('ErrorMessageIsRequired');
            }
            if ($validation !== '' && $errorMessage === '') {
                $errors['error_message'] = BL::getError('ErrorMessageIsRequired');
            }
        } elseif ($type === 'datetime') {
            // validate datetime
            if ($label === '') {
                $errors['label'] = BL::getError('LabelIsRequired');
            }
            if (in_array($valueType, ['day', 'week', 'month', 'year']) && $valueAmount === '') {
                $errors['default_value_error_message'] = BL::getError('ValueIsRequired');
            }
            if ($required && $requiredErrorMessage === '') {
                $errors['required_error_message'] = BL::getError('ErrorMessageIsRequired');
            }
            if ($validation !== '' && $errorMessage === '') {
                $errors['error_message'] = BL::getError('ErrorMessageIsRequired');
            }
        } elseif ($type === 'heading' && $values === '') {
            // validate heading
            $errors['values'] = BL::getError('ValueIsRequired');
        } elseif ($type === 'paragraph' && $values === '') {
            // validate paragraphs
            $errors['values'] = BL::getError('ValueIsRequired');
        } elseif ($type === 'submit' && $values === '') {
            // validate submitbuttons
            $errors['values'] = BL::getError('ValueIsRequired');
        } elseif ($type === 'dropdown') {
            // validate dropdown
            $values = trim($values, ',');

            // validate
            if ($label === '') {
                $errors['label'] = BL::getError('LabelIsRequired');
            }
            if ($required && $requiredErrorMessage === '') {
                $errors['required_error_message'] = BL::getError('ErrorMessageIsRequired');
            }
            if ($values === '') {
                $errors['values'] = BL::getError('ValueIsRequired');
            }
        } elseif ($type === 'radiobutton') {
            // validate radiobutton
            if ($label === '') {
                $errors['label'] = BL::getError('LabelIsRequired');
            }
            if ($required && $requiredErrorMessage === '') {
                $errors['required_error_message'] = BL::getError('ErrorMessageIsRequired');
            }
            if ($values === '') {
                $errors['values'] = BL::getError('ValueIsRequired');
            }
        } elseif ($type === 'checkbox') {
            // validate checkbox
            if ($label === '') {
                $errors['label'] = BL::getError('LabelIsRequired');
            }
            if ($required && $requiredErrorMessage === '') {
                $errors['required_error_message'] = BL::getError('ErrorMessageIsRequired');
            }
        }

        // got errors
        if (!empty($errors)) {
            $this->output(Response::HTTP_OK, ['errors' => $errors], 'form contains errors');

            return;
        }

        // htmlspecialchars except for paragraphs
        if ($type !== 'paragraph') {
            if ($values !== '') {
                $values = \SpoonFilter::htmlspecialchars($values);
            }
            if ($defaultValues !== '') {
                $defaultValues = \SpoonFilter::htmlspecialchars($defaultValues);
            }
        }

        // split
        if ($type === 'dropdown' || $type === 'checkbox') {
            $values = (array) explode('|', $values);
        } elseif ($type === 'radiobutton') {
            $postedValues = (array) explode('|', $values);
            $values = [];

            foreach ($postedValues as $postedValue) {
                $values[] = [
                    'value' => CommonUri::getUrl($postedValue),
                    'label' => $postedValue,
                ];
            }
            if ($defaultValues !== '') {
                $defaultValues = CommonUri::getUrl($defaultValues);
            }
        }

        /*
         * Save!
         */
        // settings
        $settings = [];
        if ($label !== '') {
            $settings['label'] = \SpoonFilter::htmlspecialchars($label);
        }
        if (isset($values)) {
            $settings['values'] = $values;
        }
        if ($defaultValues !== '') {
            $settings['default_values'] = $defaultValues;
        }
        if ($placeholder !== '') {
            $settings['placeholder'] = \SpoonFilter::htmlspecialchars($placeholder);
        }
        if ($classname !== '') {
            $settings['classname'] = \SpoonFilter::htmlspecialchars($classname);
        }

        // reply-to, only for textboxes
        if ($type === 'textbox') {
            $settings['reply_to'] = $replyTo;
            $settings['send_confirmation_mail_to'] = $sendConfirmationMailTo;
            $settings['confirmation_mail_subject'] = $confirmationMailSubject;
        }

        // only for datetime input
        if ($type === 'datetime') {
            $settings['input_type'] = $inputType;

            if ($inputType === 'date') {
                $settings['value_amount'] = $valueAmount;
                $settings['value_type'] = $valueType;
            }
        }

        // build array
        $field = [];
        $field['form_id'] = $formId;
        $field['type'] = $type;
        $field['settings'] = (!empty($settings) ? serialize($settings) : null);

        // existing field
        if ($fieldId !== 0) {
            // update field
            BackendFormBuilderModel::updateField($fieldId, $field);

            // delete all validation (added again later)
            BackendFormBuilderModel::deleteFieldValidation($fieldId);
        } else {
            // sequence
            $field['sequence'] = BackendFormBuilderModel::getMaximumSequence($formId) + 1;

            // insert
            $fieldId = BackendFormBuilderModel::insertField($field);
        }

        // required
        if ($required) {
            // build array
            $validate = [];
            $validate['field_id'] = $fieldId;
            $validate['type'] = 'required';
            $validate['error_message'] = \SpoonFilter::htmlspecialchars($requiredErrorMessage);

            // add validation
            BackendFormBuilderModel::insertFieldValidation($validate);

            // add to field (for parsing)
            $field['validations']['required'] = $validate;
        }

        // other validation
        if ($validation !== '') {
            // build array
            $validate['field_id'] = $fieldId;
            $validate['type'] = $validation;
            $validate['error_message'] = \SpoonFilter::htmlspecialchars($errorMessage);
            $validate['parameter'] = ($validationParameter !== '') ?
                \SpoonFilter::htmlspecialchars($validationParameter) :
                null;

            // add validation
            BackendFormBuilderModel::insertFieldValidation($validate);

            // add to field (for parsing)
            $field['validations'][$type] = $validate;
        }

        // get item from database (i do this call again to keep the pof as low as possible)
        $field = BackendFormBuilderModel::getField($fieldId);

        // submit button isnt parsed but handled directly via javascript
        $fieldHTML = $type === 'submit' ? '' : FormBuilderHelper::parseField($field);

        // success output
        $this->output(
            Response::HTTP_OK,
            [
                'field_id' => $fieldId,
                'field_html' => $fieldHTML,
            ],
            'field saved'
        );
    }
}
