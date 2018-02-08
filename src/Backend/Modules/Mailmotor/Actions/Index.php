<?php

namespace Backend\Modules\Mailmotor\Actions;

use Backend\Core\Engine\Base\ActionIndex;
use App\Component\Model\BackendModel;

/**
 * This redirects to settings
 */
final class Index extends ActionIndex
{
    public function execute(): void
    {
        parent::execute();

        $this->redirect(
            BackendModel::createUrlForAction(
                'Settings'
            )
        );
    }
}
