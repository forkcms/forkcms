<?php

namespace App\Backend\Modules\Settings\Actions;

use App\Backend\Core\Engine\Base\Action;

class Tools extends Action
{
    public function execute(): void
    {
        parent::execute();

        $this->display();
    }
}
