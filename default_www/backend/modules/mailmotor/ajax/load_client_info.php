<?php

/**
 * BackendMailmotorAjaxLoadClientInfo
 * This checks if a CampaignMonitor account exists or not, and links it if it does
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorAjaxLoadClientInfo extends BackendBaseAJAXAction
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
		$clientId = SpoonFilter::getPostValue('client_id', null, '');

		// check input
		if(empty($clientId)) $this->output(self::BAD_REQUEST);

		// get basic details for this client
		$client = BackendMailmotorCMHelper::getCM()->getClient($clientId);

		// CM was successfully initialized
		$this->output(self::OK, $client);
	}
}

?>