<?php

namespace Backend\Modules\Faq\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;

/**
 * Reorder categories
 */
class Sequence extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // get parameters
        $newIdSequence = trim(\SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));

        // list id
        $ids = (array) explode(',', rtrim($newIdSequence, ','));

        // loop id's and set new sequence
        foreach ($ids as $i => $id) {
            // define category
            $category = BackendFaqModel::getCategory((int) $id);

            // update sequence
            if ($category) {
                // change sequence
                $category['sequence'] = $i + 1;

                // update category
                BackendFaqModel::updateCategory($category);
            }
        }

        // success output
        $this->output(self::OK, null, 'sequence updated');
    }
}
