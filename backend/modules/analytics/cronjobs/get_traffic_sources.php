<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This cronjob will fetch the traffic sources
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class BackendAnalyticsCronjobGetTrafficSources extends BackendBaseCronjob
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// fork is no longer authorized to collect analytics data
		if(BackendAnalyticsHelper::getStatus() == 'UNAUTHORIZED')
		{
			// remove all parameters from the module settings
			BackendModel::setModuleSetting('analytics', 'session_token', null);
			BackendModel::setModuleSetting('analytics', 'account_name', null);
			BackendModel::setModuleSetting('analytics', 'table_id', null);
			BackendModel::setModuleSetting('analytics', 'profile_title', null);

			BackendAnalyticsModel::removeCacheFiles();
			BackendAnalyticsModel::clearTables();
			return;
		}

		$this->getData();
	}

	/**
	 * Get data
	 */
	private function getData()
	{
		try
		{
			BackendAnalyticsHelper::getRecentReferrers();
			BackendAnalyticsHelper::getRecentKeywords();
		}

		catch(Exception $e)
		{
			throw new SpoonException('Something went wrong while getting dashboard data.');
		}
	}
}
