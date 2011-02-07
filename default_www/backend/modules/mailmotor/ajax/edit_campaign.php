<?php

/**
 * BackendMailmotorAjaxEditCampaign
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorAjaxEditCampaign extends BackendBaseAJAXAction
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
		$id = SpoonFilter::getPostValue('id', null, '', 'int');
		$name = trim(SpoonFilter::getPostValue('value', null, '', 'string'));

		// validate
		if($name == '') $this->output(self::BAD_REQUEST, null, 'no name provided');

		// get existing id
		$existingId = BackendMailmotorModel::getCampaignId($name);

		// existing campaign
		if($existingId !== 0 && $id !== $existingId) $this->output(self::ERROR, array('id' => $existingId, 'error' => true), BL::err('CampaignExists', 'mailmotor'));

		// build array
		$record = array();
		$record['id'] = $id;
		$record['name'] = $name;
		$record['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');

		// get page
		$rows = BackendMailmotorModel::updateCampaign($record);

		// output
		if($rows !== 0) $this->output(self::OK, array('id' => $id), BL::msg('CampaignEdited', 'mailmotor'));
		else $this->output(self::ERROR, null, BL::err('CampaignNotEdited', 'mailmotor'));
	}
}

?>