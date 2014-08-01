<?php

namespace Backend\Modules\Pages\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;

/**
 * This edit-action will reorder moved pages using Ajax
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Move extends BackendBaseAJAXAction
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
        $droppedOn = \SpoonFilter::getPostValue('dropped_on', null, -1, 'int');
        $typeOfDrop = \SpoonFilter::getPostValue('type', null, '');
        $tree = \SpoonFilter::getPostValue('tree', array('main', 'meta', 'footer', 'root'), '');

        // init validation
        $errors = array();

        // validate
        if ($id === 0) {
            $errors[] = 'no id provided';
        }
        if ($droppedOn === -1) {
            $errors[] = 'no dropped_on provided';
        }
        if ($typeOfDrop == '') {
            $errors[] = 'no type provided';
        }
        if ($tree == '') {
            $errors[] = 'no tree provided';
        }

        // got errors
        if (!empty($errors)) {
            $this->output(self::BAD_REQUEST, array('errors' => $errors), 'not all fields were filled');
        } else {
            // get page
            $success = BackendPagesModel::move($id, $droppedOn, $typeOfDrop, $tree);

            // build cache
            BackendPagesModel::buildCache(BL::getWorkingLanguage());

            // output
            if ($success) {
                $this->output(self::OK, BackendPagesModel::get($id), 'page moved');
            } else {
                $this->output(self::ERROR, null, 'page not moved');
            }
        }
    }
}
