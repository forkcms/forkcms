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
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class GetInfo extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // call parent
        parent::execute();

        // get parameters
        $id = \SpoonFilter::getPostValue('id', null, 0, 'int');

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
