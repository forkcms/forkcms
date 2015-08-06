<?php

namespace Backend\Modules\Analytics\Widgets;

use Backend\Core\Engine\Base\Widget;
use Google_Auth_Exception;

/**
 * This widget will show a trafic sources graph for the last week
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
class TraficSources extends Widget
{
    /**
     * Execute the widget
     */
    public function execute()
    {
        $startDate = strtotime('-1 week', mktime(0, 0, 0));
        $endDate = mktime(0, 0, 0);

        try {
            $analytics = $this->get('analytics.connector');

            $this->tpl->assign(
                'source_graph_data',
                $analytics->getSourceGraphData($startDate, $endDate)
            );

            $this->header->addJS('highcharts.js', 'Core', false);
            $this->header->addJS('Analytics.js', 'Analytics', false);
            $this->display();
        } catch (Google_Auth_Exception $e) {
            // do nothing, analyticis is probably not set up yet.
        }
    }
}
