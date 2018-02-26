<?php

namespace ForkCMS\Backend\Modules\Tags\Ajax;

use ForkCMS\Backend\Core\Engine\Base\AjaxAction;
use ForkCMS\Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * This will return an array with all existing tags
 */
class GetAllTags extends AjaxAction
{
    public function execute(): void
    {
        parent::execute();
        $this->output(Response::HTTP_OK, BackendTagsModel::getTagNames());
    }
}
