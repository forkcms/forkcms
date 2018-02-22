<?php

namespace App\Backend\Modules\Mailmotor\Actions;

use App\Backend\Core\Engine\Base\ActionIndex;
use App\Backend\Core\Engine\Model;

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
