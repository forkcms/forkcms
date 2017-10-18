<?php

namespace Backend\Modules\Mailmotor\Actions;

use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\Model;

/**
 * This redirects to settings
 */
final class Index extends ActionIndex
{
    public function execute(): void
    {
        parent::execute();

        $this->redirect(
            Model::createUrlForAction(
                'Settings'
            )
        );
    }
}
