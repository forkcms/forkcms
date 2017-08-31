<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;

class MediaBrowser extends BackendBaseActionIndex
{
    public function execute(): void
    {
        parent::execute();
        $this->parse();
        $this->display();
    }
}
