<?php

namespace Backend\Modules\Pages\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language as BL;
use Backend\Core\Language\Locale;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * This edit-action will reorder moved pages using Ajax
 */
class Move extends BackendBaseAJAXAction
{
    public function execute(): void
    {
        // call parent
        parent::execute();

        // get parameters
        $id = $this->getRequest()->request->getInt('id');
        $droppedOn = $this->getRequest()->request->getInt('dropped_on', -1);
        $typeOfDrop = $this->getRequest()->request->get('type', '');
        $tree = $this->getRequest()->request->get('tree');
        if (!in_array($tree, ['main', 'meta', 'footer', 'root'])) {
            $tree = '';
        }

        // init validation
        $errors = [];

        // validate
        if ($id === 0) {
            $errors[] = 'no id provided';
        }
        if ($droppedOn === -1) {
            $errors[] = 'no dropped_on provided';
        }
        if ($typeOfDrop === '') {
            $errors[] = 'no type provided';
        }
        if ($tree === '') {
            $errors[] = 'no tree provided';
        }

        // got errors
        if (!empty($errors)) {
            $this->output(Response::HTTP_BAD_REQUEST, ['errors' => $errors], 'not all fields were filled');

            return;
        }

        // get page
        $success = BackendPagesModel::move($id, $droppedOn, $typeOfDrop, $tree);

        // build cache
        BackendPagesModel::buildCache(Locale::workingLocale());

        // output
        if ($success) {
            $this->output(Response::HTTP_OK, BackendPagesModel::get($id), 'page moved');

            return;
        }

        $this->output(Response::HTTP_INTERNAL_SERVER_ERROR, null, 'page not moved');
    }
}
