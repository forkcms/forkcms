<?php

namespace Backend\Modules\Mailmotor\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailmotor\Engine\Model as BackendMailmotorModel;

/**
 * This action is used to update one or more campaigns (delete, ...)
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class MassCampaignAction extends BackendBaseAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // action to execute
        $action = \SpoonFilter::getGetValue('action', array('delete'), 'delete');

        // no id's provided
        if (!isset($_GET['id'])) {
            $this->redirect(
                BackendModel::createURLForAction('Campaigns') . '&error=no-items-selected'
            );
        } else {
            // redefine id's
            $ids = (array) $_GET['id'];

            // delete comment(s)
            if ($action == 'delete') {
                BackendMailmotorModel::deleteCampaigns($ids);

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_delete_campaigns', array('ids' => $ids));
            }
        }

        // redirect
        $this->redirect(BackendModel::createURLForAction('Campaigns') . '&report=delete-campaigns');
    }
}
