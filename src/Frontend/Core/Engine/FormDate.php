<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is our extended version of \SpoonFormDate
 */
class FormDate extends \SpoonFormDate
{
    /**
     * Checks if this field is correctly submitted.
     *
     * @param string $error The error message to set.
     *
     * @return bool
     */
    public function isValid($error = null)
    {
        // call parent (let them do the hard word)
        $return = parent::isValid($error);

        // already errors detect, no more further testing is needed
        if ($return === false) {
            return false;
        }

        // define long mask
        $longMask = str_replace(['d', 'm', 'y', 'Y'], ['dd', 'mm', 'yy', 'yyyy'], $this->mask);

        // post/get data
        $data = $this->getMethod(true);

        // init some vars
        $year = (mb_strpos($longMask, 'yyyy') !== false) ? mb_substr(
            $data[$this->attributes['name']],
            mb_strpos($longMask, 'yyyy'),
            4
        ) : mb_substr($data[$this->attributes['name']], mb_strpos($longMask, 'yy'), 2);
        $month = mb_substr($data[$this->attributes['name']], mb_strpos($longMask, 'mm'), 2);
        $day = mb_substr($data[$this->attributes['name']], mb_strpos($longMask, 'dd'), 2);

        // validate datefields that have a from-date set
        if (mb_strpos($this->attributes['class'], 'inputDatefieldFrom') !== false) {
            // process from date
            $fromDateChunks = explode('-', $this->attributes['data-startdate']);
            $fromDateTimestamp = mktime(12, 00, 00, $fromDateChunks[1], $fromDateChunks[2], $fromDateChunks[0]);

            // process given date
            $givenDateTimestamp = mktime(12, 00, 00, $month, $day, $year);

            // compare dates
            if ($givenDateTimestamp < $fromDateTimestamp) {
                if ($error !== null) {
                    $this->setError($error);
                }

                return false;
            }
        } elseif (mb_strpos($this->attributes['class'], 'inputDatefieldTill') !== false) {
            // process till date
            $tillDateChunks = explode('-', $this->attributes['data-enddate']);
            $tillDateTimestamp = mktime(12, 00, 00, $tillDateChunks[1], $tillDateChunks[2], $tillDateChunks[0]);

            // process given date
            $givenDateTimestamp = mktime(12, 00, 00, $month, $day, $year);

            // compare dates
            if ($givenDateTimestamp > $tillDateTimestamp) {
                if ($error !== null) {
                    $this->setError($error);
                }

                return false;
            }
        } elseif (mb_strpos($this->attributes['class'], 'inputDatefieldRange') !== false) {
            // process from date
            $fromDateChunks = explode('-', $this->attributes['data-startdate']);
            $fromDateTimestamp = mktime(12, 00, 00, $fromDateChunks[1], $fromDateChunks[2], $fromDateChunks[0]);

            // process till date
            $tillDateChunks = explode('-', $this->attributes['data-enddate']);
            $tillDateTimestamp = mktime(12, 00, 00, $tillDateChunks[1], $tillDateChunks[2], $tillDateChunks[0]);

            // process given date
            $givenDateTimestamp = mktime(12, 00, 00, $month, $day, $year);

            // compare dates
            if ($givenDateTimestamp < $fromDateTimestamp || $givenDateTimestamp > $tillDateTimestamp) {
                if ($error !== null) {
                    $this->setError($error);
                }

                return false;
            }
        }

        /**
         * When the code reaches the point, it means no errors have occurred
         * and truth will out!
         */

        return true;
    }
}
