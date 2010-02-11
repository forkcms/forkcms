<?php

/**
 * BackendPagesAjaxMove
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendPagesAjaxMove extends BackendBaseAJAXAction
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
		if($id == 0) $this->output(self::BAD_REQUEST, null, 'no id provided');
		if($droppedOn == 0) $this->output(self::BAD_REQUEST, null, 'no id provided');
		if($typeOfDrop == '') $this->output(self::BAD_REQUEST, null, 'no type provided');

		// get page
		$success = BackendPagesModel::move($id, $droppedOn, $typeOfDrop);

		// output
		if($success) $this->output(self::OK, null, 'page moved');
		else $this->output(self::ERROR, null, 'page not moved');
	}
}

?>