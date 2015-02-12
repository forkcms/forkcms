<?php

namespace Backend\Modules\Location\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Location\Engine\Model as BackendLocationModel;

/**
 * This is an ajax handler
 *
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Mathias Dewelde <mathias@dewelde.be>
 */
class UpdateMarker extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // get parameters
        $itemId = trim(\SpoonFilter::getPostValue('id', null, '', 'int'));
        $lat = \SpoonFilter::getPostValue('lat', null, null, 'float');
        $lng = \SpoonFilter::getPostValue('lng', null, null, 'float');

        $item = BackendLocationModel::get($itemId);

        // does the item exists
        if ($itemId === null || empty($item)) {
            $this->output(self::BAD_REQUEST, null, BL::err('NonExisting'));
        } else {
            //update
            $item->setLat($lat);
            $item->setLng($lng);
            BackendLocationModel::update($item);

            // output
            $this->output(self::OK);
        }
    }
}
