<?php

namespace Backend\Modules\Analytics\DateRange;

final class DateRange
{
    /**
     * The start timestamp of the collected data
     *
     * @var int
     */
    private $startDate;

    /**
     * The end timestamp of the collected data
     *
     * @var int
     */
    private $endDate;

    /**
     * Sets the initial dates for the range
     */
    public function __construct()
    {
        $this->startDate = strtotime('-1 week', mktime(0, 0, 0));
        $this->endDate = mktime(0, 0, 0);
    }

    public function update(int $startDate, int $endDate): void
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getStartDate(): int
    {
        return $this->startDate;
    }

    public function getEndDate(): int
    {
        return $this->endDate;
    }
}
