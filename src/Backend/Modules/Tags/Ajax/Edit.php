<?php

namespace Backend\Modules\Tags\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Uri as CommonUri;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/**
 * This edit-action will update tags using Ajax
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Edit extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // get parameters
        $id = \SpoonFilter::getPostValue('id', null, 0, 'int');
        $tag = trim(\SpoonFilter::getPostValue('value', null, '', 'string'));

        // validate id
        if ($id === 0) {
            $this->output(self::BAD_REQUEST, null, 'no id provided');
        } else {
            // validate tag name
            if ($tag === '') {
                $this->output(self::BAD_REQUEST, null, BL::err('NameIsRequired'));
            } else {
                // check if tag exists
                if (BackendTagsModel::existsTag($tag)) {
                    $this->output(self::BAD_REQUEST, null, BL::err('TagAlreadyExists'));
                } else {
                    $item['id'] = $id;
                    $item['tag'] = \SpoonFilter::htmlspecialchars($tag);
                    $item['url'] = BackendTagsModel::getURL(
                        CommonUri::getUrl(\SpoonFilter::htmlspecialcharsDecode($item['tag'])),
                        $id
                    );

                    BackendTagsModel::update($item);
                    $this->output(self::OK, $item, vsprintf(BL::msg('Edited'), array($item['tag'])));
                }
            }
        }
    }
}
