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
 * This action is used to export statistics for a given campaign ID
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class ExportStatisticsCampaign extends BackendBaseAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // action to execute
        $id = \SpoonFilter::getGetValue('id', null, 0);

        // no id's provided
        if (!BackendMailmotorModel::existsCampaign($id)) {
            $this->redirect(
                BackendModel::createURLForAction('Campaigns') . '&error=campaign-does-not-exist'
            );
        } else {
            // at least one id
            BackendMailmotorModel::exportStatisticsByCampaignID($id);
        }

        // redirect
        $this->redirect(BackendModel::createURLForAction('Groups') . '&report=export-failed');
    }
}
