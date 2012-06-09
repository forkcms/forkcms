<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Save a field via ajax.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendFormBuilderAjaxSaveField extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// get parameters
		$formId = SpoonFilter::getPostValue('form_id', null, '', 'int');
		$fieldId = SpoonFilter::getPostValue('field_id', null, '', 'int');
		$type = SpoonFilter::getPostValue('type', array('checkbox', 'dropdown', 'heading', 'paragraph', 'radiobutton', 'submit', 'textarea', 'textbox'), '', 'string');
		$label = trim(SpoonFilter::getPostValue('label', null, '', 'string'));
		$values = trim(SpoonFilter::getPostValue('values', null, '', 'string'));
		$defaultValues = trim(SpoonFilter::getPostValue('default_values', null, '', 'string'));
		$required = SpoonFilter::getPostValue('required', array('Y','N'), 'N', 'string');
		$requiredErrorMessage = trim(SpoonFilter::getPostValue('required_error_message', null, '', 'string'));
		$validation = SpoonFilter::getPostValue('validation', array('email', 'numeric'), '', 'string');
		$validationParameter = trim(SpoonFilter::getPostValue('validation_parameter', null, '', 'string'));
		$errorMessage = trim(SpoonFilter::getPostValue('error_message', null, '', 'string'));

		// invalid form id
		if(!BackendFormBuilderModel::exists($formId)) $this->output(self::BAD_REQUEST, null, 'form does not exist');

		// invalid fieldId
		if($fieldId !== 0 && !BackendFormBuilderModel::existsField($fieldId, $formId)) $this->output(self::BAD_REQUEST, null, 'field does not exist');

		// invalid type
		if($type == '') $this->output(self::BAD_REQUEST, null, 'invalid type provided');

		// init
		$errors = array();

		// validate textbox
		if($type == 'textbox')
		{
			if($label == '') $errors['label'] = BL::getError('LabelIsRequired');
			if($required == 'Y' && $requiredErrorMessage == '') $errors['required_error_message'] = BL::getError('ErrorMessageIsRequired');
			if($validation != '' && $errorMessage == '') $errors['error_message'] = BL::getError('ErrorMessageIsRequired');
		}

		// validate textarea
		elseif($type == 'textarea')
		{
			if($label == '') $errors['label'] = BL::getError('LabelIsRequired');
			if($required == 'Y' && $requiredErrorMessage == '') $errors['required_error_message'] = BL::getError('ErrorMessageIsRequired');
			if($validation != '' && $errorMessage == '') $errors['error_message'] = BL::getError('ErrorMessageIsRequired');
		}

		// validate heading
		elseif($type == 'heading' && $values == '') $errors['values'] = BL::getError('ValueIsRequired');

		// validate paragraph
		elseif($type == 'paragraph' && $values == '') $errors['values'] = BL::getError('ValueIsRequired');

		// validate submit button
		elseif($type == 'submit' && $values == '') $errors['values'] = BL::getError('ValueIsRequired');

		// validate dropdown
		elseif($type == 'dropdown')
		{
			// values trim
			$values = trim($values, ',');

			// validate
			if($label == '') $errors['label'] = BL::getError('LabelIsRequired');
			if($required == 'Y' && $requiredErrorMessage == '') $errors['required_error_message'] = BL::getError('ErrorMessageIsRequired');
			if($values == '') $errors['values'] = BL::getError('ValueIsRequired');
		}

		// validate radiobutton
		elseif($type == 'radiobutton')
		{
			if($label == '') $errors['label'] = BL::getError('LabelIsRequired');
			if($required == 'Y' && $requiredErrorMessage == '') $errors['required_error_message'] = BL::getError('ErrorMessageIsRequired');
			if($values == '') $errors['values'] = BL::getError('ValueIsRequired');
		}

		// validate checkbox
		elseif($type == 'checkbox')
		{
			if($label == '') $errors['label'] = BL::getError('LabelIsRequired');
			if($required == 'Y' && $requiredErrorMessage == '') $errors['required_error_message'] = BL::getError('ErrorMessageIsRequired');
		}

		// got errors
		if(!empty($errors)) $this->output(self::OK, array('errors' => $errors), 'form contains errors');

		// htmlspecialchars except for paragraphs
		if($type != 'paragraph')
		{
			if($values != '') $values = SpoonFilter::htmlspecialchars($values);
			if($defaultValues != '') $defaultValues = SpoonFilter::htmlspecialchars($defaultValues);
		}

		// split
		if($type == 'dropdown' || $type == 'radiobutton' || $type == 'checkbox') $values = (array) explode('|', $values);

		/**
		 * Save!
		 */
		// settings
		$settings = array();
		if($label != '') $settings['label'] = SpoonFilter::htmlspecialchars($label);
		if($values != '') $settings['values'] = $values;
		if($defaultValues != '') $settings['default_values'] = $defaultValues;

		// build array
		$field = array();
		$field['form_id'] = $formId;
		$field['type'] = $type;
		$field['settings'] = (!empty($settings) ? serialize($settings) : null);

		// existing field
		if($fieldId !== 0)
		{
			// update field
			BackendFormBuilderModel::updateField($fieldId, $field);

			// delete all validation (added again later)
			BackendFormBuilderModel::deleteFieldValidation($fieldId);
		}

		// create one
		else
		{
			// sequence
			$field['sequence'] = BackendFormBuilderModel::getMaximumSequence($formId) + 1;

			// insert
			$fieldId = BackendFormBuilderModel::insertField($field);
		}

		// required
		if($required == 'Y')
		{
			// build array
			$validate['field_id'] = $fieldId;
			$validate['type'] = 'required';
			$validate['error_message'] = SpoonFilter::htmlspecialchars($requiredErrorMessage);

			// add validation
			BackendFormBuilderModel::insertFieldValidation($validate);

			// add to field (for parsing)
			$field['validations']['required'] = $validate;
		}

		// other validation
		if($validation != '')
		{
			// build array
			$validate['field_id'] = $fieldId;
			$validate['type'] = $validation;
			$validate['error_message'] = SpoonFilter::htmlspecialchars($errorMessage);
			$validate['parameter'] = ($validationParameter != '') ? SpoonFilter::htmlspecialchars($validationParameter) : null;

			// add validation
			BackendFormBuilderModel::insertFieldValidation($validate);

			// add to field (for parsing)
			$field['validations'][$type] = $validate;
		}

		// get item from database (i do this call again to keep the points of failure as low as possible)
		$field = BackendFormBuilderModel::getField($fieldId);

		// submit button isnt parsed but handled directly via javascript
		if($type == 'submit') $fieldHTML = '';

		// parse field to html
		else $fieldHTML = FormBuilderHelper::parseField($field);

		// success output
		$this->output(self::OK, array('field_id' => $fieldId, 'field_html' => $fieldHTML), 'field saved');
	}
}
