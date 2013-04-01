<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be available through the API
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendFormBuilderAPI
{
	/**
	 * Delete entry/entries.
	 *
	 * @param string $id The id/ids of the entries(s) to delete.
	 */
	public static function entriesDelete($id)
	{
		// authorize
		if(API::authorize() && API::isValidRequestMethod('POST'))
		{
			// redefine
			if(!is_array($id)) $id = (array) explode(',', $id);

			// update statuses
			BackendFormBuilderModel::deleteData($id);
		}
	}

	/**
	 * Get the entries for a form
	 *
	 * @param int $id The id of the form.
	 * @param int[optional] $limit The maximum number of items to retrieve.
	 * @param int[optional] $offset The offset.
	 * @return array
	 */
	public static function entriesGet($id, $limit = 30, $offset = 0)
	{
		if(API::authorize() && API::isValidRequestMethod('GET'))
		{
			// redefine
			$id = (int) $id;
			$limit = (int) $limit;
			$offset = (int) $offset;

			// validate
			if($limit > 10000)
			{
				return API::output(API::ERROR, array('message' => 'Limit can\'t be larger than 10000.'));
			}

			$dataIDs = (array) BackendModel::getContainer()->get('database')->getColumn(
				'SELECT a.id
				 FROM forms_data AS a
				 WHERE a.form_id = ?
				 ORDER BY a.sent_on DESC
				 LIMIT ?,?',
				array($id, $offset, $limit)
			);

			if(empty($dataIDs)) return array();

			$fields = (array) BackendModel::getContainer()->get('database')->getRecords(
				'SELECT i.type, i.settings
				 FROM forms_fields AS i
				 WHERE i.form_id = ?',
				array($id)
			);

			$fieldTypes = array();
			foreach($fields as $row)
			{
				$row['settings'] = unserialize($row['settings']);

				if(isset($row['settings']['label']))
				{
					$fieldTypes[$row['settings']['label']] = $row['type'];
				}
			}

			$entries = (array) BackendModel::getContainer()->get('database')->getRecords(
				'SELECT i.*, f.data_id, f.label, f.value, UNIX_TIMESTAMP(i.sent_on) AS sent_on
				 FROM forms_data AS i
				 INNER JOIN forms_data_fields AS f ON i.id = f.data_id
				 WHERE i.id IN('. implode(',', $dataIDs) .')
				 ORDER BY i.sent_on DESC'
			);

			$return = array('entries' => null);

			// any entries?
			if(empty($entries)) return $return;

			$data = array();
			foreach($entries as $row)
			{
				if(!isset($data[$row['data_id']]))
				{
					$data[$row['data_id']] = $row;
				}

				$data[$row['data_id']]['fields'][$row['label']] = unserialize($row['value']);
			}

			foreach($data as $row)
			{
				$item['entry'] = array();

				// set attributes
				$item['entry']['@attributes']['form_id'] = $row['form_id'];
				$item['entry']['@attributes']['id'] = $row['id'];
				$item['entry']['@attributes']['sent_on'] = date('c', $row['sent_on']);

				// set content
				foreach($row['fields'] as $key => $value)
				{
					$item['entry']['fields']['fields'][] = array('field' => array(
						'name' => $key,
						'value' => $value,
						'guessed_type' => (isset($fieldTypes[$key])) ? $fieldTypes[$key] : 'textbox'
					));
				}

				$return['entries'][$row['id']] = $item;
			}

			$return['entries'] = array_values($return['entries']);

			return $return;
		}
	}

	/**
	 * Get a single entry
	 *
	 * @param int $id The id of the entry.
	 * @return array
	 */
	public static function entriesGetById($id)
	{
		if(API::authorize() && API::isValidRequestMethod('GET'))
		{
			// redefine
			$id = (int) $id;

			$entries = (array) BackendModel::getContainer()->get('database')->getRecords(
				'SELECT i.*, f.*, UNIX_TIMESTAMP(i.sent_on) AS sent_on
				 FROM forms_data AS i
				 INNER JOIN forms_data_fields AS f ON i.id = f.data_id
				 WHERE i.id = ?',
				array($id)
			);

			// any entries?
			if(empty($entries))
			{
				return API::output(API::ERROR, array('message' => 'Not found.'));
			}

			$return = array('entry' => null);

			$data = array();
			foreach($entries as $row)
			{
				if(!isset($data['id'])) $data = $row;

				$data['fields'][$row['label']] = unserialize($row['value']);
			}

			$fields = (array) BackendModel::getContainer()->get('database')->getRecords(
				'SELECT i.type, i.settings
				 FROM forms_fields AS i
				 WHERE i.form_id = ?',
				array($data['form_id'])
			);
			$fieldTypes = array();
			foreach($fields as $row)
			{
				$row['settings'] = unserialize($row['settings']);

				if(isset($row['settings']['label']))
				{
					$fieldTypes[$row['settings']['label']] = $row['type'];
				}
			}

			// set attributes
			$return['entry']['@attributes']['form_id'] = $data['form_id'];
			$return['entry']['id'] = $data['id'];
			$return['entry']['sent_on'] = date('c', $data['sent_on']);

			foreach($data['fields'] as $key => $value)
			{
				$return['entry']['fields'][] = array('field' => array(
					'name' => $key,
					'value' => $value,
					'guessed_type' => (isset($fieldTypes[$key])) ? $fieldTypes[$key] : 'textbox'
				));
			}

			return $return;
		}
	}

	/**
	 * Get a list of all the forms
	 *
	 * @param int[optional] $limit The maximum number of items to retrieve.
	 * @param int[optional] $offset The offset.
	 * @return array
	 */
	public static function getAll($limit = 30, $offset = 0)
	{
		if(API::authorize() && API::isValidRequestMethod('GET'))
		{
			// redefine
			$limit = (int) $limit;
			$offset = (int) $offset;

			// validate
			if($limit > 10000)
			{
				return API::output(API::ERROR, array('message' => 'Limit can\'t be larger than 10000.'));
			}

			$forms = (array) BackendModel::getContainer()->get('database')->getRecords(
				'SELECT i.id, i.language, i.name, i.method, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on
				 FROM forms AS i
				 ORDER BY i.created_on DESC
				 LIMIT ?, ?',
				array($offset, $limit)
			);

			$return = array('forms' => null);

			foreach($forms as $row)
			{
				$item['form'] = array();

				// set attributes
				$item['form']['@attributes']['id'] = $row['id'];
				$item['form']['@attributes']['created_on'] = date('c', $row['created_on']);
				$item['form']['@attributes']['language'] = $row['language'];

				// set content
				$item['form']['name'] = $row['name'];
				$item['form']['method'] = $row['method'];

				$return['forms'][] = $item;
			}

			return $return;
		}
	}
}
