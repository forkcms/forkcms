<?php

namespace Backend\Modules\Settings\Actions;

use Backend\Core\Engine\Base\Action;

class Tools extends Action
{
    public function execute(): void
    {
        parent::execute();

        $this->display();
    }
}
