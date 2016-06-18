<?php

namespace Backend\Modules\Analytics\Actions;

use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Core\Engine\DataGridArray;
use Backend\Modules\Analytics\DateRange\DateRange;

/**
 * This is the index-action (default), it will display the overview of analytics data
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 * @author Jelmer Prins <jelmer@sumocoders.be>
 */
final class Index extends ActionIndex
{
    /**
     * @var Form
     */
    private $form;

    /**
     * @var DateRange
     */
    private $dateRange;

    public function execute()
    {
        parent::execute();

        /* Set the initial date range */
        $this->dateRange = new DateRange();

        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function loadForm()
    {
        $this->form = new Form('dates');
        $this->form->addDate(
            'start_date',
            $this->dateRange->getStartDate(),
            'range',
            mktime(0, 0, 0, 1, 1, 2005),
            time()
        );
        $this->form->addDate(
            'end_date',
            $this->dateRange->getEndDate(),
            'range',
            mktime(0, 0, 0, 1, 1, 2005),
            time()
        );
    }

    private function validateForm()
    {
        if ($this->form->isSubmitted()) {
            $fields = $this->form->getFields();

            if (!$fields['start_date']->isFilled(Language::err('FieldIsRequired')) ||
                !$fields['end_date']->isFilled(Language::err('FieldIsRequired'))
            ) {
                return;
            }

            if (!$fields['start_date']->isValid(Language::err('DateIsInvalid')) ||
                !$fields['end_date']->isValid(Language::err('DateIsInvalid'))
            ) {
                return;
            }

            $newStartDate = Model::getUTCTimestamp($fields['start_date']);
            $newEndDate = Model::getUTCTimestamp($fields['end_date']);

            // startdate cannot be before 2005 (earliest valid google startdate)
            if ($newStartDate < mktime(0, 0, 0, 1, 1, 2005)) {
                $fields['start_date']->setError(Language::err('DateRangeIsInvalid'));
            }

            // enddate cannot be in the future
            if ($newEndDate > time()) {
                $fields['start_date']->setError(Language::err('DateRangeIsInvalid'));
            }

            // enddate cannot be before the startdate
            if ($newStartDate > $newEndDate) {
                $fields['start_date']->setError(Language::err('DateRangeIsInvalid'));
            }

            if ($this->form->isCorrect()) {
                $this->dateRange->update($newStartDate, $newEndDate);
            }
        }
    }

    private function parseDateRangeForm()
    {
        $this->form->parse($this->tpl);
        $this->tpl->assign('startTimestamp', $this->dateRange->getStartDate());
        $this->tpl->assign('endTimestamp', $this->dateRange->getEndDate());
    }

    protected function parse()
    {
        parent::parse();

        $this->parseDateRangeForm();
        $this->parseAnalytics();
    }

    private function parseAnalytics()
    {
        // if we don't have a token anymore, redirect to the settings page
        if ($this->get('fork.settings')->get($this->getModule(), 'certificate') === null
            || $this->get('fork.settings')->get($this->getModule(), 'account') === null
            || $this->get('fork.settings')->get($this->getModule(), 'web_property_id') === null
            || $this->get('fork.settings')->get($this->getModule(), 'profile') === null
        ) {
            $this->redirect(Model::createURLForAction('Settings'));
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
            'source_graph_data' => 'getSourceGraphData'
        ];

        foreach ($analyticsTemplateToFunctionMap as $templateVariableName => $functionName) {
            $this->tpl->assign(
                $templateVariableName,
                $analytics->$functionName($this->dateRange->getStartDate(), $this->dateRange->getEndDate())
            );
        }

        $dataGrid = new DataGridArray(
            $analytics->getMostVisitedPagesData($this->dateRange->getStartDate(), $this->dateRange->getEndDate())
        );
        $this->tpl->assign(
            'dataGridMostViewedPages',
            (string) $dataGrid->getContent()
        );
    }
}
