<?php

namespace Backend\Modules\Authentication\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;

/**
 * This is the logout-action, it will logout the current user
 */
class Logout extends BackendBaseAction
{
    public function execute(): void
    {
        parent::execute();
        BackendAuthentication::logout();

        // redirect to login-screen
        $this->redirect(BackendModel::createUrlForAction('Index', $this->getModule()));
    }
}
