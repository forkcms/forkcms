<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Resequence the fields via ajax.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendFormBuilderAjaxSequence extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// get parameters
		$formId = SpoonFilter::getPostValue('form_id', null, '', 'int');
		$newIdSequence = trim(SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));

		// invalid form id
		if(!BackendFormBuilderModel::exists($formId)) $this->output(self::BAD_REQUEST, null, 'form does not exist');

		// list id
		$ids = (array) explode('|', rtrim($newIdSequence, '|'));

		// loop id's and set new sequence
		foreach($ids as $i => $id)
		{
			$id = (int) $id;

			// get field
			$field = BackendFormBuilderModel::getField($id);

			// from this form and not a submit button
			if(!empty($field) && $field['form_id'] == $formId && $field['type'] != 'submit') BackendFormBuilderModel::updateField($id, array('sequence' => ($i + 1)));
		}

		$this->output(self::OK, null, 'sequence updated');
	}
}
