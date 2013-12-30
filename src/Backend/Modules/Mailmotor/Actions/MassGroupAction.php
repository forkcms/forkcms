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
use Backend\Modules\Mailmotor\Engine\CMHelper as BackendMailmotorCMHelper;

/**
 * This action is used to update one or more groups (delete, ...)
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class MassGroupAction extends BackendBaseAction
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
            $this->redirect(BackendModel::createURLForAction('Groups') . '&error=no-selection');
        } else {
            // redefine id's
            $ids = (array) $_GET['id'];

            // delete comment(s)
            if ($action == 'delete') {
                BackendMailmotorCMHelper::deleteGroups($ids);

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_delete_groups', array('ids' => $ids));
            }
        }

        // redirect
        $this->redirect(BackendModel::createURLForAction('Groups') . '&report=delete-groups');
    }
}
