<?php

namespace Backend\Modules\Analytics\Widgets;

use Backend\Core\Engine\Base\Widget;

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
        $this->setColumn('middle');
        $this->setPosition(0);

        $startDate = strtotime('-1 week', mktime(0, 0, 0));
        $endDate = mktime(0, 0, 0);
        $analytics = $this->get('analytics.connector');

        $this->tpl->assign(
            'source_graph_data',
            $analytics->getSourceGraphData($startDate, $endDate)
        );

        $this->header->addJS('highcharts.js', 'Core', false);
        $this->header->addJS('Analytics.js', 'Analytics', false);
        $this->display();
    }
}
