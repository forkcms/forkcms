<?php

/**
 * BackendAnalyticsAjaxCheckStatus
 *
 * @package		backend
 * @subpackage	analytics
 *
 * @author 		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class BackendAnalyticsAjaxGetTrafficSources extends BackendBaseAJAXAction
{
	/**
	 * The path to the temporary busy file
	 *
	 * @var string
	 */
	private $busyFile;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get path to the busy file
		$this->busyFile = BACKEND_CACHE_PATH .'/analytics/busy_traffic_sources.txt';

		// fork is no longer authorized to collect analytics data
		if(BackendAnalyticsHelper::getStatus() == 'UNAUTHORIZED')
		{
			// cleanup temporary file
			SpoonFile::delete($this->busyFile);

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
			$this->output(self::OK, array('status' => 'unauthorized'), 'No longer authorized.');
		}

		// the busy file still exists - go away
		if(SpoonFile::exists($this->busyFile))
		{
			// get the content of the file
			$counter = (int) SpoonFile::getContent($this->busyFile);

			// should be done by now
			if($counter > 10)
			{
				// cleanup temporary file
				SpoonFile::delete($this->busyFile);

				// return status
				$this->output(self::OK, array('status' => 'error'), 'Data took too long to retrieve. Cache file has been removed.');
			}

			// set new counter
			SpoonFile::setContent($this->busyFile, ++$counter);

			// output status
			$this->output(self::OK, array('status' => 'getting_data'), 'Data is still being retrieved.');
		}

		// there is no busy file - create one
		else SpoonFile::setContent($this->busyFile, '1');

		// get data
		$this->getData();

		// cleanup temp file
		SpoonFile::delete($this->busyFile);

		// return status
		$this->output(self::OK, array('status' => 'success'), 'Data has been retrieved.');
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
			// cleanup temporary account
			SpoonFile::delete($this->busyFile);

			// return status
			$this->output(self::OK, array('status' => 'error'), 'Something went wrong while getting the traffic sources.');
		}
	}
}

?>