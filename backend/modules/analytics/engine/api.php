<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be available through the API
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendAnalyticsAPI
{
	/**
	 * Check the settings for the analytics-module.
	 *
	 * @return bool
	 */
	private static function checkSettings()
	{
		$warnings = array();

		// analytics session token
		if(BackendModel::getModuleSetting('analytics', 'session_token', null) == '')
		{
			API::output(API::ERROR, array('message' => 'Analytics-module not configured correctly.'));
		}

		// analytics table id (only show this error if no other exist)
		if(BackendModel::getModuleSetting('analytics', 'table_id', null) == '')
		{
			API::output(API::ERROR, array('message' => 'Analytics-module not configured correctly.'));
		}

		return true;
	}

	/**
	 * Get the data for the keywords tab in the Iphone-app
	 *
	 * @return array
	 */
	public static function keywordsGetData()
	{
		// authorize
		if(API::authorize() && API::isValidRequestMethod('GET') && self::checkSettings())
		{
			$data = BackendAnalyticsModel::getRecentKeywords();

			$return = array('data' => null);

			foreach($data as $row)
			{
				$item['keyword'] = array();
				$item['keyword']['word'] = $row['keyword'];
				$item['keyword']['entrances'] = $row['entrances'];

				$return['data'][] = $item;
			}

			return $return;
		}
	}

	/**
	 * Get the data for the referrers tab in the Iphone-app
	 *
	 * @return array
	 */
	public static function referrersGetData()
	{
		// authorize
		if(API::authorize() && API::isValidRequestMethod('GET') && self::checkSettings())
		{
			$data = BackendAnalyticsModel::getRecentReferrers();

			$return = array('data' => null);

			foreach($data as $row)
			{
				$item['keyword'] = array();
				$item['keyword']['referrer'] = $row['referrer'];
				$item['keyword']['url'] = $row['url'];
				$item['keyword']['entrances'] = $row['entrances'];

				$return['data'][] = $item;
			}

			return $return;
		}
	}

	/**
	 * Get the data for the visitors tab in the Iphone-app
	 *
	 * @return array
	 */
	public static function visitorsGetData()
	{
		// authorize
		if(API::authorize() && API::isValidRequestMethod('GET') && self::checkSettings())
		{
			$startTimestamp = strtotime('-1 week -1 days', mktime(0, 0, 0));
			$endTimestamp = mktime(0, 0, 0);

			// get data
			$graphData = BackendAnalyticsHelper::getDashboardData($startTimestamp, $endTimestamp);
			$numericData = BackendAnalyticsHelper::getAggregates($startTimestamp, $endTimestamp);

			$return = array('data' => null);

			foreach($graphData as $row)
			{
				// create array
				$item['day'] = array();

				// article meta data
				$item['day']['@attributes']['timestamp'] = date('c', $row['timestamp']);
				$item['day']['visitors'] = $row['visitors'];
				$item['day']['pageviews'] = $row['pageviews'];

				$return['data']['graph']['days'][] = $item;
			}

			foreach($numericData as $key => $value)
			{
				$return['data']['numeric'][$key] = $value;
			}

			return $return;
		}
	}
}
