<?php

namespace Backend\Modules\FormBuilder\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\FormBuilder\Engine\Helper as FormBuilderHelper;
use Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;
use Common\Uri as CommonUri;

/**
 * Save a field via ajax.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@wijs.be>
 */
class SaveField extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // get parameters
        $formId = \SpoonFilter::getPostValue('form_id', null, '', 'int');
        $fieldId = \SpoonFilter::getPostValue('field_id', null, '', 'int');
        $type = \SpoonFilter::getPostValue(
            'type',
            array('checkbox', 'dropdown', 'datetime', 'heading', 'paragraph', 'radiobutton', 'submit', 'textarea', 'textbox'),
            '',
            'string'
        );
        $label = trim(\SpoonFilter::getPostValue('label', null, '', 'string'));
        $values = trim(\SpoonFilter::getPostValue('values', null, '', 'string'));

        // this is somewhat a nasty hack, but it makes special chars work.
        $values = \SpoonFilter::htmlspecialcharsDecode($values);

        $defaultValues = trim(\SpoonFilter::getPostValue('default_values', null, '', 'string'));
        $placeholder = trim(\SpoonFilter::getPostValue('placeholder', null, '', 'string'));
        $required = \SpoonFilter::getPostValue('required', array('Y','N'), 'N', 'string');
        $requiredErrorMessage = trim(\SpoonFilter::getPostValue('required_error_message', null, '', 'string'));
        $validation = \SpoonFilter::getPostValue('validation', array('email', 'numeric', 'time'), '', 'string');
        $validationParameter = trim(\SpoonFilter::getPostValue('validation_parameter', null, '', 'string'));
        $errorMessage = trim(\SpoonFilter::getPostValue('error_message', null, '', 'string'));

        // special field for textbox: reply to
        $replyTo = \SpoonFilter::getPostValue('reply_to', array('Y','N'), 'N', 'string');

        // special fields for datetime
        $inputType = \SpoonFilter::getPostValue('input_type', array('date','time'), 'date', 'string');
        $valueAmount = trim(\SpoonFilter::getPostValue('value_amount', null, '', 'string'));
        $valueType = trim(\SpoonFilter::getPostValue('value_type', null, '', 'string'));

        // invalid form id
        if (!BackendFormBuilderModel::exists($formId)) {
            $this->output(self::BAD_REQUEST, null, 'form does not exist');
        } else {
            // invalid fieldId
            if ($fieldId !== 0 && !BackendFormBuilderModel::existsField($fieldId, $formId)) {
                $this->output(self::BAD_REQUEST, null, 'field does not exist');
            } else {
                // invalid type
                if ($type == '') {
                    $this->output(self::BAD_REQUEST, null, 'invalid type provided');
                } else {
                    // extra validation is only possible for textfields & datetime fields
                    if ($type != 'textbox' && $type != 'datetime') {
                        $validation = '';
                        $validationParameter = '';
                        $errorMessage = '';
                    }

                    // init
                    $errors = array();

                    // validate textbox
                    if ($type == 'textbox') {
                        if ($label == '') {
                            $errors['label'] = BL::getError('LabelIsRequired');
                        }
                        if ($required == 'Y' && $requiredErrorMessage == '') {
                            $errors['required_error_message'] = BL::getError('ErrorMessageIsRequired');
                        }
                        if ($validation != '' && $errorMessage == '') {
                            $errors['error_message'] = BL::getError('ErrorMessageIsRequired');
                        }
                        if ($replyTo == 'Y' && $validation != 'email') {
                            $errors['reply_to_error_message'] = BL::getError('EmailValidationIsRequired');
                        }
                    } elseif ($type == 'textarea') {
                        // validate textarea
                        if ($label == '') {
                            $errors['label'] = BL::getError('LabelIsRequired');
                        }
                        if ($required == 'Y' && $requiredErrorMessage == '') {
                            $errors['required_error_message'] = BL::getError('ErrorMessageIsRequired');
                        }
                        if ($validation != '' && $errorMessage == '') {
                            $errors['error_message'] = BL::getError('ErrorMessageIsRequired');
                        }
                    } elseif ($type == 'datetime') {
                        // validate datetime
                        if ($label == '') {
                            $errors['label'] = BL::getError('LabelIsRequired');
                        }
                        if (in_array($valueType, array('day','week','month','year')) && $valueAmount == '') {
                            $errors['default_value_error_message'] = BL::getError('ValueIsRequired');
                        }
                        if ($required == 'Y' && $requiredErrorMessage == '') {
                            $errors['required_error_message'] = BL::getError('ErrorMessageIsRequired');
                        }
                        if ($validation != '' && $errorMessage == '') {
                            $errors['error_message'] = BL::getError('ErrorMessageIsRequired');
                        }
                    } elseif ($type == 'heading' && $values == '') {
                        // validate heading
                        $errors['values'] = BL::getError('ValueIsRequired');
                    } elseif ($type == 'paragraph' && $values == '') {
                        // validate paragraphs
                        $errors['values'] = BL::getError('ValueIsRequired');
                    } elseif ($type == 'submit' && $values == '') {
                        // validate submitbuttons
                        $errors['values'] = BL::getError('ValueIsRequired');
                    } elseif ($type == 'dropdown') {
                        // validate dropdown
                        $values = trim($values, ',');

                        // validate
                        if ($label == '') {
                            $errors['label'] = BL::getError('LabelIsRequired');
                        }
                        if ($required == 'Y' && $requiredErrorMessage == '') {
                            $errors['required_error_message'] = BL::getError('ErrorMessageIsRequired');
                        }
                        if ($values == '') {
                            $errors['values'] = BL::getError('ValueIsRequired');
                        }
                    } elseif ($type == 'radiobutton') {
                        // validate radiobutton
                        if ($label == '') {
                            $errors['label'] = BL::getError('LabelIsRequired');
                        }
                        if ($required == 'Y' && $requiredErrorMessage == '') {
                            $errors['required_error_message'] = BL::getError('ErrorMessageIsRequired');
                        }
                        if ($values == '') {
                            $errors['values'] = BL::getError('ValueIsRequired');
                        }
                    } elseif ($type == 'checkbox') {
                        // validate checkbox
                        if ($label == '') {
                            $errors['label'] = BL::getError('LabelIsRequired');
                        }
                        if ($required == 'Y' && $requiredErrorMessage == '') {
                            $errors['required_error_message'] = BL::getError('ErrorMessageIsRequired');
                        }
                    }

                    // got errors
                    if (!empty($errors)) {
                        $this->output(self::OK, array('errors' => $errors), 'form contains errors');
                    } else {
                        // htmlspecialchars except for paragraphs
                        if ($type != 'paragraph') {
                            if ($values != '') {
                                $values = \SpoonFilter::htmlspecialchars($values);
                            }
                            if ($defaultValues != '') {
                                $defaultValues = \SpoonFilter::htmlspecialchars($defaultValues);
                            }
                        }

                        // split
                        if ($type == 'dropdown' || $type == 'checkbox') {
                            $values = (array) explode('|', $values);
                        } elseif ($type == 'radiobutton') {
                            $postedValues = (array) explode('|', $values);
                            $values = array();

                            foreach ($postedValues as $postedValue) {
                                $values[] = array(
                                    'value' => CommonUri::getUrl($postedValue),
                                    'label' => $postedValue
                                );
                            }
                            if ($defaultValues != '') {
                                $defaultValues = CommonUri::getUrl($defaultValues);
                            }
                        }

                        /**
                         * Save!
                         */
                        // settings
                        $settings = array();
                        if ($label != '') {
                            $settings['label'] = \SpoonFilter::htmlspecialchars($label);
                        }
                        if (isset($values)) {
                            $settings['values'] = $values;
                        }
                        if ($defaultValues != '') {
                            $settings['default_values'] = $defaultValues;
                        }
                        if ($placeholder != '') {
                            $settings['placeholder'] = \SpoonFilter::htmlspecialchars($placeholder);
                        }

                        // reply-to, only for textboxes
                        if ($type == 'textbox') {
                            $settings['reply_to'] = ($replyTo == 'Y');
                        }

                        // only for datetime input
                        if ($type == 'datetime') {
                            $settings['input_type'] = $inputType;

                            if ($inputType == 'date') {
                                $settings['value_amount'] = $valueAmount;
                                $settings['value_type'] = $valueType;
                            }
                        }

                        // build array
                        $field = array();
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
                        if ($required == 'Y') {
                            // build array
                            $validate['field_id'] = $fieldId;
                            $validate['type'] = 'required';
                            $validate['error_message'] = \SpoonFilter::htmlspecialchars($requiredErrorMessage);

                            // add validation
                            BackendFormBuilderModel::insertFieldValidation($validate);

                            // add to field (for parsing)
                            $field['validations']['required'] = $validate;
                        }

                        // other validation
                        if ($validation != '') {
                            // build array
                            $validate['field_id'] = $fieldId;
                            $validate['type'] = $validation;
                            $validate['error_message'] = \SpoonFilter::htmlspecialchars($errorMessage);
                            $validate['parameter'] = ($validationParameter != '') ?
                                \SpoonFilter::htmlspecialchars($validationParameter) :
                                null
                            ;

                            // add validation
                            BackendFormBuilderModel::insertFieldValidation($validate);

                            // add to field (for parsing)
                            $field['validations'][$type] = $validate;
                        }

                        // get item from database (i do this call again to keep the pof as low as possible)
                        $field = BackendFormBuilderModel::getField($fieldId);

                        // submit button isnt parsed but handled directly via javascript
                        if ($type == 'submit') {
                            $fieldHTML = '';
                        } else {
                            // parse field to html
                            $fieldHTML = FormBuilderHelper::parseField($field);
                        }

                        // success output
                        $this->output(
                            self::OK,
                            array(
                                'field_id' => $fieldId,
                                'field_html' => $fieldHTML,
                            ),
                            'field saved'
                        );
                    }
                }
            }
        }
    }
}
