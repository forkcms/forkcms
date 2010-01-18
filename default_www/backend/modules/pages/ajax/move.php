<?php

/**
 * PagesAjaxMove
 *
 * This is the add-action, it will display a form to create a new item
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class PagesAjaxMove extends BackendBaseAJAXAction
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
		$id = SpoonFilter::getPostValue('id', null, 0, 'int');
		$droppedOn = SpoonFilter::getPostValue('dropped_on', null, 0, 'int');
		$typeOfDrop = SpoonFilter::getPostValue('type', null, '');

		// validate
		if($id == 0) $this->output(BackendBaseAJAXAction::BAD_REQUEST, null, 'no id provided');
		if($droppedOn == 0) $this->output(BackendBaseAJAXAction::BAD_REQUEST, null, 'no id provided');
		if($typeOfDrop == '') $this->output(BackendBaseAJAXAction::BAD_REQUEST, null, 'no type provided');

		// get page
		$success = BackendPagesModel::move($id, $droppedOn, $typeOfDrop);

		// output
		if($success) $this->output(BackendBaseAJAXAction::OK, null, 'page moved');
		else $this->output(BackendBaseAJAXAction::ERROR, null, 'page not moved');
	}
}

?>