<?php

namespace ForkCMS\Backend\Modules\Pages\Ajax;

use ForkCMS\Backend\Core\Engine\Base\AjaxAction;
use ForkCMS\Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * This edit-action will get the page info using Ajax
 */
class GetInfo extends AjaxAction
{
    public function execute(): void
    {
        // call parent
        parent::execute();

        // get parameters
        $id = $this->getRequest()->request->getInt('id');

        // validate
        if ($id === 0) {
            $this->output(Response::HTTP_BAD_REQUEST, null, 'no id provided');

            return;
        }

        // get page
        $page = BackendPagesModel::get($id);

        // output
        $this->output(Response::HTTP_OK, $page);
    }
}
