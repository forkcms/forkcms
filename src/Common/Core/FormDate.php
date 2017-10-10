<?php

namespace Common\Core;

use SpoonFormDate;

/**
 * This is our extended version of \SpoonFormDate
 */
class FormDate extends SpoonFormDate
{
    /**
     * Checks if this field is correctly submitted.
     *
     * @param string $error The error message to set.
     *
     * @return bool
     */
    public function isValid($error = null): bool
    {
        $return = parent::isValid($error);

        if ($return === false) {
            return false;
        }

        $longMask = str_replace(['d', 'm', 'y', 'Y'], ['dd', 'mm', 'yy', 'yyyy'], $this->mask);

        $data = $this->getMethod(true);

        $year = (mb_strpos($longMask, 'yyyy') !== false) ? mb_substr(
            $data[$this->attributes['name']],
            mb_strpos($longMask, 'yyyy'),
            4
        ) : mb_substr($data[$this->attributes['name']], mb_strpos($longMask, 'yy'), 2);
        $month = mb_substr($data[$this->attributes['name']], mb_strpos($longMask, 'mm'), 2);
        $day = mb_substr($data[$this->attributes['name']], mb_strpos($longMask, 'dd'), 2);

        // validate datefields that have a from-date set
        if (mb_strpos($this->attributes['class'], 'inputDatefieldFrom') !== false) {
            $fromDateChunks = explode('-', $this->attributes['data-startdate']);
            $fromDateTimestamp = mktime(12, 00, 00, $fromDateChunks[1], $fromDateChunks[2], $fromDateChunks[0]);

            $givenDateTimestamp = mktime(12, 00, 00, $month, $day, $year);

            if ($givenDateTimestamp < $fromDateTimestamp) {
                if ($error !== null) {
                    $this->setError($error);
                }

                return false;
            }
        } elseif (mb_strpos($this->attributes['class'], 'inputDatefieldTill') !== false) {
            $tillDateChunks = explode('-', $this->attributes['data-enddate']);
            $tillDateTimestamp = mktime(12, 00, 00, $tillDateChunks[1], $tillDateChunks[2], $tillDateChunks[0]);

            $givenDateTimestamp = mktime(12, 00, 00, $month, $day, $year);

            if ($givenDateTimestamp > $tillDateTimestamp) {
                if ($error !== null) {
                    $this->setError($error);
                }

                return false;
            }
        } elseif (mb_strpos($this->attributes['class'], 'inputDatefieldRange') !== false) {
            $fromDateChunks = explode('-', $this->attributes['data-startdate']);
            $fromDateTimestamp = mktime(12, 00, 00, $fromDateChunks[1], $fromDateChunks[2], $fromDateChunks[0]);

            $tillDateChunks = explode('-', $this->attributes['data-enddate']);
            $tillDateTimestamp = mktime(12, 00, 00, $tillDateChunks[1], $tillDateChunks[2], $tillDateChunks[0]);

            $givenDateTimestamp = mktime(12, 00, 00, $month, $day, $year);

            if ($givenDateTimestamp < $fromDateTimestamp || $givenDateTimestamp > $tillDateTimestamp) {
                if ($error !== null) {
                    $this->setError($error);
                }

                return false;
            }
        }

        return true;
    }
}
