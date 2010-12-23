<?php

/**
 * This edit-action will get the page info using Ajax
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
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
		// call parent
		parent::execute();

		// get parameters
		$id = SpoonFilter::getPostValue('id', null, 0, 'int');

		// validate
		if($id === 0) $this->output(self::BAD_REQUEST, null, 'no id provided');

		// get page
		$page = BackendPagesModel::get($id);

		// output
		$this->output(self::OK, $page);
	}
}

?>