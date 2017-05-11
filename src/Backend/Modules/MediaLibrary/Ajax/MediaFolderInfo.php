<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Common\Exception\AjaxExitException;

/**
 * This edit-action will get the item info using Ajax
 */
class MediaFolderInfo extends BackendBaseAJAXAction
{
    public function execute(): void
    {
        // call parent
        parent::execute();

        // get parameters
        $id = $this->get('request')->request->getInt('id', 0);

        if ($id === 0) {
            throw new AjaxExitException('no id provided');
        }

        // Currently always allow to be moved
        $this->output(self::OK, ['allow_move' => 'Y']);
    }
}
