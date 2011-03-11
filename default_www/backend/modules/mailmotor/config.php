<?php

// require the CM helper class
require_once 'engine/helper.php';

/**
 * BackendMailmotorConfig
 * This is the configuration-object for the mailmotor module
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
final class BackendMailmotorConfig extends BackendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'index';


	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();


	/**
	 * Check if all required settings have been set
	 *
	 * @return	void
	 * @param	string $module	The module.
	 */
	public function __construct($module)
	{
		// parent construct
		parent::__construct($module);

		// get url object reference
		$url = Spoon::exists('url') ? Spoon::get('url') : null;

		// do the client ID check if we're not in the settings page
		if($url != null && $url->getAction() != 'settings' && strpos($url->getQueryString(), 'link_account') === false)
		{
			// check for CM account
			$this->checkForAccount();

			// check for client ID
			$this->checkForClientID();

			// check for default groups
			$this->checkForDefaultGroups();
		}
	}


	/**
	 * Checks if a general CM account is made or not
	 *
	 * @return	void
	 */
	private function checkForAccount()
	{
		if(!BackendMailmotorCMHelper::checkAccount()) SpoonHTTP::redirect(BackendModel::createURLForAction('settings', 'mailmotor', BL::getWorkingLanguage()));
	}


	/**
	 * Checks if a client ID was already set or not
	 *
	 * @return	void
	 */
	private function checkForClientID()
	{
		// fetch client ID
		$clientId = BackendMailmotorCMHelper::getClientID();

		// no client ID set, so redirect to settings with an appropriate error message.
		if(empty($clientId)) SpoonHTTP::redirect(BackendModel::createURLForAction('settings', 'mailmotor', BL::getWorkingLanguage()));

		// get price per email
		$pricePerEmail = BackendModel::getModuleSetting('mailmotor', 'price_per_email');

		// check if a price per e-mail is set
		if(empty($pricePerEmail) && $pricePerEmail != 0) SpoonHTTP::redirect(BackendModel::createURLForAction('settings', 'mailmotor', BL::getWorkingLanguage()) . '&error=no-price-per-email');
	}


	/**
	 * Checks if any default groups are set for the active working language, and creates them if none were found
	 *
	 * @return	void
	 */
	private function checkForDefaultGroups()
	{
		// defaults are already set
		if(BackendModel::getModuleSetting('mailmotor', 'cm_defaults_set')) return false;

		// no CM data found
		if(!BackendMailmotorCMHelper::checkAccount()) return false;

		// fetch the default groups, language abbreviation is the array key
		$groups = BackendMailmotorModel::getDefaultGroups();

		// loop languages
		foreach(BL::getActiveLanguages() as $language)
		{
			// this language does not have a default group set
			if(!isset($groups[$language]))
			{
				// set group record
				$group['name'] = 'Website (' . strtoupper($language) . ')';
				$group['language'] = $language;
				$group['is_default'] = 'Y';
				$group['created_on'] = date('Y-m-d H:i:s');

				// insert the group in CampaignMonitor
				BackendMailmotorCMHelper::insertGroup($group);
			}
		}

		// reset the cm_defaults_set setting
		BackendModel::setModuleSetting('mailmotor', 'cm_defaults_set', true);
	}
}

?>