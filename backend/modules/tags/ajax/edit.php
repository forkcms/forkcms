<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This edit-action will update tags using Ajax
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendTagsAjaxEdit extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
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
		$item['url'] = BackendTagsModel::getURL(SpoonFilter::urlise(SpoonFilter::htmlspecialcharsDecode($item['tag'])), $id);

		// update
		BackendTagsModel::update($item);

		// output
		$this->output(self::OK, $item, vsprintf(BL::msg('Edited'), array($item['tag'])));
	}
}
