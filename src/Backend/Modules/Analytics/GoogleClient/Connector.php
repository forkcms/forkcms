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

    public function __construct(Google_Service_Analytics $analytics)
    {
        $this->analytics = $analytics;
    }

    public function getPageViews($startDate, $endDate)
    {
        $results = $this->analytics->data_ga->get(
            'ga:' . Model::getModuleSetting('Analytics', 'profile'),
            date('Y-m-d', $startDate),
            date('Y-m-d', $endDate),
            'ga:pageviews'
        );

        return $results['totalsForAllResults']['ga:pageviews'];
    }
}
