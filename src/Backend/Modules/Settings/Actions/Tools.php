<?php

namespace ForkCMS\Backend\Modules\Settings\Actions;

use ForkCMS\Backend\Core\Engine\Base\Action;

class Tools extends Action
{
    public function execute(): void
    {
        parent::execute();

        $this->display();
    }
}
