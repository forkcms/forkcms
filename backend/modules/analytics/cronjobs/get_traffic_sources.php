<?php

/**
 * This cronjob will fetch the traffic sources
 *
 * @package		backend
 * @subpackage	analytics
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class BackendAnalyticsCronjobGetTrafficSources extends BackendBaseCronjob
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

		// fork is no longer authorized to collect analytics data
		if(BackendAnalyticsHelper::getStatus() == 'UNAUTHORIZED')
		{
			// remove all parameters from the module settings
			BackendModel::setModuleSetting('analytics', 'session_token', null);
			BackendModel::setModuleSetting('analytics', 'account_name', null);
			BackendModel::setModuleSetting('analytics', 'table_id', null);
			BackendModel::setModuleSetting('analytics', 'profile_title', null);

			// remove cache files
			BackendAnalyticsModel::removeCacheFiles();

			// clear tables
			BackendAnalyticsModel::clearTables();

			// stop here
			return;
		}

		// get data
		$this->getData();
	}


	/**
	 * Get data
	 *
	 * @return	void
	 */
	private function getData()
	{
		// try
		try
		{
			// fetch from google and save in db
			BackendAnalyticsHelper::getRecentReferrers();

			// fetch from google and save in db
			BackendAnalyticsHelper::getRecentKeywords();
		}

		// something went wrong
		catch(Exception $e)
		{
			// throw exception
			throw new SpoonException('Something went wrong while getting dashboard data.');
		}
	}
}

?>