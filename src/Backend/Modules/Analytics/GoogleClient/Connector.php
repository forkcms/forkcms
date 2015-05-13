<?php

namespace Backend\Modules\Analytics\GoogleClient;

use Backend\Core\Engine\Model;
use Google_Service_Analytics;

/**
 * The class that will do query's on the google analytics API
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
final class Connector
{
    private $analytics;
    private $statistics = array();

    public function __construct(Google_Service_Analytics $analytics)
    {
        $this->analytics = $analytics;
    }

    /**
     * Returns the amount of pageviews in a period
     *
     * @param  int $startData
     * @param  int $endDate
     * @return int
     */
    public function getPageViews($startDate, $endDate)
    {
        $results = $this->getData($startDate, $endDate);

        return $results['ga:pageviews'];
    }

    /**
     * Returns the amount of visitors in a period
     *
     * @param  int $startData
     * @param  int $endDate
     * @return int
     */
    public function getVisitors($startDate, $endDate)
    {
        $results = $this->getData($startDate, $endDate);

        return $results['ga:users'];
    }

    /**
     * Returns the amount of pages per visit in a period
     *
     * @param  int $startData
     * @param  int $endDate
     * @return float
     */
    public function getPagesPerVisit($startDate, $endDate)
    {
        $results = $this->getData($startDate, $endDate);

        return $results['ga:pageviewsPerSession'];
    }

    /**
     * Returns the average time on the site in a certain period
     *
     * @param  int $startData
     * @param  int $endDate
     * @return float
     */
    public function getTimeOnSite($startDate, $endDate)
    {
        $results = $this->getData($startDate, $endDate);

        return $results['ga:avgSessionDuration'];
    }

    /**
     * Returns the percentage of new sessions in a certain period
     *
     * @param  int $startData
     * @param  int $endDate
     * @return float
     */
    public function getNewSessionsPercentage($startDate, $endDate)
    {
        $results = $this->getData($startDate, $endDate);

        return $results['ga:percentNewSessions'];
    }

    /**
     * Returns the bounce rate in a certain period
     *
     * @param  int $startData
     * @param  int $endDate
     * @return float
     */
    public function getBounceRate($startDate, $endDate)
    {
        $results = $this->getData($startDate, $endDate);

        return $results['ga:bounceRate'];
    }

    /**
     * Fetches all the needed data and caches it in our statistics array
     *
     * @param  int $startData
     * @param  int $endDate
     * @return array
     */
    private function getData($startDate, $endDate)
    {
        $dateRange = $startDate . '-' . $endDate;

        if (!array_key_exists($dateRange, $this->statistics)) {
            $results = $this->analytics->data_ga->get(
                'ga:' . Model::getModuleSetting('Analytics', 'profile'),
                date('Y-m-d', $startDate),
                date('Y-m-d', $endDate),
                'ga:pageviews,ga:users,ga:pageviewsPerSession,ga:avgSessionDuration,ga:percentNewSessions,ga:bounceRate'
            );

            $this->statistics[$dateRange] = $results['totalsForAllResults'];
        }

        return $this->statistics[$dateRange];
    }
}
