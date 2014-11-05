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
 * This action is used to export email addresses by group ID
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class ExportAddresses extends BackendBaseAction
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
        if (empty($id)) {
            $this->redirect(BackendModel::createURLForAction('Groups') . '&error=no-items-selected');
        } else {
            // at least one id
            // export all addresses
            if ($id == 'all') {
                // fetch records
                $records = BackendMailmotorModel::getAddresses();

                // export records
                BackendMailmotorModel::exportAddresses($records);
            } else {
                // export addresses by group ID
                BackendMailmotorModel::exportAddressesByGroupID($id);
            }
        }

        // redirect
        $this->redirect(BackendModel::createURLForAction('Groups') . '&report=export-failed');
    }
}
