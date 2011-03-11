<?php

/**
 * BackendMailmotorAjaxLinkAccount
 * This checks if a CampaignMonitor account exists or not, and links it if it does
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorAjaxLinkAccount extends BackendBaseAJAXAction
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
		$url = SpoonFilter::getPostValue('url', null, '');
		$username = SpoonFilter::getPostValue('username', null, '');
		$password = SpoonFilter::getPostValue('password', null, '');

		// check input
		if(empty($url)) $this->output(self::BAD_REQUEST, array('field' => 'url'), BL::err('NoCMAccountCredentials'));
		if(empty($username)) $this->output(self::BAD_REQUEST, array('field' => 'username'), BL::err('NoCMAccountCredentials'));
		if(empty($password)) $this->output(self::BAD_REQUEST, array('field' => 'password'), BL::err('NoCMAccountCredentials'));

		try
		{
			// check if the CampaignMonitor class exists
			if(!SpoonFile::exists(PATH_LIBRARY . '/external/campaignmonitor.php'))
			{
				// the class doesn't exist, so stop here
				$this->output(self::BAD_REQUEST, null, BL::err('ClassDoesNotExist', 'mailmotor'));
			}

			// require CampaignMonitor class
			require_once 'external/campaignmonitor.php';

			// init CampaignMonitor object
			$cm = new CampaignMonitor($url, $username, $password, 10);

			// save the new data
			BackendModel::setModuleSetting('mailmotor', 'cm_url', $url);
			BackendModel::setModuleSetting('mailmotor', 'cm_username', $username);
			BackendModel::setModuleSetting('mailmotor', 'cm_password', $password);

			// account was linked
			BackendModel::setModuleSetting('mailmotor', 'cm_account', true);
		}

		catch(Exception $e)
		{
			// timeout occured
			if($e->getMessage() == 'Error Fetching http headers') $this->output(self::BAD_REQUEST, null, BL::err('CmTimeout', 'mailmotor'));

			// other error
			$this->output(self::ERROR, array('field' => 'url'), sprintf(BL::err('CampaignMonitorError', 'mailmotor'), $e->getMessage()));
		}

		// CM was successfully initialized
		$this->output(self::OK, array('message' => 'account-linked'), BL::msg('AccountLinked', 'mailmotor'));
	}
}

?>