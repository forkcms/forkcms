<?php

namespace Backend\Modules\Analytics\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Analytics\Engine\Model as BackendAnalyticsModel;

/**
 * This action is used to perform mass actions on landing pages (delete, ...)
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class MassLandingPageAction extends BackendBaseAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $action = \SpoonFilter::getGetValue('action', array('delete'), 'delete');

        // no id's provided
        if (!isset($_GET['id'])) {
            $this->redirect(BackendModel::createURLForAction('LandingPages') . '&error=no-items-selected');
        } elseif ($action == 'delete') {
            BackendAnalyticsModel::deleteLandingPage((array) $_GET['id']);
        }

        // redirect
        $this->redirect(BackendModel::createURLForAction('LandingPages') . '&report=' . $action);
    }
}
