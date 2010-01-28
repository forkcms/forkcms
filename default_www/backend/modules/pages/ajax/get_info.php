<?php

/**
 * BackendPagesAjaxGetInfo
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendPagesAjaxGetInfo extends BackendBaseAJAXAction
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

		// validate
		if($id == 0) $this->output(BackendBaseAJAXAction::BAD_REQUEST, null, 'no id provided');

		// get page
		$page = BackendPagesModel::get($id);

		// output
		$this->output(BackendBaseAJAXAction::OK, $page);
	}
}

?>