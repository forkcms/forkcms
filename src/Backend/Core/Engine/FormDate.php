<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is our extended version of \SpoonFormDate
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FormDate extends \SpoonFormDate
{
    /**
     * Checks if this field is correctly submitted.
     *
     * @param string $error The errormessage to set.
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
        $longMask = str_replace(array('d', 'm', 'y', 'Y'), array('dd', 'mm', 'yy', 'yyyy'), $this->mask);

        // post/get data
        $data = $this->getMethod(true);

        // init some vars
        $year = (strpos($longMask, 'yyyy') !== false) ? substr(
            $data[$this->attributes['name']],
            strpos($longMask, 'yyyy'),
            4
        ) : substr($data[$this->attributes['name']], strpos($longMask, 'yy'), 2);
        $month = substr($data[$this->attributes['name']], strpos($longMask, 'mm'), 2);
        $day = substr($data[$this->attributes['name']], strpos($longMask, 'dd'), 2);

        // validate datefields that have a from-date set
        if (strpos($this->attributes['class'], 'inputDatefieldFrom') !== false) {
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
        } elseif (strpos($this->attributes['class'], 'inputDatefieldTill') !== false) {
            // validate datefield that have a till-date set
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
        } elseif (strpos($this->attributes['class'], 'inputDatefieldRange') !== false) {
            // validate datefield that have a from and till-date set
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

        // The form didn't contain any errors
        return true;
    }
}
