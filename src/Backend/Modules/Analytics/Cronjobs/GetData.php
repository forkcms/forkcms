<?php

namespace Backend\Modules\Analytics\Cronjobs;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Backend\Core\Engine\Base\Cronjob as BackendBaseCronjob;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Analytics\Engine\Helper as BackendAnalyticsHelper;
use Backend\Modules\Analytics\Engine\Model as BackendAnalyticsModel;

/**
 * This cronjob will fetch the requested data
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class GetData extends BackendBaseCronjob
{
    /**
     * The path to the analytics cache files
     *
     * @var string
     */
    private $cachePath;

    /**
     * Cleanup cache files
     */
    private function cleanupCache()
    {
        $finder = new Finder();
        $fs = new Filesystem();
        foreach ($finder->files()->in($this->cachePath) as $file) {
            /** @var $file \SplFileInfo */
            // delete file if more than 1 week old
            if ($file->getMTime() < strtotime('-1 week')) {
                $fs->remove($file->getRealPath());
            }
        }
    }

    /**
     * Cleanup database
     */
    private function cleanupDatabase()
    {
        $this->get('database')->delete(
            'analytics_pages',
            'date_viewed < ?',
            array(\SpoonDate::getDate('Y-m-d H:i:s', strtotime('-1 week')))
        );
    }

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->cachePath = BACKEND_CACHE_PATH . '/Analytics';

        // get parameters
        $page = trim(\SpoonFilter::getGetValue('page', null, ''));
        $pageId = trim(\SpoonFilter::getGetValue('page_id', null, ''));
        $identifier = trim(\SpoonFilter::getGetValue('identifier', null, ''));
        $startTimestamp = (int) trim(\SpoonFilter::getGetValue('start_date', null, ''));
        $endTimestamp = (int) trim(\SpoonFilter::getGetValue('end_date', null, ''));
        $force = trim(\SpoonFilter::getGetValue('force', array('Y', 'N'), 'N')) == 'Y';
        $filename = null;

        // no parameters given? cronjob called
        if ($page == '' && $identifier == '' && $startTimestamp === 0 && $endTimestamp === 0) {
            // is everything still set?
            if (BackendAnalyticsHelper::getStatus() != 'UNAUTHORIZED') {
                $interval = BackendModel::getModuleSetting('Analytics', 'interval', 'week');
                if ($interval == 'week') {
                    $interval .= ' -2 days';
                }

                $page = 'all';
                $startTimestamp = strtotime('-1' . $interval);
                $endTimestamp = time();
            }
        } elseif ($page != '' && $identifier != '' && $startTimestamp !== 0 && $endTimestamp !== 0) {
            // all parameters given? curl called
            $filename = $this->cachePath . '/' . $page . ($pageId != '' ? '_' . $pageId : '')
                . '_' . $identifier . '.txt';
            $fs = new Filesystem();

            // is everything still set?
            if (BackendAnalyticsHelper::getStatus() != 'UNAUTHORIZED') {
                // create temporary file to indicate we're getting data
                $fs->dumpFile($filename, 'busy1');
            } else {
                // no longer authorized: set status in cache
                $fs->dumpFile($filename, 'unauthorized');

                return;
            }
        } else {
            // some parameters aren't given? throw exception
            throw new \SpoonException('Some parameters are missing.');
        }

        $this->getDashboardData();
        $this->getData($startTimestamp, $endTimestamp, $force, $page, $pageId, $filename);
        $this->cleanupCache();
        $this->cleanupDatabase();
    }

    /**
     * Get data from analytics
     */
    private function getDashboardData()
    {
        try {
            $startTimestamp = strtotime('-1 week -1 days', mktime(0, 0, 0));
            $endTimestamp = mktime(0, 0, 0);

            // get data from cache
            $data = BackendAnalyticsModel::getDashboardDataFromCache($startTimestamp, $endTimestamp);

            // nothing in cache - fetch from google and set cache
            if (!isset($data['dashboard_data'])) {
                $data['dashboard_data']['entries'] = BackendAnalyticsHelper::getDashboardData(
                    $startTimestamp,
                    $endTimestamp
                );
            }

            // update cache file
            BackendAnalyticsModel::writeCacheFile($data, $startTimestamp, $endTimestamp);
        } catch (\Exception $e) {
            throw new \SpoonException('Something went wrong while getting dashboard data.');
        }
    }

    /**
     * Get data from analytics
     *
     * @param int    $startTimestamp The start timestamp for the data to collect.
     * @param int    $endTimestamp   The end timestamp for the data to collect.
     * @param bool   $force          Force getting data. Don't rely on cache.
     * @param string $page           The page to get data for.
     * @param string $pageId         The id of the page to get data for.
     * @param string $filename       The name of the cache file.
     */
    private function getData(
        $startTimestamp,
        $endTimestamp,
        $force = false,
        $page = 'all',
        $pageId = null,
        $filename = null
    ) {
        $fs = new Filesystem();

        try {
            // get data from cache
            $data = BackendAnalyticsModel::getDataFromCache($startTimestamp, $endTimestamp);

            // nothing in cache - fetch from google and set cache
            if (!isset($data['aggregates']) || $force) {
                $data['aggregates'] = BackendAnalyticsHelper::getAggregates(
                    $startTimestamp,
                    $endTimestamp
                );
            }

            // nothing in cache - fetch from google and set cache
            if (!isset($data['aggregates_total']) || $force) {
                $data['aggregates_total'] = BackendAnalyticsHelper::getAggregates(
                    mktime(0, 0, 0, 1, 1, 2005),
                    mktime(0, 0, 0)
                );
            }

            // nothing in cache - fetch from google and set cache
            if (!isset($data['metrics_per_day']) || $force) {
                $data['metrics_per_day']['entries'] = BackendAnalyticsHelper::getMetricsPerDay(
                    $startTimestamp,
                    $endTimestamp
                );
            }

            // @todo refactor the code below. Isnt a switch statement more suitable?

            // traffic sources, top keywords and top referrals on index page
            if ($page == 'all' || $page == 'Index') {
                // nothing in cache - fetch from google and set cache
                if (!isset($data['traffic_sources']) || $force) {
                    $data['traffic_sources']['entries'] = BackendAnalyticsHelper::getTrafficSourcesGrouped(
                        array('pageviews'),
                        $startTimestamp,
                        $endTimestamp,
                        'pageviews'
                    );
                }

                // nothing in cache
                if (!isset($data['top_keywords']) || $force) {
                    // fetch from google and use a safe limit
                    $gaResults = BackendAnalyticsHelper::getKeywords(
                        'pageviews',
                        $startTimestamp,
                        $endTimestamp,
                        'pageviews',
                        50
                    );

                    // set cache
                    $data['top_keywords']['entries'] = $gaResults['entries'];
                }

                // nothing in cache
                if (!isset($data['top_referrals']) || $force) {
                    // fetch from google and use a safe limit
                    $gaResults = BackendAnalyticsHelper::getReferrals(
                        'pageviews',
                        $startTimestamp,
                        $endTimestamp,
                        'pageviews',
                        50
                    );

                    // init vars
                    $topReferrals = array();

                    // add entries to items
                    foreach ($gaResults['entries'] as $entry) {
                        $topReferrals[] = array(
                            'referrer' => $entry['source'] . $entry['referralPath'],
                            'pageviews' => $entry['pageviews']
                        );
                    }

                    // set cache
                    $data['top_referrals']['entries'] = $topReferrals;
                }
            }

            // top pages on index and content page
            if ($page == 'all' || $page == 'Index' || $page == 'Content') {
                // nothing in cache
                if (!isset($data['top_pages']) || $force) {
                    // fetch from google and use a safe limit
                    $gaResults = BackendAnalyticsHelper::getPages(
                        'pageviews',
                        $startTimestamp,
                        $endTimestamp,
                        'pageviews',
                        50
                    );

                    // set cache
                    $data['top_pages']['entries'] = $gaResults['entries'];
                }
            }

            // top exit pages on content page
            if ($page == 'all' || $page == 'Content') {
                // nothing in cache
                if (!isset($data['top_exit_pages']) || $force) {
                    // fetch from google
                    $gaResults = BackendAnalyticsHelper::getPages(
                        array('exits', 'pageviews'),
                        $startTimestamp,
                        $endTimestamp,
                        'exits',
                        50
                    );

                    // set cache
                    $data['top_exit_pages']['entries'] = $gaResults['entries'];
                }
            }

            // top exit pages on all pages page
            if ($page == 'all' || $page == 'AllPages') {
                // nothing in cache
                if (!isset($data['pages']) || $force) {
                    // fetch from google
                    $gaResults = BackendAnalyticsHelper::getPages(
                        array('bounces', 'entrances', 'exits', 'newVisits', 'pageviews', 'timeOnSite', 'visits'),
                        $startTimestamp,
                        $endTimestamp,
                        'pageviews',
                        50
                    );

                    // set cache
                    $data['pages']['entries'] = $gaResults['entries'];
                    $data['pages']['attributes'] = array(
                        'totalResults' => isset($gaResults['totalResults']) ? $gaResults['totalResults'] : 0
                    );
                }
            }

            // exit pages on exit pages page
            if ($page == 'all' || $page == 'ExitPages') {
                // nothing in cache
                if (!isset($data['exit_pages']) || $force) {
                    // fetch from google
                    $gaResults = BackendAnalyticsHelper::getExitPages(
                        array('bounces', 'entrances', 'exits', 'newVisits', 'pageviews', 'timeOnSite', 'visits'),
                        $startTimestamp,
                        $endTimestamp,
                        'exits',
                        50
                    );

                    // set cache
                    $data['exit_pages']['entries'] = $gaResults['entries'];
                }
            }

            // detail page
            if ($page == 'DetailPage') {
                // nothing in cache
                if (!isset($data['page' . $pageId]) || $force) {
                    // fetch from google
                    $gaResults = BackendAnalyticsHelper::getDataForPage($pageId, $startTimestamp, $endTimestamp);

                    // set cache
                    $data['page_' . $pageId] = $gaResults;
                }
            }

            // update cache file
            BackendAnalyticsModel::writeCacheFile($data, $startTimestamp, $endTimestamp);
        } catch (\Exception $e) {
            // set file content to indicate something went wrong if needed
            if (isset($filename)) {
                $fs->dumpFile($filename, 'error');
            } else {
                throw new \SpoonException('Something went wrong while getting data.');
            }
        }

        // remove temporary file if needed
        if (isset($filename)) {
            $fs->dumpFile($filename, 'done');
        }
    }
}
