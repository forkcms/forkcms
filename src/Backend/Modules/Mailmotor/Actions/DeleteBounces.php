<?php

namespace Backend\Modules\Mailmotor\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailmotor\Engine\Model as BackendMailmotorModel;
use Backend\Modules\Mailmotor\Engine\CMHelper as BackendMailmotorCMHelper;


/**
 * This action will delete all bounces for a specified mailing
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class DeleteBounces extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('mailing_id', 'int');

        // does the item exist
        if (BackendMailmotorModel::existsMailing($this->id)) {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // fetch the mailing
            $mailing = BackendMailmotorModel::getMailing($this->id);

            // get all data for the user we want to edit
            $records = (array) BackendMailmotorCMHelper::getCM()->getCampaignBounces($mailing['cm_id']);

            // reset some data
            if (!empty($records)) {
                // loop the records
                foreach ($records as $record) {
                    // only remove the hard bounces
                    if ($record['bounce_type'] == 'Hard') {
                        // remove the address
                        BackendMailmotorModel::deleteAddresses($record['email']);
                    }
                }
            }

            // trigger event
            BackendModel::triggerEvent($this->getModule(), 'after_delete_bounces');

            // user was deleted, so redirect
            $this->redirect(
                BackendModel::createURLForAction('Statistics') . '&id=' . $mailing['id'] . '&report=deleted-bounces'
            );
        } else {
            $this->redirect(BackendModel::createURLForAction('Statistics') . '&error=no-bounces');
        }
    }
}
