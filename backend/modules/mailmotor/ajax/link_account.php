<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This checks if a CampaignMonitor account exists or not, and links it if it does
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendMailmotorAjaxLinkAccount extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// get parameters
		$url = SpoonFilter::getPostValue('url', null, '');
		$username = SpoonFilter::getPostValue('username', null, '');
		$password = SpoonFilter::getPostValue('password', null, '');

		// filter out the 'http://' from the URL
		if(strpos($url, 'http://') !== false) $url = str_replace('http://', '', $url);
		if(strpos($url, 'https://') !== false) $url = str_replace('https://', '', $url);

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
				$this->output(self::BAD_REQUEST, null, BL::err('ClassDoesNotExist', $this->getModule()));
			}

			// require CampaignMonitor class
			require_once 'external/campaignmonitor.php';

			// init CampaignMonitor object
			new CampaignMonitor($url, $username, $password, 10);

			// save the new data
			BackendModel::setModuleSetting($this->getModule(), 'cm_url', $url);
			BackendModel::setModuleSetting($this->getModule(), 'cm_username', $username);
			BackendModel::setModuleSetting($this->getModule(), 'cm_password', $password);

			// account was linked
			BackendModel::setModuleSetting($this->getModule(), 'cm_account', true);
		}

		catch(Exception $e)
		{
			// timeout occured
			if($e->getMessage() == 'Error Fetching http headers') $this->output(self::BAD_REQUEST, null, BL::err('CmTimeout', $this->getModule()));

			// other error
			$this->output(self::ERROR, array('field' => 'url'), sprintf(BL::err('CampaignMonitorError', $this->getModule()), $e->getMessage()));
		}

		// trigger event
		BackendModel::triggerEvent($this->getModule(), 'after_account_linked');

		// CM was successfully initialized
		$this->output(self::OK, array('message' => 'account-linked'), BL::msg('AccountLinked', $this->getModule()));
	}
}
