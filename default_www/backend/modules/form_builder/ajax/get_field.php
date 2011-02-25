<?php

/**
 * Get a field via ajax.
 *
 * @package		backend
 * @subpackage	form_builder
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendFormBuilderAjaxGetField extends BackendBaseAJAXAction
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
		$formId = trim(SpoonFilter::getGetValue('form_id', null, '', 'int'));
		$fieldId = trim(SpoonFilter::getGetValue('field_id', null, '', 'int'));

		// invalid form id
		if(!BackendFormBuilderModel::exists($formId)) $this->output(self::BAD_REQUEST, null, 'form does not exist');

		// invalid fieldId
		if(!BackendFormBuilderModel::existsField($fieldId, $formId)) $this->output(self::BAD_REQUEST, null, 'field does not exist');

		// get field
		$field = BackendFormBuilderModel::getField($fieldId);

		// success output
		$this->output(self::OK, array('field' => $field));
	}
}

?>