<?php

/**
 * In this file we store all generic functions that we will be using in the form_builder module
 *
 * @package		backend
 * @subpackage	form_builder
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendFormBuilderModel
{
	/**
	 * Overview of items.
	 *
	 * @var	string
	 */
	const QRY_BROWSE = 'SELECT i.id, i.name, i.email, i.method,
							(SELECT COUNT(fd.form_id) FROM forms_data AS fd WHERE fd.form_id = i.id) AS sent_forms
						FROM forms AS i
						WHERE i.language = ?';


	/**
	 * Calculate time ago.
	 *
	 * @return	string
	 * @param	int $timestamp		Unix timestamp from the past.
	 */
	public static function calculateTimeAgo($timestamp)
	{
		// calculate difference
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

			// more then one minute
			elseif($minutes > 1) return sprintf(BL::getLabel('MinutesAgo'), $minutes);

			// one minute
			elseif($minutes == 1) return BL::getLabel('OneMinuteAgo');

			// more then one seconde
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
	 * @return	string
	 */
	public static function createIdentifier()
	{
		// get last id
		$id = (int) BackendModel::getDb()->getVar('SELECT i.id FROM forms AS i ORDER BY i.id DESC LIMIT 1');

		// create identifier
		do
		{
			// increase the id
			$id++;

			// create identifier
			$identifier = 'form' . $id;
		}

		// keep trying till its unique
		while((int) BackendModel::getDb()->getVar('SELECT COUNT(i.id) FROM forms AS i WHERE i.identifier = ?', $identifier) > 0);

		// unique identifier
		return $identifier;
	}


	/**
	 * Delete an item.
	 *
	 * @return	void
	 * @param	int $id		The id of the record to delete.
	 */
	public static function delete($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB(true);

		// get field ids
		$fieldIds = (array) $db->getColumn('SELECT i.id FROM forms_fields AS i WHERE i.form_id = ?', $id);

		// we have items to be deleted
		if(!empty($fieldIds))
		{
			// delete all fields
			$db->delete('forms_fields', 'form_id = ?', $id);
			$db->delete('forms_fields_validation', 'field_id IN(' . implode(',', $fieldIds) . ');');
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
	 * @return	void
	 * @param	array $ids		Ids of data items.
	 */
	public static function deleteData(array $ids)
	{
		// get db
		$db = BackendModel::getDB(true);

		// update record
		$db->delete('forms_data', 'id IN(' . implode(',', $ids) . ')');
		$db->delete('forms_data_fields', 'data_id IN(' . implode(',', $ids) . ')');
	}


	/**
	 * Delete a field.
	 *
	 * @return	void
	 * @param	int $id		Id of a field.
	 */
	public static function deleteField($id)
	{
		// redefine
		$id = (int) $id;

		// delete linked validation
		self::deleteFieldValidation($id);

		// delete field
		BackendModel::getDB(true)->delete('forms_fields', 'id = ?', $id);
	}


	/**
	 * Delete all validation of a field.
	 *
	 * @return	void
	 * @param	int $id		Id of a field.
	 */
	public static function deleteFieldValidation($id)
	{
		BackendModel::getDB(true)->delete('forms_fields_validation', 'field_id = ?', (int) $id);
	}


	/**
	 * Does the item exist.
	 *
	 * @return	bool
	 * @param	int $id		Id of a form.
	 */
	public static function exists($id)
	{
		return (BackendModel::getDB()->getVar('SELECT COUNT(f.id) FROM forms AS f WHERE f.id = ?', (int) $id) >= 1);
	}


	/**
	 * Does the data item exist.
	 *
	 * @return	bool
	 * @param	int $id		Id of the data item.
	 */
	public static function existsData($id)
	{
		return (BackendModel::getDB()->getVar('SELECT COUNT(fd.id) FROM forms_data AS fd WHERE fd.id = ?', (int) $id) >= 1);
	}


	/**
	 * Does a field exist (within a form).
	 *
	 * @return	bool
	 * @param	int $id					Id of a field.
	 * @param	int[optional] $formId	Id of a form.
	 */
	public static function existsField($id, $formId = null)
	{
		// redefine
		$id = (int) $id;

		// exists
		if($formId === null) return (BackendModel::getDB()->getVar('SELECT COUNT(ff.id) FROM forms_fields AS ff WHERE ff.id = ?', $id) >= 1);

		// exists and ignore an id
		return (BackendModel::getDB()->getVar('SELECT COUNT(ff.id) FROM forms_fields AS ff WHERE ff.id = ? AND ff.form_id = ?', array($id, (int) $formId)) >= 1);
	}


	/**
	 * Does an identifier exist.
	 *
	 * @return	bool
	 * @param	string $identifier			Identifier.
	 * @param	in[optional] $ignoreId		Field id to ignore.
	 */
	public static function existsIdentifier($identifier, $ignoreId = null)
	{
		// redefine
		$identifier = (string) $identifier;

		// exists
		if($ignoreId === null) return (BackendModel::getDB()->getVar('SELECT COUNT(f.id) FROM forms AS f WHERE f.identifier = ?', $identifier) >= 1);

		// exists and ignore an id
		else return (BackendModel::getDB()->getVar('SELECT COUNT(f.id) FROM forms AS f WHERE f.identifier = ? AND f.id != ?', array($identifier, (int) $ignoreId)) >= 1);
	}


	/**
	 * Get all data for a given id.
	 *
	 * @return	array
	 * @param	int $id		The id for the record to get.
	 */
	public static function get($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT f.*	FROM forms AS f WHERE f.id = ?', (int) $id);
	}


	/**
	 * Get data for a given id.
	 *
	 * @return	array
	 * @param	int $id		The id for the record to get.
	 */
	public static function getData($id)
	{
		// get data
		$data = (array) BackendModel::getDB()->getRecord('SELECT fd.id, fd.form_id, fd.ip, UNIX_TIMESTAMP(fd.sent_on) AS sent_on
															FROM forms_data AS fd
															WHERE fd.id = ?',
															(int) $id);

		// get fields
		$data['fields'] = (array) BackendModel::getDB()->getRecords('SELECT fdf.label, fdf.value
																	FROM forms_data_fields AS fdf
																	WHERE fdf.data_id = ?',
																	(int) $data['id']);

		// unserialize values
		foreach($data['fields'] as &$field)
		{
			// not null
			if($field['value'] !== null) $field['value'] = unserialize($field['value']);
		}

		// cough up
		return $data;
	}


	/**
	 * Get errors (optional by type).
	 *
	 * @return	mixed
	 * @param	string[optional] $type		Type of error.
	 */
	public static function getErrors($type = null)
	{
		// init
		$errors['required'] = FL::getError('FieldIsRequired');
		$errors['email'] = FL::getError('EmailIsInvalid');
		$errors['numeric'] = FL::getError('NumericCharactersOnly');

		// specific type
		if($type !== null)
		{
			// redefine
			$type = (string) $type;

			// get specific error
			return $errors[$type];
		}

		// all errors
		else
		{
			// init
			$return = array();

			// loop errors
			foreach($errors as $key => $error) $return[] = array('type' => $key, 'message' => $error);

			// cough up
			return $return;
		}
	}


	/**
	 * Get a field.
	 *
	 * @return	array
	 * @param	int $id		Id of a field.
	 */
	public static function getField($id)
	{
		// get field
		$field = (array) BackendModel::getDB()->getRecord('SELECT ff.id, ff.form_id, ff.type, ff.settings
															FROM forms_fields AS ff
															WHERE ff.id = ?', (int) $id);

		// unserialize settings
		if($field['settings'] !== null) $field['settings'] = unserialize($field['settings']);

		// get validation
		$field['validations'] = (array) BackendModel::getDB()->getRecords('SELECT ffv.type, ffv.parameter, ffv.error_message
																			FROM forms_fields_validation AS ffv
																			WHERE ffv.field_id = ?', $field['id'], 'type');

		// cough up field
		return $field;
	}


	/**
	 * Get all fields of a form.
	 *
	 * @return	array
	 * @param	int $id		Id of a form.
	 */
	public static function getFields($id)
	{
		// get fields
		$fields = (array) BackendModel::getDB()->getRecords('SELECT ff.id, ff.type, ff.settings
															FROM forms_fields AS ff
															WHERE ff.form_id = ?
															ORDER BY ff.sequence ASC', (int) $id);

		// fields
		foreach($fields as &$field)
		{
			// unserialize
			if($field['settings'] !== null) $field['settings'] = unserialize($field['settings']);

			// get validation
			$field['validations'] = (array) BackendModel::getDB()->getRecords('SELECT ffv.type, ffv.parameter, ffv.error_message
																				FROM forms_fields_validation AS ffv
																				WHERE ffv.field_id = ?', $field['id'], 'type');
		}

		// cough up fields
		return $fields;
	}


	/**
	 * Get a label/action/message from locale.
	 * Used as datagridfunction.
	 *
	 * @return	string
	 * @param	string $name					Name of the locale item.
	 * @param	string[optional] $type			Type of locale item.
	 * @param	string[optional] $application	Name of the application.
	 */
	public static function getLocale($name, $type = 'label', $application = 'backend')
	{
		// init name
		$name = SpoonFilter::toCamelCase($name);
		$class = ucfirst($application) . 'Language';
		$function = 'get' . ucfirst($type);

		// execute and return value
		return ucfirst(call_user_func_array(array($class, $function), array($name)));
	}


	/**
	 * Get the maximum sequence for fields in a form.
	 *
	 * @return	int
	 * @param	int $formId		Id of the form.
	 */
	public static function getMaximumSequence($formId)
	{
		// get the maximum sequence
		return (int) BackendModel::getDB()->getVar('SELECT MAX(ff.sequence)
													FROM forms_fields AS ff
													WHERE ff.form_id = ?', (int) $formId);
	}


	/**
	 * Add a new item.
	 *
	 * @return	int
	 * @param	array $values		The data to insert.
	 */
	public static function insert(array $values)
	{
		// insert and return the insertId
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
		BackendModel::getDB(true)->insert('pages_extras', $extra);

		// return the insert id
		return $insertId;
	}


	/**
	 * Add a new field.
	 *
	 * @return	int
	 * @param	array $values		The data to insert.
	 */
	public static function insertField(array $values)
	{
		return BackendModel::getDB(true)->insert('forms_fields', $values);
	}


	/**
	 * Add validation for a field.
	 *
	 * @return	int
	 * @param	array $values		The data to insert.
	 */
	public static function insertFieldValidation(array $values)
	{
		return BackendModel::getDB(true)->insert('forms_fields_validation', $values);
	}


	/**
	 * Update an existing item.
	 *
	 * @return	int
	 * @param	int $id				The id for the item to update.
	 * @param	array $values		The new data.
	 */
	public static function update($id, array $values)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB(true);

		// update item
		$db->update('forms', $values, 'id = ?', $id);

		// build array
		$extra['data'] = serialize(array('extra_label' => $values['name'], 'id' => $id));

		// update extra
		$db->update('pages_extras', $extra, 'module = ? AND type = ? AND sequence = ?', array('form_builder', 'widget', '400' . $id));

		// return id
		return $id;
	}


	/**
	 * Update a field.
	 *
	 * @return	int
	 * @param	int $id				The id for the item to update.
	 * @param	array $values		The new data.
	 */
	public static function updateField($id, array $values)
	{
		// update item
		BackendModel::getDB(true)->update('forms_fields', $values, 'id = ?', (int) $id);

		// return id
		return $id;
	}
}


/**
 * Helper class for the form_builder module.
 *
 * @author	Dieter Vanden Eynde <dieter@netlash.com>
 */
class FormBuilderHelper
{
	/**
	 * Parse a field and return the HTML.
	 *
	 * @return	string
	 * @param	array $field	Field data.
	 */
	public static function parseField(array $field)
	{
		// got a field
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

				/// create element
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

				/// create element
				$rbt = $frm->addRadiobutton($fieldName, $newValues, $defaultValues);

				// get content
				$fieldHTML = $rbt->parse();
			}

			// checkbox
			elseif($field['type'] == 'checkbox')
			{
				// rebuild values
				foreach($values as $value) $newValues[] = array('label' => $value, 'value' => $value);

				/// create element
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
				$name = ($field['type'] == 'checkbox') ? 'chk' . ucfirst($fieldName) : 'rbt' . ucfirst($fieldName);

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

			// cough up created html
			return $tpl->getContent(BACKEND_MODULE_PATH . '/layout/templates/field.tpl');
		}

		// empty field so return empty string
		else return '';
	}
}

?>