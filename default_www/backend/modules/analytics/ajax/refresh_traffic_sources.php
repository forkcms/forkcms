<?php

/**
 * This edit-action will refresh the traffic sources using Ajax
 *
 * @package		backend
 * @subpackage	analytics
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class BackendAnalyticsAjaxRefreshTrafficSources extends BackendBaseAJAXAction
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

			// return status
			$this->output(self::OK, array('status' => 'unauthorized', 'message' => BL::msg('Redirecting')), 'No longer authorized.');
		}

		// get data
		$this->getData();

		// get html
		$referrersHtml = $this->parseReferrers();
		$keywordsHtml = $this->parseKeywords();

		// return status
		$this->output(self::OK, array('status' => 'success', 'referrersHtml' => $referrersHtml, 'keywordsHtml' => $keywordsHtml, 'date' => BL::lbl('Today'), 'message' => BL::msg('RefreshedTrafficSources')), 'Data has been retrieved.');
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
			// return status
			$this->output(self::OK, array('status' => 'error'), 'Something went wrong while getting the traffic sources.');
		}
	}


	/**
	 * Parse into template
	 *
	 * @return	void
	 */
	private function parseKeywords()
	{
		// get results
		$results = BackendAnalyticsModel::getRecentKeywords();

		// there are some results
		if(!empty($results))
		{
			// get the datagrid
			$dataGrid = new BackendDataGridArray($results);

			// no pagination
			$dataGrid->setPaging();

			// hide columns
			$dataGrid->setColumnsHidden('id', 'date');
		}

		// parse the datagrid
		return (!empty($results) ? $dataGrid->getContent() : '<table border="0" cellspacing="0" cellpadding="0" class="dataGrid"><tr><td>' . BL::msg('NoReferrers') . '</td></tr></table>');
	}


	/**
	 * Parse into template
	 *
	 * @return	void
	 */
	private function parseReferrers()
	{
		// get results
		$results = BackendAnalyticsModel::getRecentReferrers();

		// there are some results
		if(!empty($results))
		{
			// get the datagrid
			$dataGrid = new BackendDataGridArray($results);

			// no pagination
			$dataGrid->setPaging();

			// hide columns
			$dataGrid->setColumnsHidden('id', 'date');

			// set url
			$dataGrid->setColumnURL('referrer', 'http://[referrer]');
		}

		// parse the datagrid
		return (!empty($results) ? $dataGrid->getContent() : '<table border="0" cellspacing="0" cellpadding="0" class="dataGrid"><tr><td>' . BL::msg('NoKeywords') . '</td></tr></table>');
	}
}

?>