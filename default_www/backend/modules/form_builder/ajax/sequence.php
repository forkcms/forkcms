<?php

/**
 * Resequence the fields via ajax.
 *
 * @package		backend
 * @subpackage	form_builder
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendFormBuilderAjaxSequence extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get parameters
		$formId = SpoonFilter::getPostValue('form_id', null, '', 'int');
		$newIdSequence = trim(SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));

		// invalid form id
		if(!BackendFormBuilderModel::exists($formId)) $this->output(self::BAD_REQUEST, null, 'form does not exist');

		// list id
		$ids = (array) explode(',', rtrim($newIdSequence, ','));

		// loop id's and set new sequence
		foreach($ids as $i => $id)
		{
			// redefine
			$id = (int) $id;

			// get field
			$field = BackendFormBuilderModel::getField($id);

			// from this form and not a submit button
			if(!empty($field) && $field['form_id'] == $formId && $field['type'] != 'submit') BackendFormBuilderModel::updateField($id, array('sequence' => ($i + 1)));
		}

		// success output
		$this->output(self::OK, null, 'sequence updated');
	}
}

?>