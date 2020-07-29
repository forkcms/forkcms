<?php

namespace Backend\Modules\Analytics\Actions;

use Backend\Core\Engine\Base\Action;
use Backend\Core\Engine\Model;

/**
 * This is the reset-action. It will remove your coupling with analytics
 */
final class Reset extends Action
{
    public function execute(): void
    {
        $this->checkToken();

        $this->get('fork.settings')->delete($this->getModule(), 'certificate');
        $this->get('fork.settings')->delete($this->getModule(), 'email');
        $this->get('fork.settings')->delete($this->getModule(), 'account');
        $this->get('fork.settings')->delete($this->getModule(), 'web_property_id');
        $this->get('fork.settings')->delete($this->getModule(), 'profile');

        $this->redirect(Model::createUrlForAction('Settings'));
    }
}
