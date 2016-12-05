<?php

namespace Backend\Modules\Analytics\Form;

use Backend\Core\Engine\Form;
use Backend\Core\Language\Language;
use Backend\Core\Engine\TwigTemplate;
use Backend\Core\Engine\Model;
use Backend\Modules\Analytics\DateRange\DateRange;

/**
 * A form to change the date range of the analytics module
 */
final class DateRangeType
{
    /** @var Form */
    private $form;

    /** @var DateRange $dateRange */
    private $dateRange;

    /**
     * @param string $name
     * @param DateRange $dateRange
     */
    public function __construct($name, DateRange $dateRange)
    {
        $this->form = new Form($name);
        $this->dateRange = $dateRange;

        $this->build();
    }

    /**
     * @param TwigTemplate $template
     */
    public function parse(TwigTemplate $template)
    {
        $this->form->parse($template);
        $template->assign('startTimestamp', $this->dateRange->getStartDate());
        $template->assign('endTimestamp', $this->dateRange->getEndDate());
    }

    /**
     * @return bool
     */
    public function handle()
    {
        $this->form->cleanupFields();

        if (!$this->form->isSubmitted() || !$this->isValid()) {
            return false;
        }

        $fields = $this->form->getFields();

        $newStartDate = Model::getUTCTimestamp($fields['start_date']);
        $newEndDate = Model::getUTCTimestamp($fields['end_date']);

        $this->dateRange->update($newStartDate, $newEndDate);

        return true;
    }

    /**
     * Build up the form
     */
    private function build()
    {
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

    /**
     * @return bool
     */
    private function isValid()
    {
        $fields = $this->form->getFields();

        if (!$fields['start_date']->isFilled(Language::err('FieldIsRequired')) ||
            !$fields['end_date']->isFilled(Language::err('FieldIsRequired'))
        ) {
            return $this->form->isCorrect();
        }

        if (!$fields['start_date']->isValid(Language::err('DateIsInvalid')) ||
            !$fields['end_date']->isValid(Language::err('DateIsInvalid'))
        ) {
            return $this->form->isCorrect();
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

        return $this->form->isCorrect();
    }

    /**
     * @return DateRange
     */
    public function getDateRange()
    {
        return $this->dateRange;
    }
}
