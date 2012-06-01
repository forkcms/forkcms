<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the form_builder module
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FormBuilderInstaller extends ModuleInstaller
{
	/**
	 * Install the module
	 */
	public function install()
	{
		// load install.sql
		$this->importSQL(PATH_WWW . '/backend/modules/form_builder/installer/data/install.sql');

		// add as a module
		$this->addModule('form_builder');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// module rights
		$this->setModuleRights(1, 'form_builder');

		// action rights
		$this->setActionRights(1, 'form_builder', 'add');
		$this->setActionRights(1, 'form_builder', 'edit');
		$this->setActionRights(1, 'form_builder', 'delete');
		$this->setActionRights(1, 'form_builder', 'index');
		$this->setActionRights(1, 'form_builder', 'data');
		$this->setActionRights(1, 'form_builder', 'data_details');
		$this->setActionRights(1, 'form_builder', 'mass_data_action');
		$this->setActionRights(1, 'form_builder', 'get_field');
		$this->setActionRights(1, 'form_builder', 'delete_field');
		$this->setActionRights(1, 'form_builder', 'save_field');
		$this->setActionRights(1, 'form_builder', 'sequence');
		$this->setActionRights(1, 'form_builder', 'export_data');

		// set navigation
		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$this->setNavigation($navigationModulesId, 'FormBuilder', 'form_builder/index', array(
			'form_builder/add',
			'form_builder/edit',
			'form_builder/data',
			'form_builder/data_details'
		));

		// get search extra id
		$searchId = (int) $this->getDB()->getVar('SELECT id FROM modules_extras WHERE module = ? AND type = ? AND action = ?', array('search', 'widget', 'form'));

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// create form
			$form = array();
			$form['language'] = $language;
			$form['user_id'] = $this->getDefaultUserID();
			$form['name'] = SpoonFilter::ucfirst($this->getLocale('Contact', 'core', $language, 'lbl', 'frontend'));
			$form['method'] = 'database_email';
			$form['email'] = serialize(array($this->getVariable('email')));
			$form['success_message'] = $this->getLocale('ContactMessageSent', 'core', $language, 'msg', 'frontend');
			$form['identifier'] = 'contact-' . $language;
			$form['created_on'] = gmdate('Y-m-d H:i:s');
			$form['edited_on'] = gmdate('Y-m-d H:i:s');
			$formId = $this->getDB()->insert('forms', $form);

			// create submit button
			$field['form_id'] = $formId;
			$field['type'] = 'submit';
			$field['settings'] = serialize(array('values' => SpoonFilter::ucfirst($this->getLocale('Send', 'core', $language, 'lbl', 'frontend'))));
			$this->getDB()->insert('forms_fields', $field);

			// create name field
			$field['form_id'] = $formId;
			$field['type'] = 'textbox';
			$field['settings'] = serialize(array('label' => SpoonFilter::ucfirst($this->getLocale('Name', 'core', $language, 'lbl', 'frontend'))));
			$nameId = $this->getDB()->insert('forms_fields', $field);

			// name validation
			$validate['field_id'] = $nameId;
			$validate['type'] = 'required';
			$validate['error_message'] = $this->getLocale('NameIsRequired', 'core', $language, 'err', 'frontend');
			$this->getDB()->insert('forms_fields_validation', $validate);

			// create email field
			$field['form_id'] = $formId;
			$field['type'] = 'textbox';
			$field['settings'] = serialize(array('label' => SpoonFilter::ucfirst($this->getLocale('Email', 'core', $language, 'lbl', 'frontend'))));
			$emailId = $this->getDB()->insert('forms_fields', $field);

			// email validation
			$validate['field_id'] = $emailId;
			$validate['type'] = 'email';
			$validate['error_message'] = $this->getLocale('EmailIsInvalid', 'core', $language, 'err', 'frontend');
			$this->getDB()->insert('forms_fields_validation', $validate);

			// create message field
			$field['form_id'] = $formId;
			$field['type'] = 'textarea';
			$field['settings'] = serialize(array('label' => SpoonFilter::ucfirst($this->getLocale('Message', 'core', $language, 'lbl', 'frontend'))));
			$messageId = $this->getDB()->insert('forms_fields', $field);

			// name validation
			$validate['field_id'] = $messageId;
			$validate['type'] = 'required';
			$validate['error_message'] = $this->getLocale('MessageIsRequired', 'core', $language, 'err', 'frontend');
			$this->getDB()->insert('forms_fields_validation', $validate);

			// insert extra
			$extraId = $this->insertExtra('form_builder', 'widget', 'FormBuilder', 'form', serialize(array('language' => $form['language'], 'extra_label' => $form['name'], 'id' => $formId)), 'N', '400' . $formId);

			// insert contact page
			$this->insertPage(
				array('title' => SpoonFilter::ucfirst($this->getLocale('Contact', 'core', $language, 'lbl', 'frontend')),
				'parent_id' => 1,
				'language' => $language),
				null,
				array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/contact.txt'),
				array('extra_id' => $extraId, 'position' => 'main'),
				array('extra_id' => $searchId, 'position' => 'top')
			);
		}
	}
}
