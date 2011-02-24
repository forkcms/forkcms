<?php

/**
 * This edit-action will update a category using Ajax
 *
 * @package		backend
 * @subpackage	events
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendEventsAjaxEditCategory extends BackendBaseAJAXAction
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
		$categoryName = trim(SpoonFilter::getPostValue('value', null, '', 'string'));

		// validate
		if($id === 0) $this->output(self::BAD_REQUEST, null, 'no id provided');
		if($categoryName === '') $this->output(self::BAD_REQUEST, null, BL::err('NameIsRequired'));

		// build array
		$item['id'] = $id;
		$item['name'] = SpoonFilter::htmlspecialchars($categoryName);
		$item['language'] = BL::getWorkingLanguage();
		$item['url'] = BackendEventsModel::getURLForCategory($item['name']);

		// update
		BackendEventsModel::updateCategory($item);

		// output
		$this->output(self::OK, $item, vsprintf(BL::msg('EditedCategory'), array($item['name'])));
	}
}

?>