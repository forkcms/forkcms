<?php

namespace ForkCMS\Backend\Modules\Mailmotor\Actions;

use ForkCMS\Backend\Core\Engine\Base\ActionIndex;
use ForkCMS\Backend\Core\Engine\Model;

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
