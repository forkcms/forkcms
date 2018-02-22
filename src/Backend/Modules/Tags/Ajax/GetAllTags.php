<?php

namespace App\Backend\Modules\Tags\Ajax;

use App\Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use App\Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * This will return an array with all existing tags
 */
class GetAllTags extends BackendBaseAJAXAction
{
    public function execute(): void
    {
        parent::execute();
        $this->output(Response::HTTP_OK, BackendTagsModel::getTagNames());
    }
}
