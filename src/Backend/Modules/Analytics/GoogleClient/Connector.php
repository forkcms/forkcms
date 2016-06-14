<?php

namespace Backend\Modules\Analytics\GoogleClient;

use Common\ModulesSettings;
use Google_Service_Analytics;
use Psr\Cache\CacheItemPoolInterface;

/**
 * The class that will do query's on the google analytics API
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
final class Connector
{
    /**
     * @var Google_Service_Analytics
     */
    private $analytics;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var ModulesSettings
     */
    private $settings;

    public function __construct(
        Google_Service_Analytics $analytics,
        CacheItemPoolInterface $cache,
        ModulesSettings $settings
    ) {
        $this->analytics = $analytics;
        $this->cache = $cache;
        $this->settings = $settings;
    }

    /**
     * Returns the amount of pageviews in a period
     *
     * @param  int $startDate
     * @param  int $endDate
     *
     * @return int
     */
    public function getPageViews($startDate, $endDate)
    {
        $results = $this->getData($startDate, $endDate);

        return $results['metrics']['ga:pageviews'];
    }

    /**
     * Returns the amount of visitors in a period
     *
     * @param  int $startDate
     * @param  int $endDate
     *
     * @return int
     */
    public function getVisitors($startDate, $endDate)
    {
        $results = $this->getData($startDate, $endDate);

        return $results['metrics']['ga:users'];
    }

    /**
     * Returns the amount of pages per visit in a period
     *
     * @param  int $startDate
     * @param  int $endDate
     *
     * @return float
     */
    public function getPagesPerVisit($startDate, $endDate)
    {
        $results = $this->getData($startDate, $endDate);

        return $results['metrics']['ga:pageviewsPerSession'];
    }

    /**
     * Returns the average time on the site in a certain period
     *
     * @param  int $startDate
     * @param  int $endDate
     *
     * @return float
     */
    public function getTimeOnSite($startDate, $endDate)
    {
        $results = $this->getData($startDate, $endDate);

        return $results['metrics']['ga:avgSessionDuration'];
    }

    /**
     * Returns the percentage of new sessions in a certain period
     *
     * @param  int $startDate
     * @param  int $endDate
     *
     * @return float
     */
    public function getNewSessionsPercentage($startDate, $endDate)
    {
        $results = $this->getData($startDate, $endDate);

        return $results['metrics']['ga:percentNewSessions'];
    }

    /**
     * Returns the bounce rate in a certain period
     *
     * @param  int $startDate
     * @param  int $endDate
     *
     * @return float
     */
    public function getBounceRate($startDate, $endDate)
    {
        $results = $this->getData($startDate, $endDate);

        return $results['metrics']['ga:bounceRate'];
    }

    /**
     * Returns the visitors graph data
     *
     * @param  int $startDate
     * @param  int $endDate
     *
     * @return array
     */
    public function getVisitorsGraphData($startDate, $endDate)
    {
        $results = $this->getData($startDate, $endDate);

        return $results['visitGraphData'];
    }

    /**
     * Returns the source graph data
     *
     * @param  int $startDate
     * @param  int $endDate
     *
     * @return array
     */
    public function getSourceGraphData($startDate, $endDate)
    {
        $results = $this->getData($startDate, $endDate);

        return $results['sourceGraphData'];
    }

    /**
     * Returns the source graph data
     *
     * @param  int $startDate
     * @param  int $endDate
     *
     * @return array
     */
    public function getMostVisitedPagesData($startDate, $endDate)
    {
        $results = $this->getData($startDate, $endDate);

        return $results['pageViews'];
    }

    /**
     * Fetches all the needed data and caches it in our statistics array
     *
     * @param  int $startDate
     * @param  int $endDate
     *
     * @return array
     */
    private function getData($startDate, $endDate)
    {
        $dateRange = $startDate . '-' . $endDate;

        $item = $this->cache->getItem('analytics-' . $dateRange);
        if ($item->isHit()) {
            return $item->get();
        }

        $data = array(
            'metrics' => $this->getMetrics($startDate, $endDate),
            'visitGraphData' => $this->collectVisitGraphData($startDate, $endDate),
            'pageViews' => $this->collectMostVisitedPagesData($startDate, $endDate),
            'sourceGraphData' => $this->collectSourceGraphData($startDate, $endDate),
        );

        $item->set($data);
        $this->cache->save($item);

        return $data;
    }

    /**
     * Fetches some metrics for a certain date range
     *
     * @param  int $startDate
     * @param  int $endDate
     *
     * @return array
     */
    private function getMetrics($startDate, $endDate)
    {
        $metrics = $this->getAnalyticsData(
            $startDate,
            $endDate,
            'ga:pageviews,ga:users,ga:pageviewsPerSession,ga:avgSessionDuration,ga:percentNewSessions,ga:bounceRate'
        );

        return $metrics['totalsForAllResults'];
    }

    /**
     * Fetches the data needed to build the visitors graph for a date range
     *
     * @param  int $startDate
     * @param  int $endDate
     *
     * @return array
     */
    private function collectVisitGraphData($startDate, $endDate)
    {
        $visitGraphData = $this->getAnalyticsData(
            $startDate,
            $endDate,
            'ga:pageviews,ga:users',
            array(
                'dimensions' => 'ga:date',
                'sort' => 'ga:date',
            )
        );

        // make sure our column headers are the metric names, not just numbers
        $namedRows = array();
        foreach ($visitGraphData['rows'] as $dataRow) {
            $namedRow = array();
            foreach ($dataRow as $key => $value) {
                $headerName = $visitGraphData['columnHeaders'][$key]['name'];

                // convert the date to a timestamp
                if ($headerName === 'ga:date') {
                    $value = \DateTime::createFromFormat('Ymd H:i:s', $value . ' 00:00:00')->format('U');
                }
                $namedRow[str_replace(':', '_', $headerName)] = $value;
            }
            $namedRows[] = $namedRow;
        }

        return $namedRows;
    }

    /**
     * Fetches the data needed to build the source graph for a date range
     *
     * @param  int $startDate
     * @param  int $endDate
     *
     * @return array
     */
    private function collectSourceGraphData($startDate, $endDate)
    {
        $sourceGraphData = $this->getAnalyticsData(
            $startDate,
            $endDate,
            'ga:pageviews',
            array(
                'dimensions' => 'ga:medium',
                'sort' => '-ga:pageviews',
            )
        );

        // make sure our column headers are the metric names, not just numbers
        $namedRows = array();
        foreach ($sourceGraphData['rows'] as $dataRow) {
            $namedRow = array();
            foreach ($dataRow as $key => $value) {
                $headerName = $sourceGraphData['columnHeaders'][$key]['name'];
                $namedRow[str_replace(':', '_', $headerName)] = $value;
            }
            $namedRows[] = $namedRow;
        }

        return $namedRows;
    }

    /**
     * Fetches the data needed to build the list with most visited pages
     *
     * @param  int $startDate
     * @param  int $endDate
     *
     * @return array
     */
    private function collectMostVisitedPagesData($startDate, $endDate)
    {
        $sourceGraphData = $this->getAnalyticsData(
            $startDate,
            $endDate,
            'ga:pageviews',
            array(
                'dimensions' => 'ga:pagePath',
                'sort' => '-ga:pageviews',
                'max-results' => 20,
            )
        );

        // make sure our column headers are the metric names, not just numbers
        $namedRows = array();
        foreach ($sourceGraphData['rows'] as $dataRow) {
            $namedRow = array();
            foreach ($dataRow as $key => $value) {
                $headerName = $sourceGraphData['columnHeaders'][$key]['name'];
                $namedRow[str_replace(':', '_', $headerName)] = $value;
            }
            $namedRows[] = $namedRow;
        }

        return $namedRows;
    }

    /**
     * Returns Analytics data for our coupled profile
     *
     * @param  int $startDate
     * @param  int $endDate
     * @param  string $metrics A comma-separated list of Analytics metrics.
     * @param  array $optParams Optional parameters.
     *
     * @return Google_Service_Analytics_GaData
     */
    private function getAnalyticsData($startDate, $endDate, $metrics, $optParams = array())
    {
        return $this->analytics->data_ga->get(
            'ga:' . $this->settings->get('Analytics', 'profile'),
            date('Y-m-d', $startDate),
            date('Y-m-d', $endDate),
            $metrics,
            $optParams
        );
    }
}
