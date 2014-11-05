<?php

namespace Backend\Modules\Tags\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/**
 * This action is used to perform mass actions on tags (delete, ...)
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class MassAction extends BackendBaseAction
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
                BackendModel::createURLForAction('Index') . '&error=no-selection'
            );
        } else {
            // at least one id
            // redefine id's
            $aIds = (array) $_GET['id'];

            // delete comment(s)
            if ($action == 'delete') {
                BackendTagsModel::delete($aIds);
            }
        }

        // redirect
        $this->redirect(
            BackendModel::createURLForAction('Index') . '&report=deleted'
        );
    }
}
