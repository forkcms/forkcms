<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This edit-action will reorder moved pages using Ajax
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendPagesAjaxMove extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent
		parent::execute();

		// get parameters
		$id = SpoonFilter::getPostValue('id', null, 0, 'int');
		$droppedOn = SpoonFilter::getPostValue('dropped_on', null, -1, 'int');
		$typeOfDrop = SpoonFilter::getPostValue('type', null, '');
		$tree = SpoonFilter::getPostValue('tree', array('main', 'meta', 'footer', 'root'), '');

		// validate
		if($id === 0) $this->output(self::BAD_REQUEST, null, 'no id provided');
		if($droppedOn === -1) $this->output(self::BAD_REQUEST, null, 'no dropped_on provided');
		if($typeOfDrop == '') $this->output(self::BAD_REQUEST, null, 'no type provided');
		if($tree == '') $this->output(self::BAD_REQUEST, null, 'no tree provided');

		// get page
		$success = BackendPagesModel::move($id, $droppedOn, $typeOfDrop, $tree);

		// build cache
		BackendPagesModel::buildCache(BL::getWorkingLanguage());

		// output
		if($success) $this->output(self::OK, BackendPagesModel::get($id), 'page moved');
		else $this->output(self::ERROR, null, 'page not moved');
	}
}
