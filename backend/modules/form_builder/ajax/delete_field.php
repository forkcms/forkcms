<?php

/**
 * Delete a field via ajax.
 *
 * @package		backend
 * @subpackage	form_builder
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendFormBuilderAjaxDeleteField extends BackendBaseAJAXAction
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
		$formId = trim(SpoonFilter::getPostValue('form_id', null, '', 'int'));
		$fieldId = trim(SpoonFilter::getPostValue('field_id', null, '', 'int'));

		// invalid form id
		if(!BackendFormBuilderModel::exists($formId)) $this->output(self::BAD_REQUEST, null, 'form does not exist');

		// invalid fieldId
		if(!BackendFormBuilderModel::existsField($fieldId, $formId)) $this->output(self::BAD_REQUEST, null, 'field does not exist');

		// get field
		$field = BackendFormBuilderModel::getField($fieldId);

		// submit button cannot be deleted
		if($field['type'] == 'submit') $this->output(self::BAD_REQUEST, null, 'submit button cannot be deleted');

		// delete
		else
		{
			// delete field
			BackendFormBuilderModel::deleteField($fieldId);

			// success output
			$this->output(self::OK, null, 'field deleted');
		}
	}
}

?>