<?php

namespace Backend\Modules\Analytics\Actions;

use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Core\Engine\DataGridArray;

/**
 * This is the index-action (default), it will display the overview of analytics data
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
final class Index extends ActionIndex
{
    /**
     * The start and end timestamp of the collected data
     *
     * @var int
     */
    private $startDate;
    private $endDate;

    /**
     * @var Form
     */
    private $form;

    public function execute()
    {
        parent::execute();
        $this->setDates();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function setDates()
    {
        $this->startDate = strtotime('-1 week', mktime(0, 0, 0));
        $this->endDate = mktime(0, 0, 0);
    }

    private function loadForm()
    {
        $this->form = new Form('dates');
        $this->form->addDate('start_date', $this->startDate, 'range', mktime(0, 0, 0, 1, 1, 2005), time());
        $this->form->addDate('end_date', $this->endDate, 'range', mktime(0, 0, 0, 1, 1, 2005), time());
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
                $fields['start_date']->setError(BL::err('DateRangeIsInvalid'));
            }

            // enddate cannot be in the future
            if ($newEndDate > time()) {
                $fields['start_date']->setError(BL::err('DateRangeIsInvalid'));
            }

            // enddate cannot be before the startdate
            if ($newStartDate > $newEndDate) {
                $fields['start_date']->setError(BL::err('DateRangeIsInvalid'));
            }

            if ($this->form->isCorrect()) {
                $this->startDate = $newStartDate;
                $this->endDate = $newEndDate;
            }
        }
    }

    protected function parse()
    {
        parent::parse();

        $this->header->addJS('highcharts.js', 'Core', false);

        $this->form->parse($this->tpl);
        $this->tpl->assign('startTimestamp', $this->startDate);
        $this->tpl->assign('endTimestamp', $this->endDate);

        // if we don't have a token anymore, redirect to the settings page
        if (
            $this->get('fork.settings')->get($this->getModule(), 'certificate') === null
            || $this->get('fork.settings')->get($this->getModule(), 'account') === null
            || $this->get('fork.settings')->get($this->getModule(), 'web_property_id') === null
            || $this->get('fork.settings')->get($this->getModule(), 'profile') === null
        ) {
            $this->redirect(Model::createURLForAction('Settings'));
        }

        $analytics = $this->get('analytics.connector');

        $this->tpl->assign(
            'page_views',
            $analytics->getPageViews($this->startDate, $this->endDate)
        );
        $this->tpl->assign(
            'visitors',
            $analytics->getVisitors($this->startDate, $this->endDate)
        );
        $this->tpl->assign(
            'pages_per_visit',
            $analytics->getPagesPerVisit($this->startDate, $this->endDate)
        );
        $this->tpl->assign(
            'time_on_site',
            $analytics->getTimeOnSite($this->startDate, $this->endDate)
        );
        $this->tpl->assign(
            'new_sessions_percentage',
            $analytics->getNewSessionsPercentage($this->startDate, $this->endDate)
        );
        $this->tpl->assign(
            'bounce_rate',
            $analytics->getBounceRate($this->startDate, $this->endDate)
        );
        $this->tpl->assign(
            'visitors_graph_data',
            $analytics->getVisitorsGraphData($this->startDate, $this->endDate)
        );
        $this->tpl->assign(
            'source_graph_data',
            $analytics->getSourceGraphData($this->startDate, $this->endDate)
        );
        $dataGrid = new DataGridArray(
            $analytics->getMostVisitedPagesData($this->startDate, $this->endDate)
        );
        $this->tpl->assign(
            'dataGridMostViewedPages',
            (string) $dataGrid->getContent()
        );
    }
}
