<?php

/**
 * This is the form widget.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendFormBuilderWidgetForm extends FrontendBaseWidget
{
	/**
	 * Fields in HTML form.
	 *
	 * @var	array
	 */
	private $fieldsHTML;

	/**
	 * The form.
	 *
	 * @var FrontendForm
	 */
	private $frm;

	/**
	 * The form item.
	 *
	 * @var	array
	 */
	private $item;

	/**
	 * Create form action and strip the identifier parameter.
	 *
	 * We use this function to create the action for the form.
	 * This action cannot contain an identifier since these are used for statistics and failed form submits cannot be tracked.
	 *
	 * @return string
	 */
	private function createAction()
	{
		// pages
		$action = implode('/', $this->URL->getPages());

		// init parameters
		$parameters = $this->URL->getParameters();
		$moduleParameters = array();
		$getParameters = array();

		// sort by key (important for action order)
		ksort($parameters);

		// loop and filter parameters
		foreach($parameters as $key => $value)
		{
			// skip identifier
			if($key === 'identifier') continue;

			// normal parameter
			if(SpoonFilter::isInteger($key)) $moduleParameters[] = $value;

			// get parameter
			else $getParameters[$key] = $value;
		}

		// single language
		if(SITE_MULTILANGUAGE) $action = FRONTEND_LANGUAGE . '/' . $action;

		// add to action
		if(count($moduleParameters) > 0) $action .= '/' . implode('/', $moduleParameters);
		if(count($getParameters) > 0) $action .= '?' . http_build_query($getParameters);

		// remove trailing slash
		$action = rtrim($action, '/');

		// cough up action
		return SITE_URL . '/' . $action;
	}

	/**
	 * Execute the extra.
	 */
	public function execute()
	{
		parent::execute();

		$this->loadTemplate();
		$this->loadData();

		// success message
		if(isset($_GET['identifier']) && $_GET['identifier'] == $this->item['identifier']) $this->parseSuccessMessage();

		// create/handle form
		else
		{
			$this->loadForm();
			$this->validateForm();
			$this->parse();
		}

		return $this->tpl->getContent(FRONTEND_MODULES_PATH . '/' . $this->getModule() . '/layout/widgets/' . $this->getAction() . '.tpl');
	}

	/**
	 * Load the data.
	 */
	private function loadData()
	{
		// fetch the item
		$this->item = FrontendFormBuilderModel::get((int) $this->data['id']);
	}

	/**
	 * Load the form.
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new FrontendForm('form' . $this->item['id']);

		// exists and has fields
		if(!empty($this->item) && !empty($this->item['fields']))
		{
			// loop fields
			foreach($this->item['fields'] as $field)
			{
				// init
				$item['name'] = 'field' . $field['id'];
				$item['type'] = $field['type'];
				$item['label'] = (isset($field['settings']['label'])) ? $field['settings']['label'] : '';
				$item['required'] = isset($field['validations']['required']);
				$item['html'] = '';

				// form values
				$values = (isset($field['settings']['values']) ? $field['settings']['values'] : null);
				$defaultValues = (isset($field['settings']['default_values']) ? $field['settings']['default_values'] : null);

				// dropdown
				if($field['type'] == 'dropdown')
				{
					// values and labels are the same
					$values = array_combine($values, $values);

					// get index of selected item
					$defaultIndex = array_search($defaultValues, $values, true);
					if($defaultIndex === false) $defaultIndex = null;

					// create element
					$ddm = $this->frm->addDropdown($item['name'], $values, $defaultIndex);

					// empty default element
					$ddm->setDefaultElement('');

					// get content
					$item['html'] = $ddm->parse();
				}

				// radiobutton
				elseif($field['type'] == 'radiobutton')
				{
					// reset
					$newValues = array();

					// rebuild values
					foreach($values as $value) $newValues[] = array('label' => $value, 'value' => $value);

					// create element
					$rbt = $this->frm->addRadiobutton($item['name'], $newValues, $defaultValues);

					// get content
					$item['html'] = $rbt->parse();
				}

				// checkbox
				elseif($field['type'] == 'checkbox')
				{
					// reset
					$newValues = array();

					// rebuild values
					foreach($values as $value) $newValues[] = array('label' => $value, 'value' => $value);

					// create element
					$chk = $this->frm->addMultiCheckbox($item['name'], $newValues, $defaultValues);

					// get content
					$item['html'] = $chk->parse();
				}

				// textbox
				elseif($field['type'] == 'textbox')
				{
					// create element
					$txt = $this->frm->addText($item['name'], $defaultValues);

					// get content
					$item['html'] = $txt->parse();
				}

				// textarea
				elseif($field['type'] == 'textarea')
				{
					// create element
					$txt = $this->frm->addTextarea($item['name'], $defaultValues);
					$txt->setAttribute('cols', 30);

					// get content
					$item['html'] = $txt->parse();
				}

				// heading
				elseif($field['type'] == 'heading') $item['html'] = '<h3>' . $values . '</h3>';

				// paragraph
				elseif($field['type'] == 'paragraph') $item['html'] = $values;

				// submit
				elseif($field['type'] == 'submit') $item['html'] = $values;

				// add to list
				$this->fieldsHTML[] = $item;
			}
		}
	}

	/**
	 * Load the template.
	 *
	 * We create a new FrontendTemplate because we could have multiple form widgets on 1 page.
	 * Every form needs to have its own scope so error messages stay within the current scope.
	 * (Without an own scope the successMessage would show in all forms instead of just 1 form.)
	 *
	 * @param string[optional] $path Unused parameter but needed because parent function uses it.
	 */
	protected function loadTemplate($path = null)
	{
		$this->tpl = new FrontendTemplate(false);
	}

	/**
	 * Parse.
	 */
	private function parse()
	{
		// form name
		$this->tpl->assign('formName', 'form' . $this->item['id']);
		$this->tpl->assign('formAction', $this->createAction());

		// got fields
		if(!empty($this->fieldsHTML))
		{
			// value of the submit button
			$submitValue = '';

			// loop html fields
			foreach($this->fieldsHTML as &$field)
			{
				// plaintext items
				if($field['type'] == 'heading' || $field['type'] == 'paragraph') $field['plaintext'] = true;

				// multiple items
				elseif($field['type'] == 'checkbox' || $field['type'] == 'radiobutton')
				{
					// name (prefixed by type)
					$name = ($field['type'] == 'checkbox') ? 'chk' . SpoonFilter::toCamelCase($field['name']) : 'rbt' . SpoonFilter::toCamelCase($field['name']);

					// rebuild so the html is stored in a general name (and not rbtName)
					foreach($field['html'] as &$item) $item['field'] = $item[$name];

					// multiple items
					$field['multiple'] = true;
				}

				// submit button
				elseif($field['type'] == 'submit') $submitValue = $field['html'];

				// simple items
				else $field['simple'] = true;

				// errors (only for form elements)
				if(isset($field['simple']) || isset($field['multiple'])) $field['error'] = $this->frm->getField($field['name'])->getErrors();
			}

			// assign
			$this->tpl->assign('submitValue', $submitValue);
			$this->tpl->assign('fields', $this->fieldsHTML);

			// parse form
			$this->frm->parse($this->tpl);

			// assign form error
			$this->tpl->assign('error', ($this->frm->getErrors() != '' ? $this->frm->getErrors() : false));
		}
	}

	/**
	 * Parse the success message.
	 */
	private function parseSuccessMessage()
	{
		$this->tpl->assign('successMessage', $this->item['success_message']);
	}

	/**
	 * Validate the form.
	 */
	private function validateForm()
	{
		// submitted
		if($this->frm->isSubmitted())
		{
			// does the key exists?
			if(SpoonSession::exists('formbuilder_' . $this->item['id']))
			{
				// calculate difference
				$diff = time() - (int) SpoonSession::get('formbuilder_' . $this->item['id']);

				// calculate difference, it it isn't 10 seconds the we tell the user to slow down
				if($diff < 10 && $diff != 0) $this->frm->addError(FL::err('FormTimeout'));
			}

			// validate fields
			foreach($this->item['fields'] as $field)
			{
				// fieldname
				$fieldName = 'field' . $field['id'];

				// skip
				if($field['type'] == 'submit' || $field['type'] == 'paragraph' || $field['type'] == 'heading') continue;

				// loop other validations
				foreach($field['validations'] as $rule => $settings)
				{
					// already has an error so skip
					if($this->frm->getField($fieldName)->getErrors() !== null) continue;

					// required
					if($rule == 'required') $this->frm->getField($fieldName)->isFilled($settings['error_message']);

					// email
					elseif($rule == 'email')
					{
						// only check this if the field is filled, if the field is required it will be validated before
						if($this->frm->getField($fieldName)->isFilled()) $this->frm->getField($fieldName)->isEmail($settings['error_message']);
					}

					// numeric
					elseif($rule == 'numeric')
					{
						// only check this if the field is filled, if the field is required it will be validated before
						if($this->frm->getField($fieldName)->isFilled()) $this->frm->getField($fieldName)->isNumeric($settings['error_message']);
					}
				}
			}

			// valid form
			if($this->frm->isCorrect())
			{
				// item
				$data['form_id'] = $this->item['id'];
				$data['session_id'] = SpoonSession::getSessionId();
				$data['sent_on'] = FrontendModel::getUTCDate();
				$data['data'] = serialize(array('server' => $_SERVER));

				// insert data
				$dataId = FrontendFormBuilderModel::insertData($data);

				// init fields array
				$fields = array();

				// loop all fields
				foreach($this->item['fields'] as $field)
				{
					// skip
					if($field['type'] == 'submit' || $field['type'] == 'paragraph' || $field['type'] == 'heading') continue;

					// field data
					$fieldData['data_id'] = $dataId;
					$fieldData['label'] = $field['settings']['label'];
					$fieldData['value'] = $this->frm->getField('field' . $field['id'])->getValue();

					// prepare fields for email
					if($this->item['method'] == 'database_email')
					{
						// add field for email
						$emailFields[] = array('label' => $field['settings']['label'],
												'value' => (is_array($fieldData['value']) ? implode(',', $fieldData['value']) : nl2br($fieldData['value'])));
					}

					// clean up
					if(is_array($fieldData['value']) && empty($fieldData['value'])) $fieldData['value'] = null;

					// serialize
					if($fieldData['value'] !== null) $fieldData['value'] = serialize($fieldData['value']);

					// save fields data
					$fields[] = $fieldData;

					// insert
					FrontendFormBuilderModel::insertDataField($fieldData);
				}

				// need to send mail
				if($this->item['method'] == 'database_email')
				{
					// build variables
					$variables['sentOn'] = time();
					$variables['name'] = $this->item['name'];
					$variables['fields'] = $emailFields;

					// loop recipients
					foreach($this->item['email'] as $address)
					{
						// add email
						FrontendMailer::addEmail(sprintf(FL::getMessage('FormBuilderSubject'), $this->item['name']), FRONTEND_MODULES_PATH . '/form_builder/layout/templates/mails/form.tpl', $variables, $address, $this->item['name']);
					}
				}

				// trigger event
				FrontendModel::triggerEvent('form_builder', 'after_submission', array('form_id' => $this->item['id'], 'data_id' => $dataId, 'data' => $data, 'fields' => $fields, 'visitorId' => FrontendModel::getVisitorId()));

				// store timestamp in session so we can block excesive usage
				SpoonSession::set('formbuilder_' . $this->item['id'], time());

				// redirect
				$redirect = SITE_URL . '/' . $this->URL->getQueryString();
				$redirect .= (stripos($redirect, '?') === false) ? '?' : '&';
				$redirect .= 'identifier=' . $this->item['identifier'];

				// redirect with identifier
				SpoonHTTP::redirect($redirect);
			}

			// not correct, show errors
			else
			{
				// global form errors set
				if($this->frm->getErrors() != '') $this->tpl->assign('formBuilderError', $this->frm->getErrors());

				// general error
				else $this->tpl->assign('formBuilderError', FL::err('FormError'));
			}
		}
	}
}
