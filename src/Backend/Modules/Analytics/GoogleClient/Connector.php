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

        return $results['metrics']['ga:pageviews'];
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

        return $results['metrics']['ga:users'];
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

        return $results['metrics']['ga:pageviewsPerSession'];
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

        return $results['metrics']['ga:avgSessionDuration'];
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

        return $results['metrics']['ga:percentNewSessions'];
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

        return $results['metrics']['ga:bounceRate'];
    }

    /**
     * Returns the visitors graph data
     *
     * @param  int $startDate
     * @param  int $endDate
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
     * @return array
     */
    public function getSourceGraphData($startDate, $endDate)
    {
        $results = $this->getData($startDate, $endDate);

        return $results['sourceGraphData'];
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
            $this->statistics[$dateRange] = array(
                'metrics' => $this->getMetrics($startDate, $endDate),
                'visitGraphData' => $this->collectVisitGraphData($startDate, $endDate),
                'sourceGraphData' => $this->collectSourceGraphData($startDate, $endDate),
            );
        }

        return $this->statistics[$dateRange];
    }

    /**
     * Fetches some metrics for a certain date range
     *
     * @param  int $startData
     * @param  int $endDate
     * @return array
     */
    private function getMetrics($startDate, $endDate)
    {
        $metrics = $this->analytics->data_ga->get(
            'ga:' . Model::getModuleSetting('Analytics', 'profile'),
            date('Y-m-d', $startDate),
            date('Y-m-d', $endDate),
            'ga:pageviews,ga:users,ga:pageviewsPerSession,ga:avgSessionDuration,ga:percentNewSessions,ga:bounceRate'
        );

        return $metrics['totalsForAllResults'];
    }

    /**
     * Fetches the data needed to build the visitors graph for a date range
     *
     * @param  int $startData
     * @param  int $endDate
     * @return array
     */
    private function collectVisitGraphData($startDate, $endDate)
    {
        $visitGraphData = $this->analytics->data_ga->get(
            'ga:' . Model::getModuleSetting('Analytics', 'profile'),
            date('Y-m-d', $startDate),
            date('Y-m-d', $endDate),
            'ga:pageviews,ga:users',
            array(
                'dimensions' => 'ga:date',
                'sort' =>'ga:date',
            )
        );

        // make sure our column headers are the metric names, not just numbers
        $namedRows = array();
        foreach ($visitGraphData['rows'] as $dataRow) {
            $namedRow = array();
            foreach ($dataRow as $key => $value) {
                $headerName = $visitGraphData['columnHeaders'][$key]->getName();

                // convert the date to a timestamp
                if ($headerName === 'ga:date') {
                    $value = \DateTime::createFromFormat('Ymd', $value)->format('U');
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
     * @param  int $startData
     * @param  int $endDate
     * @return array
     */
    private function collectSourceGraphData($startDate, $endDate)
    {
        $sourceGraphData = $this->analytics->data_ga->get(
            'ga:' . Model::getModuleSetting('Analytics', 'profile'),
            date('Y-m-d', $startDate),
            date('Y-m-d', $endDate),
            'ga:pageviews',
            array(
                'dimensions' => 'ga:medium',
                'sort' =>'-ga:pageviews',
            )
        );

        // make sure our column headers are the metric names, not just numbers
        $namedRows = array();
        foreach ($sourceGraphData['rows'] as $dataRow) {
            $namedRow = array();
            foreach ($dataRow as $key => $value) {
                $headerName = $sourceGraphData['columnHeaders'][$key]->getName();
                $namedRow[str_replace(':', '_', $headerName)] = $value;
            }
            $namedRows[] = $namedRow;
        }

        return $namedRows;
    }
}
