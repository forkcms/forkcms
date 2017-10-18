<?php

namespace Backend\Modules\Authentication\Actions;

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
