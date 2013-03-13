<?php

/**
 * In this file we store all generic functions that we will be using in the form_builder module
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendFormBuilderModel
{
	/**
	 * Get an item.
	 *
	 * @param string $id The id of the item to fetch.
	 * @return array
	 */
	public static function get($id)
	{
		$id = (int) $id;

		// get form
		$form = (array) FrontendModel::getContainer()->get('database')->getRecord(
			'SELECT i.id, i.method, i.name, i.email, i.success_message, i.identifier
			 FROM forms AS i
			 WHERE i.id = ?',
			$id
		);

		// unserialize the recipients
		if(isset($form['email'])) $form['email'] = (array) unserialize($form['email']);

		// get validation
		$form['fields'] = self::getFields($id);

		return $form;
	}

	/**
	 * Get all fields of a form.
	 *
	 * @param int $id The id of the form wherefore we fetch the fields.
	 * @return array
	 */
	public static function getFields($id)
	{
		// get fields
		$fields = (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT i.id, i.type, i.settings
			 FROM forms_fields AS i
			 WHERE i.form_id = ?
			 ORDER BY i.sequence ASC',
			(int) $id
		);

		// fields
		foreach($fields as &$field)
		{
			// unserialize
			if($field['settings'] !== null) $field['settings'] = unserialize($field['settings']);

			// get validation
			$field['validations'] = (array) FrontendModel::getContainer()->get('database')->getRecords(
				'SELECT i.type, i.parameter, i.error_message
				 FROM forms_fields_validation AS i
				 WHERE i.field_id = ?',
				$field['id'],
				'type'
			);
		}

		return $fields;
	}

	/**
	 * Insert data.
	 *
	 * @param array $data The data to insert.
	 * @return int
	 */
	public static function insertData(array $data)
	{
		return FrontendModel::getContainer()->get('database')->insert('forms_data', $data);
	}

	/**
	 * Insert data fields.
	 *
	 * @param array $data The data to insert.
	 * @return int
	 */
	public static function insertDataField(array $data)
	{
		return FrontendModel::getContainer()->get('database')->insert('forms_data_fields', $data);
	}

	/**
	 * Notify the admin
	 *
	 * @param array $data
	 */
	public static function notifyAdmin(array $data)
	{
		$alert = array(
			'loc-key' => 'FORMBUILDER_SUBMISSION'
		);

		// build data
		$data = array(
			'api' => SITE_URL . '/api/1.0',
			'form_id' => $data['form_id'],
			'id' => $data['entry_id']
		);

		// push it
		FrontendModel::pushToAppleApp($alert, 1, 'default', $data);
	}
}
