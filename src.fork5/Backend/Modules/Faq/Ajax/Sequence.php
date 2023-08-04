<?php

namespace Backend\Modules\Faq\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Reorder categories
 */
class Sequence extends BackendBaseAJAXAction
{
    public function execute(): void
    {
        parent::execute();

        // get parameters
        $newIdSequence = trim($this->getRequest()->request->get('new_id_sequence', ''));

        // list id
        $ids = (array) explode(',', rtrim($newIdSequence, ','));

        // loop id's and set new sequence
        foreach ($ids as $i => $id) {
            // define category
            $category = BackendFaqModel::getCategory((int) $id);

            // update sequence
            if (!empty($category)) {
                // change sequence
                $category['sequence'] = $i + 1;

                // update category
                BackendFaqModel::updateCategory($category);
            }
        }

        // success output
        $this->output(Response::HTTP_OK, null, 'sequence updated');
    }
}
