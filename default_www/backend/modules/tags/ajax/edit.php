<?php

/**
 * This edit-action will update tags using Ajax
 *
 * @package		backend
 * @subpackage	tags
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendTagsAjaxEdit extends BackendBaseAJAXAction
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
		$tag = trim(SpoonFilter::getPostValue('value', null, '', 'string'));

		// validate
		if($id === 0) $this->output(self::BAD_REQUEST, null, 'no id provided');
		if($tag === '') $this->output(self::BAD_REQUEST, null, BL::err('NameIsRequired'));

		// check if tag exists
		if(BackendTagsModel::existsTag($tag)) $this->output(self::BAD_REQUEST, null, BL::err('TagAlreadyExists'));

		// build array
		$item['id'] = $id;
		$item['tag'] = SpoonFilter::htmlspecialchars($tag);
		$item['url'] = BackendTagsModel::getURL($item['tag'], $id);

		// update
		BackendTagsModel::update($item);

		// output
		$this->output(self::OK, $item, vsprintf(BL::msg('Edited'), array($item['tag'])));
	}
}

?>