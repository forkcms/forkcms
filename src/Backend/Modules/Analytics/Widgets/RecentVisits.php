<?php

namespace Backend\Modules\Analytics\Widgets;

use Backend\Core\Engine\Base\Widget;

/**
 * This widget will show recent visits
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
class RecentVisits extends Widget
{
    /**
     * Execute the widget
     */
    public function execute()
    {
        $startDate = strtotime('-1 week', mktime(0, 0, 0));
        $endDate = mktime(0, 0, 0);
        $analytics = $this->get('analytics.connector');

        $this->tpl->assign(
            'visitors_graph_data',
            $analytics->getVisitorsGraphData($startDate, $endDate)
        );

        $this->header->addJS('highcharts.js', 'Core', false);
        $this->header->addJS('Analytics.js', 'Analytics', false);
        $this->display();
    }
}
