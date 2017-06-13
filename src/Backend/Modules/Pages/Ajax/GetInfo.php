<?php

namespace Backend\Modules\Pages\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;

/**
 * This edit-action will get the page info using Ajax
 */
class GetInfo extends BackendBaseAJAXAction
{
    public function execute(): void
    {
        // call parent
        parent::execute();

        // get parameters
        $id = $this->getRequest()->request->getInt('id');

        // validate
        if ($id === 0) {
            $this->output(self::BAD_REQUEST, null, 'no id provided');
        } else {
            // get page
            $page = BackendPagesModel::get($id);

            // output
            $this->output(self::OK, $page);
        }
    }
}
