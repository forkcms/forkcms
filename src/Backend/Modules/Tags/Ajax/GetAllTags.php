<?php

namespace Backend\Modules\Tags\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
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
