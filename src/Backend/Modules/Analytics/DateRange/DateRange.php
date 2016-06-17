<?php

namespace Backend\Modules\Orders\DateRange;

/**
 * @author Jelmer Prins <jelmer@sumocoders.be>
 */
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

    /**
     * Set the new start and end date
     *
     * @param int $startDate
     * @param int $endDate
     */
    public function update($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return int
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return int
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
}
