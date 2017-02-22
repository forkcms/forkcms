<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;

/**
 * This edit-action will get the item info using Ajax
 */
class GetMediaFolderInfo extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // call parent
        parent::execute();

        // get parameters
        $type = \SpoonFilter::getPostValue(
            'type',
            array(
                'folder',
            ),
            null,
            'string'
        );
        $id = \SpoonFilter::getPostValue('id', null, 0, 'int');

        // validate
        if ($type === null) {
            $this->output(
                self::BAD_REQUEST,
                null,
                'no type provided'
            );
        }
        if ($id === 0) {
            $this->output(
                self::BAD_REQUEST,
                null,
                'no id provided'
            );
        }

        // Currently always allow to be moved
        $this->output(
            self::OK,
            ['allow_move' => 'Y']
        );
    }
}
