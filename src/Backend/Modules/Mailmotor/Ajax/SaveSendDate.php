<?php

namespace Backend\Modules\Mailmotor\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailmotor\Engine\Model as BackendMailmotorModel;

/**
 * This saved the date on which the mailing is to be sent
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class SaveSendDate extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // get parameters
        $mailingId = \SpoonFilter::getPostValue('mailing_id', null, '', 'int');
        $sendOnDate = \SpoonFilter::getPostValue('send_on_date', null, BackendModel::getUTCDate('d/m/Y'));
        $sendOnTime = \SpoonFilter::getPostValue('send_on_time', null, BackendModel::getUTCDate('H:i'));
        $messageDate = $sendOnDate;

        // validate mailing ID
        if ($mailingId == '') {
            $this->output(static::BAD_REQUEST, null, 'Provide a valid mailing ID');
        } else {
            // validate date & time
            if ($sendOnDate == '' || $sendOnTime == '') {
                $this->output(
                    static::BAD_REQUEST,
                    null,
                    'Provide a valid send date date provided'
                );
            } else {
                // record is empty
                if (!BackendMailmotorModel::existsMailing($mailingId)) {
                    $this->output(
                        static::BAD_REQUEST,
                        null,
                        BL::err('MailingDoesNotExist', $this->getModule())
                    );
                } else {
                    // reverse the date and make it a proper
                    $explodedDate = explode('/', $sendOnDate);
                    $sendOnDate = $explodedDate[2] . '-' . $explodedDate[1] . '-' . $explodedDate[0];

                    // calc full send timestamp
                    $sendTimestamp = strtotime($sendOnDate . ' ' . $sendOnTime);

                    // build data
                    $item['id'] = $mailingId;
                    $item['send_on'] = BackendModel::getUTCDate('Y-m-d H:i:s', $sendTimestamp);
                    $item['edited_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');

                    // update mailing
                    BackendMailmotorModel::updateMailing($item);

                    // trigger event
                    BackendModel::triggerEvent($this->getModule(), 'after_edit_mailing_step4', array('item' => $item));

                    // output
                    $this->output(
                        static::OK,
                        array('mailing_id' => $mailingId, 'timestamp' => $sendTimestamp),
                        sprintf(BL::msg('SendOn', $this->getModule()), $messageDate, $sendOnTime)
                    );
                }
            }
        }
    }
}
