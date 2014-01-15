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
 * This action is used to export statistics by mailing ID
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class ExportStatistics extends BackendBaseAction
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
        if (!BackendMailmotorModel::existsMailing($id)) {
            $this->redirect(
                BackendModel::createURLForAction('Index') . '&error=mailing-does-not-exist'
            );
        } else {
            // at least one id
            BackendMailmotorModel::exportStatistics($id);
        }

        // redirect
        $this->redirect(BackendModel::createURLForAction('Groups') . '&report=export-failed');
    }
}
