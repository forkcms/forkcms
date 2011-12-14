<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the form_builder module
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendFormBuilderModel
{
	const QRY_BROWSE =
		'SELECT i.id, i.name, i.email, i.method,
		 (SELECT COUNT(fd.form_id) FROM forms_data AS fd WHERE fd.form_id = i.id) AS sent_forms
		 FROM forms AS i
		 WHERE i.language = ?';

	/**
	 * Calculate time ago.
	 *
	 * @param int $timestamp Unix timestamp from the past.
	 * @return string
	 */
	public static function calculateTimeAgo($timestamp)
	{
		$secondsBetween = time() - $timestamp;

		// calculate
		$hours = floor($secondsBetween / (60 * 60));
		$minutes = floor($secondsBetween / 60);
		$seconds = floor($secondsBetween);

		// today start
		$todayStart = (int) strtotime(date('d F Y'));

		// today
		if($timestamp >= $todayStart)
		{
			// today
			if($hours >= 1) return BL::getLabel('Today') . ' ' . date('H:i', $timestamp);

			// more than one minute
			elseif($minutes > 1) return sprintf(BL::getLabel('MinutesAgo'), $minutes);

			// one minute
			elseif($minutes == 1) return BL::getLabel('OneMinuteAgo');

			// more than one seconde
			elseif($seconds > 1) return sprintf(BL::getLabel('SecondsAgo'), $seconds);

			// one second
			elseif($seconds <= 1) return BL::getLabel('OneSecondAgo');
		}

		// yesterday
		elseif($timestamp < $todayStart && $timestamp >= ($todayStart - 86400)) return BL::getLabel('Yesterday') . ' ' . date('H:i', $timestamp);

		// older
		else return date('d/m/Y H:i', $timestamp);
	}

	/**
	 * Create an unique identifier.
	 *
	 * @return string
	 */
	public static function createIdentifier()
	{
		// get last id
		$id = (int) BackendModel::getDb()->getVar('SELECT i.id FROM forms AS i ORDER BY i.id DESC LIMIT 1');

		// create identifier
		do
		{
			$id++;
			$identifier = 'form' . $id;
		}

		// @todo refactor me...
		// keep trying till its unique
		while((int) BackendModel::getDb()->getVar('SELECT COUNT(i.id) FROM forms AS i WHERE i.identifier = ?', $identifier) > 0);

		return $identifier;
	}

	/**
	 * Delete an item.
	 *
	 * @param int $id The id of the record to delete.
	 */
	public static function delete($id)
	{
		$id = (int) $id;
		$db = BackendModel::getDB(true);

		// get field ids
		$fieldIds = (array) $db->getColumn('SELECT i.id FROM forms_fields AS i WHERE i.form_id = ?', $id);

		// we have items to be deleted
		if(!empty($fieldIds))
		{
			// delete all fields
			$db->delete('forms_fields', 'form_id = ?', $id);
			$db->delete('forms_fields_validation', 'field_id IN(' . implode(',', $fieldIds) . ')');
		}

		// get data ids
		$dataIds = (array) $db->getColumn('SELECT i.id FROM forms_data AS i WHERE i.form_id = ?', $id);

		// we have items to be deleted
		if(!empty($dataIds)) self::deleteData($dataIds);

		// delete extra
		BackendModel::deleteExtra('form_builder', 'widget', array('id' => $id));

		// delete form
		$db->delete('forms', 'id = ?', $id);
	}

	/**
	 * Deletes one or more data items.
	 *
	 * @param array $ids Ids of data items.
	 */
	public static function deleteData(array $ids)
	{
		$db = BackendModel::getDB(true);

		$db->delete('forms_data', 'id IN(' . implode(',', $ids) . ')');
		$db->delete('forms_data_fields', 'data_id IN(' . implode(',', $ids) . ')');
	}

	/**
	 * Delete a field.
	 *
	 * @param int $id Id of a field.
	 */
	public static function deleteField($id)
	{
		$id = (int) $id;

		// delete linked validation
		self::deleteFieldValidation($id);

		// delete field
		BackendModel::getDB(true)->delete('forms_fields', 'id = ?', $id);
	}

	/**
	 * Delete all validation of a field.
	 *
	 * @param int $id Id of a field.
	 */
	public static function deleteFieldValidation($id)
	{
		BackendModel::getDB(true)->delete('forms_fields_validation', 'field_id = ?', (int) $id);
	}

	/**
	 * Does the item exist.
	 *
	 * @param int $id Id of a form.
	 * @return bool
	 */
	public static function exists($id)
	{
		return (BackendModel::getDB()->getVar('SELECT COUNT(f.id) FROM forms AS f WHERE f.id = ?', (int) $id) >= 1);
	}

	/**
	 * Does the data item exist.
	 *
	 * @param int $id Id of the data item.
	 * @return bool
	 */
	public static function existsData($id)
	{
		return (BackendModel::getDB()->getVar('SELECT COUNT(fd.id) FROM forms_data AS fd WHERE fd.id = ?', (int) $id) >= 1);
	}

	/**
	 * Does a field exist (within a form).
	 *
	 * @param int $id Id of a field.
	 * @param int[optional] $formId Id of a form.
	 * @return bool
	 */
	public static function existsField($id, $formId = null)
	{
		$id = (int) $id;

		// exists
		if($formId === null) return (BackendModel::getDB()->getVar('SELECT COUNT(ff.id) FROM forms_fields AS ff WHERE ff.id = ?', $id) >= 1);

		// exists and ignore an id
		return (BackendModel::getDB()->getVar('SELECT COUNT(ff.id) FROM forms_fields AS ff WHERE ff.id = ? AND ff.form_id = ?', array($id, (int) $formId)) >= 1);
	}

	/**
	 * Does an identifier exist.
	 *
	 * @param string $identifier Identifier.
	 * @param in[optional] $ignoreId Field id to ignore.
	 * @return bool
	 */
	public static function existsIdentifier($identifier, $ignoreId = null)
	{
		$identifier = (string) $identifier;

		// exists
		if($ignoreId === null) return (BackendModel::getDB()->getVar('SELECT COUNT(f.id) FROM forms AS f WHERE f.identifier = ?', $identifier) >= 1);

		// exists and ignore an id
		else return (BackendModel::getDB()->getVar('SELECT COUNT(f.id) FROM forms AS f WHERE f.identifier = ? AND f.id != ?', array($identifier, (int) $ignoreId)) >= 1);
	}

	/**
	 * Formats the recipients based on the serialized string
	 *
	 * @param string $string The serialized string that should be formated
	 * @return string
	 */
	public static function formatRecipients($string)
	{
		return implode(', ', (array) @unserialize((string) $string));
	}

	/**
	 * Get all data for a given id.
	 *
	 * @param int $id The id for the record to get.
	 * @return array
	 */
	public static function get($id)
	{
		$return = (array) BackendModel::getDB()->getRecord('SELECT f.*	FROM forms AS f WHERE f.id = ?', (int) $id);

		// unserialize the emailaddresses
		if(isset($return['email'])) $return['email'] = (array) unserialize($return['email']);

		return $return;
	}

	/**
	 * Get data for a given id.
	 *
	 * @param int $id The id for the record to get.
	 * @return array
	 */
	public static function getData($id)
	{
		// get data
		$data = (array) BackendModel::getDB()->getRecord(
			'SELECT fd.id, fd.form_id, UNIX_TIMESTAMP(fd.sent_on) AS sent_on
			 FROM forms_data AS fd
			 WHERE fd.id = ?',
			(int) $id
		);

		// get fields
		$data['fields'] = (array) BackendModel::getDB()->getRecords(
			'SELECT fdf.label, fdf.value
			 FROM forms_data_fields AS fdf
			 WHERE fdf.data_id = ?',
			(int) $data['id']
		);

		// unserialize values
		foreach($data['fields'] as &$field)
		{
			if($field['value'] !== null)
			{
				$field['value'] = unserialize($field['value']);
			}
		}

		return $data;
	}

	/**
	 * Get errors (optional by type).
	 *
	 * @param string[optional] $type Type of error.
	 * @return mixed
	 */
	public static function getErrors($type = null)
	{
		$errors['required'] = FL::getError('FieldIsRequired');
		$errors['email'] = FL::getError('EmailIsInvalid');
		$errors['numeric'] = FL::getError('NumericCharactersOnly');

		// specific type
		if($type !== null)
		{
			$type = (string) $type;
			return $errors[$type];
		}

		// all errors
		else
		{
			$return = array();

			// loop errors
			foreach($errors as $key => $error) $return[] = array('type' => $key, 'message' => $error);

			return $return;
		}
	}

	/**
	 * Get a field.
	 *
	 * @param int $id Id of a field.
	 * @return array
	 */
	public static function getField($id)
	{
		$field = (array) BackendModel::getDB()->getRecord(
			'SELECT ff.id, ff.form_id, ff.type, ff.settings
			 FROM forms_fields AS ff
			 WHERE ff.id = ?',
			(int) $id
		);

		// unserialize settings
		if($field['settings'] !== null) $field['settings'] = unserialize($field['settings']);

		// get validation
		$field['validations'] = (array) BackendModel::getDB()->getRecords(
			'SELECT ffv.type, ffv.parameter, ffv.error_message
			 FROM forms_fields_validation AS ffv
			 WHERE ffv.field_id = ?',
			$field['id'], 'type'
		);

		return $field;
	}

	/**
	 * Get all fields of a form.
	 *
	 * @param int $id Id of a form.
	 * @return array
	 */
	public static function getFields($id)
	{
		$fields = (array) BackendModel::getDB()->getRecords(
			'SELECT ff.id, ff.type, ff.settings
			 FROM forms_fields AS ff
			 WHERE ff.form_id = ?
			 ORDER BY ff.sequence ASC',
			(int) $id
		);

		foreach($fields as &$field)
		{
			// unserialize
			if($field['settings'] !== null) $field['settings'] = unserialize($field['settings']);

			// get validation
			$field['validations'] = (array) BackendModel::getDB()->getRecords(
				'SELECT ffv.type, ffv.parameter, ffv.error_message
				 FROM forms_fields_validation AS ffv
				 WHERE ffv.field_id = ?', $field['id'],
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
	 * @param string[optional] $type Type of locale item.
	 * @param string[optional] $application Name of the application.
	 * @return string
	 */
	public static function getLocale($name, $type = 'label', $application = 'backend')
	{
		$name = SpoonFilter::toCamelCase($name);
		$class = SpoonFilter::ucfirst($application) . 'Language';
		$function = 'get' . SpoonFilter::ucfirst($type);

		// execute and return value
		return SpoonFilter::ucfirst(call_user_func_array(array($class, $function), array($name)));
	}

	/**
	 * Get the maximum sequence for fields in a form.
	 *
	 * @param int $formId Id of the form.
	 * @return int
	 */
	public static function getMaximumSequence($formId)
	{
		return (int) BackendModel::getDB()->getVar(
			'SELECT MAX(ff.sequence)
			 FROM forms_fields AS ff
			 WHERE ff.form_id = ?',
			(int) $formId
		);
	}

	/**
	 * Add a new item.
	 *
	 * @param array $values The data to insert.
	 * @return int
	 */
	public static function insert(array $values)
	{
		$insertId = BackendModel::getDB(true)->insert('forms', $values);

		// build array
		$extra['module'] = 'form_builder';
		$extra['type'] = 'widget';
		$extra['label'] = 'FormBuilder';
		$extra['action'] = 'form';
		$extra['data'] = serialize(array('language' => $values['language'], 'extra_label' => $values['name'], 'id' => $insertId, 'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $insertId));
		$extra['hidden'] = 'N';
		$extra['sequence'] = '400' . $insertId;

		// insert extra
		BackendModel::getDB(true)->insert('modules_extras', $extra);

		return $insertId;
	}

	/**
	 * Add a new field.
	 *
	 * @param array $values The data to insert.
	 * @return int
	 */
	public static function insertField(array $values)
	{
		return BackendModel::getDB(true)->insert('forms_fields', $values);
	}

	/**
	 * Add validation for a field.
	 *
	 * @param array $values The data to insert.
	 * @return int
	 */
	public static function insertFieldValidation(array $values)
	{
		return BackendModel::getDB(true)->insert('forms_fields_validation', $values);
	}

	/**
	 * Update an existing item.
	 *
	 * @param int $id The id for the item to update.
	 * @param array $values The new data.
	 * @return int
	 */
	public static function update($id, array $values)
	{
		$id = (int) $id;
		$db = BackendModel::getDB(true);

		// update item
		$db->update('forms', $values, 'id = ?', $id);

		// build array
		$extra['data'] = serialize(array('language' => BL::getWorkingLanguage(), 'extra_label' => $values['name'], 'id' => $id));

		// update extra
		$db->update('modules_extras', $extra, 'module = ? AND type = ? AND sequence = ?', array('form_builder', 'widget', '400' . $id));

		return $id;
	}

	/**
	 * Update a field.
	 *
	 * @param int $id The id for the item to update.
	 * @param array $values The new data.
	 * @return int
	 */
	public static function updateField($id, array $values)
	{
		BackendModel::getDB(true)->update('forms_fields', $values, 'id = ?', (int) $id);
		return $id;
	}
}

/**
 * Helper class for the form_builder module.
 *
 * @todo this class should be in helper.php like the other modules do
 *
 * Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class FormBuilderHelper
{
	/**
	 * Parse a field and return the HTML.
	 *
	 * @param array $field Field data.
	 * @return string
	 */
	public static function parseField(array $field)
	{
		if(!empty($field))
		{
			// init
			$frm = new BackendForm('tmp', '');
			$tpl = (Spoon::exists('template') ? Spoon::get('template') : new BackendTemplate());
			$fieldHTML = '';
			$fieldName = 'field' . $field['id'];
			$values = (isset($field['settings']['values']) ? $field['settings']['values'] : null);
			$defaultValues = (isset($field['settings']['default_values']) ? $field['settings']['default_values'] : null);

			/**
			 * Create form and parse to HTML
			 */
			// dropdown
			if($field['type'] == 'dropdown')
			{
				// get index of selected item
				$defaultIndex = array_search($defaultValues, $values, true);
				if($defaultIndex === false) $defaultIndex = null;

				// create element
				$ddm = $frm->addDropdown($fieldName, $values, $defaultIndex);

				// empty default element
				$ddm->setDefaultElement('');

				// get content
				$fieldHTML = $ddm->parse();
			}

			// radiobutton
			elseif($field['type'] == 'radiobutton')
			{
				// rebuild values
				foreach($values as $value) $newValues[] = array('label' => $value, 'value' => $value);

				// create element
				$rbt = $frm->addRadiobutton($fieldName, $newValues, $defaultValues);

				// get content
				$fieldHTML = $rbt->parse();
			}

			// checkbox
			elseif($field['type'] == 'checkbox')
			{
				// rebuild values
				foreach($values as $value) $newValues[] = array('label' => $value, 'value' => $value);

				// create element
				$chk = $frm->addMultiCheckbox($fieldName, $newValues, $defaultValues);

				// get content
				$fieldHTML = $chk->parse();
			}

			// textbox
			elseif($field['type'] == 'textbox')
			{
				// create element
				$txt = $frm->addText($fieldName, $defaultValues);
				$txt->setAttribute('disabled', 'disabled');

				// get content
				$fieldHTML = $txt->parse();
			}

			// textarea
			elseif($field['type'] == 'textarea')
			{
				// create element
				$txt = $frm->addTextarea($fieldName, $defaultValues);
				$txt->setAttribute('cols', 30);
				$txt->setAttribute('disabled', 'disabled');

				// get content
				$fieldHTML = $txt->parse();
			}

			// heading
			elseif($field['type'] == 'heading') $fieldHTML = '<h3>' . $values . '</h3>';

			// paragraph
			elseif($field['type'] == 'paragraph') $fieldHTML = $values;

			/**
			 * Parse the field into the template
			 */
			// init
			$tpl->assign('plaintext', false);
			$tpl->assign('simple', false);
			$tpl->assign('multiple', false);
			$tpl->assign('id', $field['id']);
			$tpl->assign('required', isset($field['validations']['required']));

			// plaintext items
			if($field['type'] == 'heading' || $field['type'] == 'paragraph')
			{
				// assign
				$tpl->assign('content', $fieldHTML);
				$tpl->assign('plaintext', true);
			}

			// multiple items
			elseif($field['type'] == 'checkbox' || $field['type'] == 'radiobutton')
			{
				// name (prefixed by type)
				$name = ($field['type'] == 'checkbox') ? 'chk' . SpoonFilter::ucfirst($fieldName) : 'rbt' . SpoonFilter::ucfirst($fieldName);

				// rebuild so the html is stored in a general name (and not rbtName)
				foreach($fieldHTML as &$item) $item['field'] = $item[$name];

				// show multiple
				$tpl->assign('label', $field['settings']['label']);
				$tpl->assign('items', $fieldHTML);
				$tpl->assign('multiple', true);
			}

			// simple items
			else
			{
				// assign
				$tpl->assign('label', $field['settings']['label']);
				$tpl->assign('field', $fieldHTML);
				$tpl->assign('simple', true);
			}

			return $tpl->getContent(BACKEND_MODULE_PATH . '/layout/templates/field.tpl');
		}

		// empty field so return empty string
		else return '';
	}
}
