<?php

namespace Backend\Modules\Analytics\Actions;

use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\Model;
use Backend\Core\Engine\DataGridArray;
use Backend\Modules\Analytics\DateRange\DateRange;
use Backend\Modules\Analytics\Form\DateRangeType;

/**
 * This is the index-action (default), it will display the overview of analytics data
 */
final class Index extends ActionIndex
{
    /**
     * @var DateRange
     */
    private $dateRange;

    public function execute(): void
    {
        parent::execute();

        /* Set the initial date range */
        $this->dateRange = new DateRange();

        $this->handleDateRangeForm();
        $this->parse();
        $this->display();
    }

    /**
     * The form will update the date range filter if needed
     */
    private function handleDateRangeForm(): void
    {
        $dateRangeForm = new DateRangeType('date_range', $this->dateRange);

        if ($dateRangeForm->handle()) {
            $this->dateRange = $dateRangeForm->getDateRange();
        }

        $dateRangeForm->parse($this->template);
    }

    protected function parse(): void
    {
        parent::parse();

        // if we don't have a token anymore, redirect to the settings page
        if ($this->get('fork.settings')->get($this->getModule(), 'certificate') === null
            || $this->get('fork.settings')->get($this->getModule(), 'account') === null
            || $this->get('fork.settings')->get($this->getModule(), 'web_property_id') === null
            || $this->get('fork.settings')->get($this->getModule(), 'profile') === null
        ) {
            $this->redirect(Model::createUrlForAction('Settings'));
        }

        $this->header->addJS('highcharts.js', 'Core', false);
        $analytics = $this->get('analytics.connector');
        $analyticsTemplateToFunctionMap = [
            'page_views' => 'getPageViews',
            'visitors' => 'getVisitors',
            'pages_per_visit' => 'getPagesPerVisit',
            'time_on_site' => 'getTimeOnSite',
            'new_sessions_percentage' => 'getNewSessionsPercentage',
            'bounce_rate' => 'getBounceRate',
            'visitors_graph_data' => 'getVisitorsGraphData',
            'source_graph_data' => 'getSourceGraphData',
        ];

        foreach ($analyticsTemplateToFunctionMap as $templateVariableName => $functionName) {
            $this->template->assign(
                $templateVariableName,
                $analytics->$functionName($this->dateRange->getStartDate(), $this->dateRange->getEndDate())
            );
        }

        $dataGrid = new DataGridArray(
            $analytics->getMostVisitedPagesData($this->dateRange->getStartDate(), $this->dateRange->getEndDate())
        );
        $this->template->assign('dataGridMostViewedPages', $dataGrid->getContent());
    }
}
