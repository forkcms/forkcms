<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic data communication functions
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Dieter Van den Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendAnalyticsModel
{
	/**
	 * Google authentication url and scope
	 *
	 * @var	string
	 */
	const GOOGLE_ACCOUNT_AUTHENTICATION_URL = 'https://www.google.com/accounts/AuthSubRequest?next=%1$s&amp;scope=%2$s&amp;secure=0&amp;session=1';
	const GOOGLE_ACCOUNT_AUTHENTICATION_SCOPE = 'https://www.google.com/analytics/feeds/';

	/**
	 * Google analytics url
	 *
	 * @var	string
	 */
	const GOOGLE_ANALYTICS_URL = 'https://www.google.com/analytics/reporting';

	/**
	 * Cached data
	 *
	 * @var	array
	 */
	private static $data = array(), $dashboardData = array();

	/**
	 * Checks the settings and optionally returns an array with warnings
	 *
	 * @return array
	 */
	public static function checkSettings()
	{
		$warnings = array();

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('settings', 'analytics'))
		{
			// analytics session token
			if(BackendModel::getModuleSetting('analytics', 'session_token', null) == '')
			{
				// add warning
				$warnings[] = array('message' => sprintf(BL::err('AnalyseNoSessionToken', 'analytics'), BackendModel::createURLForAction('settings', 'analytics')));
			}

			// analytics table id (only show this error if no other exist)
			if(empty($warnings) && BackendModel::getModuleSetting('analytics', 'table_id', null) == '')
			{
				// add warning
				$warnings[] = array('message' => sprintf(BL::err('AnalyseNoTableId', 'analytics'), BackendModel::createURLForAction('settings', 'analytics')));
			}
		}

		return $warnings;
	}

	/**
	 * Clear tables
	 */
	public static function clearTables()
	{
		BackendModel::getDB(true)->truncate(
			array(
				'analytics_keywords',
				'analytics_landing_pages',
				'analytics_pages',
				'analytics_referrers'
			)
		);
	}

	/**
	 * Delete one or more landing pages
	 *
	 * @param mixed $ids The ids to delete.
	 */
	public static function deleteLandingPage($ids)
	{
		BackendModel::getDB(true)->delete('analytics_landing_pages', 'id IN (' . implode(',', (array) $ids) . ')');
	}

	/**
	 * Checks if a landing page exists
	 *
	 * @param int $id The id of the landing page to check for existence.
	 * @return bool
	 */
	public static function existsLandingPage($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(id)
			 FROM analytics_landing_pages
			 WHERE id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get an aggregate
	 *
	 * @param string $name The name of the aggregate to look for.
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @return string
	 */
	public static function getAggregate($name, $startTimestamp, $endTimestamp)
	{
		$aggregates = self::getAggregates($startTimestamp, $endTimestamp);

		// aggregate exists
		if(isset($aggregates[$name])) return $aggregates[$name];

		// doesn't exist
		return '';
	}

	/**
	 * Get the aggregates between 2 dates
	 *
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @return array
	 */
	public static function getAggregates($startTimestamp, $endTimestamp)
	{
		// get data from cache
		$aggregates = self::getDataFromCacheByType('aggregates', $startTimestamp, $endTimestamp);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($aggregates === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		return $aggregates;
	}

	/**
	 * Get data by type from the cache
	 *
	 * @param string $type The type of data to get.
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @return array
	 */
	public static function getAggregatesFromCacheByType($type, $startTimestamp, $endTimestamp)
	{
		// doesnt exist in cache - load cache xml file
		if(!isset(self::$data[$type]['aggregates'])) self::$data = self::getCacheFile($startTimestamp, $endTimestamp);

		// return data is exists and false if not to get live data
		return (isset(self::$data[$type]['aggregates']) ? self::$data[$type]['aggregates'] : false);
	}

	/**
	 * Get the sites total aggregates
	 *
	 * startTimestamp and endTimestamp are needed so we can fetch the correct cache file
	 * They are not used when fetching the data from google.
	 *
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @return array
	 */
	public static function getAggregatesTotal($startTimestamp, $endTimestamp)
	{
		// get data from cache
		$aggregates = self::getDataFromCacheByType('aggregates_total', $startTimestamp, $endTimestamp);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($aggregates === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		return $aggregates;
	}

	/**
	 * Get attributes by type from the cache
	 *
	 * @param string $type The type of data of which to get the attributes.
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @return array
	 */
	private static function getAttributesFromCache($type, $startTimestamp, $endTimestamp)
	{
		// doesn't exist in cache
		if(!isset(self::$data[$type]['attributes']))
		{
			// load cache xml file
			self::$data = self::getCacheFile($startTimestamp, $endTimestamp);

			// doesnt exist in cache after loading the xml file so set to empty
			if(!isset(self::$data[$type]['attributes'])) self::$data[$type]['attributes'] = array();
		}

		return self::$data[$type]['attributes'];
	}

	/**
	 * Get cache file
	 *
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @return array
	 */
	private static function getCacheFile($startTimestamp, $endTimestamp)
	{
		$filename = (string) $startTimestamp . '_' . (string) $endTimestamp . '.xml';

		// file exists
		if(SpoonFile::exists(BACKEND_CACHE_PATH . '/analytics/' . $filename))
		{
			// get the xml (cast is important otherwise we cant use array_walk_recursive)
			$xml = simplexml_load_file(BACKEND_CACHE_PATH . '/analytics/' . $filename, 'SimpleXMLElement', LIBXML_NOCDATA);

			// parse xml to array
			return self::parseXMLToArray($xml);
		}

		// fallback (cache file doesn't exist)
		return array();
	}

	/**
	 * Fetch dashboard data grouped by day
	 *
	 * @param array $metrics The metrics to collect.
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @param bool[optional] $forceCache Should the data be forced from cache.
	 * @return array
	 */
	public static function getDashboardData(array $metrics, $startTimestamp, $endTimestamp, $forceCache = false)
	{
		$metrics = (array) $metrics;
		$forceCache = (bool) $forceCache;

		return self::getDataFromCacheByType('dashboard_data', $startTimestamp, $endTimestamp);
	}

	/**
	 * Get dashboard data from the cache
	 *
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @return array
	 */
	public static function getDashboardDataFromCache($startTimestamp, $endTimestamp)
	{
		// doesnt exist in cache - load cache xml file
		if(!isset(self::$dashboardData) || empty(self::$dashboardData))
		{
			self::$dashboardData = self::getCacheFile($startTimestamp, $endTimestamp);
		}

		return self::$dashboardData;
	}

	/**
	 * Get the top exit pages
	 *
	 * @param string $page The page.
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @return array
	 */
	public static function getDataForPage($page, $startTimestamp, $endTimestamp)
	{
		$db = BackendModel::getDB();

		// get id for this page
		$id = (int) $db->getVar(
			'SELECT id
			 FROM analytics_pages
			 WHERE page = ?',
			array((string) $page)
		);

		// no id? insert this page
		if($id === 0) $id = $db->insert('analytics_pages', array('page' => (string) $page));

		// get data from cache
		$items = array();
		$items['aggregates'] = self::getAggregatesFromCacheByType('page_' . $id, $startTimestamp, $endTimestamp);
		$items['entries'] = self::getDataFromCacheByType('page_' . $id, $startTimestamp, $endTimestamp);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items['aggregates'] === false || $items['entries'] === false) self::redirectToLoadingPage($action, array('page_id' => $id));

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		// update date_viewed for this page
		BackendAnalyticsModel::updatePageDateViewed($id);

		return $items;
	}

	/**
	 * Get data from the cache
	 *
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @return array
	 */
	public static function getDataFromCache($startTimestamp, $endTimestamp)
	{
		// doesnt exist in cache - load cache xml file
		if(!isset(self::$data) || empty(self::$data)) self::$data = self::getCacheFile($startTimestamp, $endTimestamp);

		return self::$data;
	}

	/**
	 * Get data by type from the cache
	 *
	 * @param string $type The type of data to get.
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @return array
	 */
	public static function getDataFromCacheByType($type, $startTimestamp, $endTimestamp)
	{
		// doesnt exist in cache
		if(!isset(self::$data[$type]))
		{
			// load cache xml file
			self::$data = self::getCacheFile($startTimestamp, $endTimestamp);

			// doesnt exist in cache after loading the xml file so set to false to get live data
			if(!isset(self::$data[$type])) return false;
		}

		return (isset(self::$data[$type]['entries']) ? self::$data[$type]['entries'] : self::$data[$type]);
	}

	/**
	 * Get the exit pages
	 *
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end Timestamp for the cache file.
	 * @return array
	 */
	public static function getExitPages($startTimestamp, $endTimestamp)
	{
		// get data from cache
		$items = self::getDataFromCacheByType('exit_pages', $startTimestamp, $endTimestamp);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		// init vars
		$results = array();

		// build top pages
		foreach($items as $i => $pageData)
		{
			// build array
			$results[$i] = array();
			$results[$i]['page'] = $pageData['pagePath'];
			$results[$i]['page_encoded'] = urlencode($pageData['pagePath']);
			$results[$i]['exits'] = (int) $pageData['exits'];
			$results[$i]['pageviews'] = (int) $pageData['pageviews'];
			$results[$i]['exit_rate'] = ($pageData['pageviews'] == 0 ? 0 : number_format(((int) $pageData['exits'] / $pageData['pageviews']) * 100, 2)) . '%';
		}

		return $results;
	}

	/**
	 * Fetch landing pages
	 *
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @param int[optional] $limit An optional limit of the number of landing pages to get.
	 * @return array
	 */
	public static function getLandingPages($startTimestamp, $endTimestamp, $limit = null)
	{
		$results = array();
		$db = BackendModel::getDB();

		// get data from database
		if($limit === null) $items = (array) $db->getRecords(
			'SELECT *, UNIX_TIMESTAMP(updated_on) AS updated_on
			 FROM analytics_landing_pages
			 ORDER BY entrances DESC'
		);

		else $items = (array) $db->getRecords(
			'SELECT *, UNIX_TIMESTAMP(updated_on) AS updated_on
			 FROM analytics_landing_pages
			 ORDER BY entrances DESC
			 LIMIT ?',
			array((int) $limit)
		);

		foreach($items as $item)
		{
			$result = array();
			$startDate = date('Y-m-d', $startTimestamp) . ' 00:00:00';
			$endDate = date('Y-m-d', $endTimestamp) . ' 00:00:00';

			// no longer up to date, not for the period we need - get new one
			if($item['updated_on'] < time() - 43200 || $item['start_date'] != $startDate || $item['end_date'] != $endDate)
			{
				// get metrics
				$metrics = BackendAnalyticsHelper::getMetricsForPage($item['page_path'], $startTimestamp, $endTimestamp);

				// build item
				$result['page_path'] = $item['page_path'];
				$result['entrances'] = (isset($metrics['entrances']) ? $metrics['entrances'] : 0);
				$result['bounces'] = (isset($metrics['bounces']) ? $metrics['bounces'] : 0);
				$result['bounce_rate'] = ($metrics['entrances'] == 0 ? 0 : number_format(((int) $metrics['bounces'] / $metrics['entrances']) * 100, 2)) . '%';
				$result['start_date'] = $startDate;
				$result['end_date'] = $endDate;
				$result['updated_on'] = date('Y-m-d H:i:s');

				// update record
				$db->update('analytics_landing_pages', $result, 'id = ?', $item['id']);
			}

			// correct data
			else $result = $item;

			// add encoded page path
			$result['page_encoded'] = urlencode($result['page_path']);

			// save record in results array
			$results[] = $result;
		}

		return $results;
	}

	/**
	 * Get all data for a given revision.
	 *
	 * @param string[optional] $language The language to use.
	 * @return array
	 */
	public static function getLinkList($language = null)
	{
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// there is no cache file
		if(!SpoonFile::exists(FRONTEND_CACHE_PATH . '/navigation/tinymce_link_list_' . $language . '.js')) return array();

		// read the cache file
		$cacheFile = SpoonFile::getContent(FRONTEND_CACHE_PATH . '/navigation/tinymce_link_list_' . $language . '.js');

		// get the array
		preg_match('/new Array\((.*)\);$/s', $cacheFile, $matches);

		// no matched
		if(empty($matches)) return array();

		// create array
		$matches = explode('],', str_replace('[', '', $matches[count($matches) - 1]));

		// init vars
		$cacheList = array();

		// loop list
		foreach($matches as $item)
		{
			// trim item
			$item = explode('", "', trim($item," \n\r\t\"]"));

			// build cache list
			$cacheList[$item[1]] = $item[0];
		}

		return $cacheList;
	}

	/**
	 * Fetch metrics grouped by day
	 *
	 * @param array $metrics The metrics to collect.
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @param string[optional] $forceCache Should the data be forced from cache.
	 * @return array
	 */
	public static function getMetricsPerDay(array $metrics, $startTimestamp, $endTimestamp, $forceCache = false)
	{
		$metrics = (array) $metrics;

		// get data from cache
		$items = self::getDataFromCacheByType('metrics_per_day', $startTimestamp, $endTimestamp);

		// force retrieval from cache
		if($forceCache) return $items;

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		return $items;
	}

	/**
	 * Fetch page by its path
	 *
	 * @param string $path The path of the page.
	 * @return array
	 */
	public static function getPageByPath($path)
	{
		return (array) BackendModel::getDB()->getRecord(
			'SELECT *
			 FROM analytics_pages
			 WHERE page = ?',
			array((string) $path)
		);
	}

	/**
	 * Get the page for a certain id
	 *
	 * @param int $pageId The page id to get the page for.
	 * @return string
	 */
	public static function getPageForId($pageId)
	{
		return (string) BackendModel::getDB()->getVar(
			'SELECT page
			 FROM analytics_pages
			 WHERE id = ?',
			array((int) $pageId)
		);
	}

	/**
	 * Get pages
	 *
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @return array
	 */
	public static function getPages($startTimestamp, $endTimestamp)
	{
		// get data from cache
		$items = self::getDataFromCacheByType('pages', $startTimestamp, $endTimestamp);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		// init vars
		$results = array();

		// build pages array
		foreach($items as $i => $item)
		{
			// build array
			$results[$i] = array();
			$results[$i]['page'] = $item['pagePath'];
			$results[$i]['page_encoded'] = urlencode($item['pagePath']);
			$results[$i]['pageviews'] = (int) $item['pageviews'];
			$results[$i]['pages_per_visit'] = ($item['visits'] == 0 ? 0 : number_format(((int) $item['pageviews'] / $item['visits']), 2));
			$results[$i]['time_on_site'] = BackendAnalyticsModel::getTimeFromSeconds(($item['entrances'] == 0 ? 0 : number_format(((int) $item['timeOnSite'] / $item['entrances']), 2)));
			$results[$i]['new_visits_percentage'] = ($item['visits'] == 0 ? 0 : number_format(((int) $item['newVisits'] / $item['visits']) * 100, 2)) . '%';
			$results[$i]['bounce_rate'] = ($item['entrances'] == 0 ? 0 : number_format(((int) $item['bounces'] / $item['entrances']) * 100, 2)) . '%';
		}

		return $results;
	}

	/**
	 * Get the most recent keywords
	 *
	 * @return string
	 */
	public static function getRecentKeywords()
	{
		return (array) BackendModel::getDB()->getRecords(
			'SELECT *
			 FROM analytics_keywords
			 ORDER BY entrances DESC, id'
		);
	}

	/**
	 * Get the most recent referrers
	 *
	 * @return string
	 */
	public static function getRecentReferrers()
	{
		$items = (array) BackendModel::getDB()->getRecords(
			'SELECT *
			 FROM analytics_referrers
			 ORDER BY entrances DESC, id'
		);

		foreach($items as $key => $item)
		{
			// assign URL
			$items[$key]['url'] = 'http://' . $item['referrer'];

			// wordwrap referrer
			$items[$key]['referrer'] = wordwrap($item['referrer'], 50, ' ', true);
		}

		return $items;
	}

	/**
	 * Get the selected table id
	 *
	 * @return string
	 */
	public static function getTableId()
	{
		return (string) BackendAnalyticsHelper::getGoogleAnalyticsInstance()->getTableId();
	}

	/**
	 * Get time from seconds
	 *
	 * @param int $seconds The seconds to format.
	 * @return string H:i:s
	 */
	public static function getTimeFromSeconds($seconds)
	{
		$seconds = (int) ceil($seconds);

		// get seconds
		$timeHours = (int) floor($seconds / 3600);
		$timeMinutes = (int) floor(($seconds - ($timeHours * 3600)) / 60);
		$timeSeconds = (int) floor($seconds - ($timeHours * 3600) - ($timeMinutes * 60));

		// return formatted time
		return str_pad($timeHours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($timeMinutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($timeSeconds, 2, '0', STR_PAD_LEFT);
	}

	/**
	 * Get the top exit pages
	 *
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @param int[optional] $limit An optional limit of the number of exit pages to get.
	 * @return array
	 */
	public static function getTopExitPages($startTimestamp, $endTimestamp, $limit = 5)
	{
		// get data from cache
		$items = self::getDataFromCacheByType('top_exit_pages', $startTimestamp, $endTimestamp);

		// limit data
		if(!empty($items)) $items = array_slice($items, 0, $limit, true);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		// init vars
		$results = array();

		// build top pages
		foreach($items as $i => $pageData)
		{
			// build array
			$results[$i] = array();
			$results[$i]['page'] = $pageData['pagePath'];
			$results[$i]['page_encoded'] = urlencode($pageData['pagePath']);
			$results[$i]['exits'] = (int) $pageData['exits'];
			$results[$i]['pageviews'] = (int) $pageData['pageviews'];
		}

		return $results;
	}

	/**
	 * Get the top keywords
	 *
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @param int[optional] $limit An optional limit of the number of keywords to get.
	 * @return array
	 */
	public static function getTopKeywords($startTimestamp, $endTimestamp, $limit = 5)
	{
		// get data from cache
		$items = self::getDataFromCacheByType('top_keywords', $startTimestamp, $endTimestamp);

		// limit data
		if(!empty($items)) $items = array_slice($items, 0, $limit, true);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		$results = array();

		// get total pageviews
		$totalPageviews = (int) self::getAggregate('keywordPageviews', $startTimestamp, $endTimestamp);

		// build top keywords
		foreach($items as $i => $keywordData)
		{
			// build array
			$results[$i] = array();
			$results[$i]['keyword'] = (mb_strlen($keywordData['keyword']) <= 45 ? $keywordData['keyword'] : mb_substr($keywordData['keyword'], 0, 45) . '…');
			$results[$i]['pageviews'] = (int) $keywordData['pageviews'];
			$results[$i]['pageviews_percentage'] = ($totalPageviews == 0 ? '0' : number_format(((int) $keywordData['pageviews'] / $totalPageviews) * 100, 2)) . '%';
		}

		return $results;
	}

	/**
	 * Get the top pages
	 *
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @param int[optional] $limit An optional limit of the number of pages to get.
	 * @return array
	 */
	public static function getTopPages($startTimestamp, $endTimestamp, $limit = 5)
	{
		// get data from cache
		$items = self::getDataFromCacheByType('top_pages', $startTimestamp, $endTimestamp);

		// limit data
		if(!empty($items)) $items = array_slice($items, 0, $limit, true);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		// init vars
		$results = array();

		// get total pageviews
		$totalPageviews = (int) self::getAggregate('pageviews', $startTimestamp, $endTimestamp);

		// build top pages
		foreach($items as $i => $pageData)
		{
			// build array
			$results[$i] = array();
			$results[$i]['page'] = $pageData['pagePath'];
			$results[$i]['page_encoded'] = urlencode($pageData['pagePath']);
			$results[$i]['pageviews'] = (int) $pageData['pageviews'];
			$results[$i]['pageviews_percentage'] = ($totalPageviews == 0 ? '0' : number_format(($pageData['pageviews'] / $totalPageviews) * 100, 2)) . '%';
		}

		return $results;
	}

	/**
	 * Get the top referrals
	 *
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @param int[optional] $limit An optional limit of the number of referrals to get.
	 * @return array
	 */
	public static function getTopReferrals($startTimestamp, $endTimestamp, $limit = 5)
	{
		// get data from cache
		$items = self::getDataFromCacheByType('top_referrals', $startTimestamp, $endTimestamp);

		// limit data
		if(!empty($items)) $items = array_slice($items, 0, $limit, true);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		// init
		$results = array();

		// get total pageviews
		$totalPageviews = (int) self::getAggregate('pageviews', $startTimestamp, $endTimestamp);

		// build top keywords
		foreach($items as $i => $referrerData)
		{
			// build array
			$results[$i] = array();
			$results[$i]['referral'] = (mb_strlen($referrerData['referrer']) <= 45 ? trim($referrerData['referrer'], '/') : trim(mb_substr($referrerData['referrer'], 0, 45), '/') . '…');
			$results[$i]['referral_long'] = trim($referrerData['referrer'], '/');
			$results[$i]['pageviews'] = (int) $referrerData['pageviews'];
			$results[$i]['pageviews_percentage'] = ($totalPageviews == 0 ? '0' : number_format(((int) $referrerData['pageviews'] / $totalPageviews) * 100, 2)) . '%';
		}

		return $results;
	}

	/**
	 * Get the traffic sources grouped by medium
	 *
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 * @return array
	 */
	public static function getTrafficSourcesGrouped($startTimestamp, $endTimestamp)
	{
		// get data from cache
		$items = self::getDataFromCacheByType('traffic_sources', $startTimestamp, $endTimestamp);

		// get current action
		$action = Spoon::get('url')->getAction();

		// nothing in cache
		if($items === false) self::redirectToLoadingPage($action);

		// reset loop counter for the current action if we got data from cache
		SpoonSession::set($action . 'Loop', null);

		return $items;
	}

	/**
	 * Inserts a landingpage into the database
	 *
	 * @param array $item The data to insert.
	 * @return int
	 */
	public static function insertLandingPage(array $item)
	{
		return (int) BackendModel::getDB(true)->insert('analytics_landing_pages', $item);
	}

	/**
	 * Parse a XML object to an array and cast all fields to their corresponding types
	 *
	 * @param SimpleXMLElement $xml The simpleXML to convert to an array.
	 * @return array
	 */
	private static function parseXMLToArray(SimpleXMLElement $xml)
	{
		$data = array();
		$xml = (array) $xml;

		// loop children
		foreach($xml as $name => $children)
		{
			$children = (array) $children;

			// skip attributes
			if($name == '@attributes') continue;

			// empty item
			if(trim((string) $children) == '')
			{
				// save empty array
				$data[$name] = array();
				continue;
			}

			// save attributes
			if(isset($children['@attributes']) && is_array($children['@attributes'])) $data[$name]['attributes'] = $children['@attributes'];

			// page details
			if(strpos($name, 'page_') !== false)
			{
				// loop entries
				foreach($children as $pageKey => $pageChildren)
				{
					// this is the hostname - add to data
					if($pageKey == 'hostname') $data[$name][$pageKey] = trim($pageChildren);

					// cast children to array
					$pageChildren = (array) $pageChildren;

					// dig deeper
					if(isset($pageChildren['entry']) && is_array($pageChildren['entry']))
					{
						// loop entries
						foreach($pageChildren['entry'] as $entry)
						{
							// cast to array
							$entry = (array) $entry;

							// entry with casted elements
							$entryCasted = array();

							// cast and add each element
							foreach($entry as $entryName => $entryValue) $entryCasted[$entryName] = (string) $entryValue;

							// add to data
							$data[$name][$pageKey][] = $entryCasted;
						}
					}

					// normal item
					else
					{
						// loop children
						foreach($pageChildren as $childName => $childValue)
						{
							// empty item - skip
							if($childName == '@attributes' || trim((string) $childValue) == '') continue;

							// cast and add item
							$data[$name][$pageKey][$childName] = (string) $childValue;
						}
					}
				}
			}

			// dig deeper
			elseif(isset($children['entry']) && is_array($children['entry']))
			{
				// loop entries
				foreach($children['entry'] as $entry)
				{
					// cast to array
					$entry = (array) $entry;

					// entry with casted elements
					$entryCasted = array();

					// cast and add each element
					foreach($entry as $entryName => $entryValue) $entryCasted[$entryName] = (string) $entryValue;

					// add to data
					$data[$name]['entries'][] = $entryCasted;
				}
			}

			// normal item
			else
			{
				// loop children
				foreach($children as $childName => $childValue)
				{
					// attributes - skip
					if($childName === '@attributes') continue;

					// empty item
					if(trim((string) $childValue) == '')
					{
						// save empty array
						$data[$name] = array();

						// continue
						continue 2;
					}

					// cast and add item
					$data[$name][$childName] = (string) $childValue;
				}
			}
		}

		return $data;
	}

	/**
	 * Redirect to the loading page after checking for infinite loops.
	 *
	 * @param string $action The action to check for infinite loops.
	 * @param array[optional] $extraParameters The extra parameters to append to the redirect url.
	 */
	public static function redirectToLoadingPage($action, array $extraParameters = array())
	{
		// get loop counter
		$counter = (SpoonSession::exists($action . 'Loop') ? SpoonSession::get($action . 'Loop') : 0);

		// loop has run too long - throw exception
		if($counter > 2) throw new BackendException('An infinite loop has been detected while getting data from cache for the action "' . $action . '".');

		// set new counter
		SpoonSession::set($action . 'Loop', ++$counter);

		// put parameters into a string
		$extraParameters = (empty($extraParameters) ? '' : '&' . http_build_query($extraParameters));

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('loading', 'analytics'))
		{
			// redirect to loading page which will get the needed data based on the current action
			SpoonHTTP::redirect(BackendModel::createURLForAction('loading') . '&redirect_action=' . $action . $extraParameters);
		}
	}

	/**
	 * Remove all cache files
	 */
	public static function removeCacheFiles()
	{
		$cachePath = BACKEND_CACHE_PATH . '/analytics';

		// delete all cache files
		foreach(SpoonFile::getList($cachePath) as $file)
		{
			SpoonFile::delete($cachePath . '/' . $file);
		}
	}

	/**
	 * Updates the date viewed for a certain page.
	 *
	 * @param int $pageId The id of the page to update.
	 */
	public static function updatePageDateViewed($pageId)
	{
		BackendModel::getDB(true)->update(
			'analytics_pages',
			array('date_viewed' => SpoonDate::getDate('Y-m-d H:i:s')),
			'id = ?',
			array((int) $pageId)
		);
	}

	/**
	 * Write data to cache file
	 *
	 * @param array $data The data to write to the cache file.
	 * @param int $startTimestamp The start timestamp for the cache file.
	 * @param int $endTimestamp The end timestamp for the cache file.
	 */
	public static function writeCacheFile(array $data, $startTimestamp, $endTimestamp)
	{
		$xml = "<?xml version='1.0' encoding='" . SPOON_CHARSET . "'?>\n";
		$xml .= "<analytics start_timestamp=\"" . $startTimestamp . "\" end_timestamp=\"" . $endTimestamp . "\">\n";

		// loop data
		foreach($data as $type => $records)
		{
			$attributes = array();

			// there are some attributes
			if(isset($records['attributes']) && !empty($records['attributes']))
			{
				// loop em
				foreach($records['attributes'] as $key => $value)
				{
					// add to the attributes string
					$attributes[] = $key . '="' . $value . '"';
				}
			}

			$xml .= "\t<" . $type . (!empty($attributes) ? ' ' . implode(' ', $attributes) : '') . ">\n";

			// we're not dealing with a page detail
			if(strpos($type, 'page_') === false)
			{
				// get items
				$items = (isset($records['entries']) ? $records['entries'] : $records);

				// loop data
				foreach($items as $key => $value)
				{
					// skip empty items
					if((is_array($value) && empty($value)) || trim((string) $value) === '') continue;

					// value contains an array
					if(is_array($value))
					{
						// there are values
						if(!empty($value))
						{
							// build xml
							$xml .= "\t\t<entry>\n";

							// loop data
							foreach($value as $entryKey => $entryValue)
							{
								// build xml
								$xml .= "\t\t\t<" . $entryKey . "><![CDATA[" . $entryValue . "]]></" . $entryKey . ">\n";
							}

							// end xml element
							$xml .= "\t\t</entry>\n";
						}
					}

					// build xml
					else $xml .= "\t\t<" . $key . ">" . $value . "</" . $key . ">\n";
				}
			}

			// we're dealing with a page detail
			else
			{
				// loop data
				foreach($records as $subkey => $subitems)
				{
					// build xml
					$xml .= "\t\t<" . $subkey . ">\n";

					// subitems is an array
					if(is_array($subitems))
					{
						// loop data
						foreach($subitems as $key => $value)
						{
							// skip empty items
							if((is_array($value) && empty($value)) || trim((string) $value) === '') continue;

							// value contains an array
							if(is_array($value))
							{
								// there are values
								if(!empty($value))
								{
									// build xml
									$xml .= "\t\t\t<entry>\n";

									// loop data
									foreach($value as $entryKey => $entryValue)
									{
										// build xml
										$xml .= "\t\t\t\t<" . $entryKey . "><![CDATA[" . $entryValue . "]]></" . $entryKey . ">\n";
									}

									// end xml element
									$xml .= "\t\t\t</entry>\n";
								}
							}

							// build xml
							else $xml .= "\t\t<" . $key . ">" . $value . "</" . $key . ">\n";
						}
					}

					// not an array
					else $xml .= "<![CDATA[" . (string) $subitems . "]]>";

					// end xml element
					$xml .= "\t\t</" . $subkey . ">\n";
				}
			}

			// end xml element
			$xml .= "\t</" . $type . ">\n";
		}

		// end xml string
		$xml .= "</analytics>";

		// perform checks for valid xml and throw exception if needed
		$simpleXml = @simplexml_load_string($xml);
		if($simpleXml === false) throw new BackendException('The xml of the cache file is invalid.');

		$filename = $startTimestamp . '_' . $endTimestamp . '.xml';
		SpoonFile::setContent(BACKEND_CACHE_PATH . '/analytics/' . $filename, $xml);
	}
}