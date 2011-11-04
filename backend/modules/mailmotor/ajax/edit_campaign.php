<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the ajax-action to update a campaign
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendMailmotorAjaxEditCampaign extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// get parameters
		$id = SpoonFilter::getPostValue('id', null, '', 'int');
		$name = trim(SpoonFilter::getPostValue('value', null, '', 'string'));

		// validate
		if($name == '') $this->output(self::BAD_REQUEST, null, 'no name provided');

		// get existing id
		$existingId = BackendMailmotorModel::getCampaignId($name);

		// existing campaign
		if($existingId !== 0 && $id !== $existingId) $this->output(self::ERROR, array('id' => $existingId, 'error' => true), BL::err('CampaignExists', $this->getModule()));

		// build array
		$item = array();
		$item['id'] = $id;
		$item['name'] = $name;
		$item['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');

		// get page
		$rows = BackendMailmotorModel::updateCampaign($item);

		// trigger event
		BackendModel::triggerEvent($this->getModule(), 'edited_campaign', array('item' => $item));

		// output
		if($rows !== 0) $this->output(self::OK, array('id' => $id), BL::msg('CampaignEdited', $this->getModule()));
		else $this->output(self::ERROR, null, BL::err('CampaignNotEdited', $this->getModule()));
	}
}
