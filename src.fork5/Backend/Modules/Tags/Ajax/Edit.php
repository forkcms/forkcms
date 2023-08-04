<?php

namespace Backend\Modules\Tags\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language as BL;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Common\Uri as CommonUri;
use Symfony\Component\HttpFoundation\Response;

/**
 * This edit-action will update tags using Ajax
 */
class Edit extends BackendBaseAJAXAction
{
    public function execute(): void
    {
        parent::execute();

        // get parameters
        $id = $this->getRequest()->request->getInt('id');
        $tag = trim($this->getRequest()->request->get('value', ''));

        // validate id
        if ($id === 0) {
            $this->output(Response::HTTP_BAD_REQUEST, null, 'no id provided');

            return;
        }
        // validate tag name
        if ($tag === '') {
            $this->output(Response::HTTP_BAD_REQUEST, null, BL::err('NameIsRequired'));

            return;
        }
        // check if tag exists
        if (BackendTagsModel::existsTag($tag)) {
            $this->output(Response::HTTP_BAD_REQUEST, null, BL::err('TagAlreadyExists'));

            return;
        }
        $item = [];
        $item['id'] = $id;
        $item['tag'] = \SpoonFilter::htmlspecialchars($tag);
        $item['url'] = BackendTagsModel::getUrl(
            CommonUri::getUrl(\SpoonFilter::htmlspecialcharsDecode($item['tag'])),
            $id
        );

        BackendTagsModel::update($item);
        $this->output(Response::HTTP_OK, $item, vsprintf(BL::msg('Edited'), [$item['tag']]));
    }
}
