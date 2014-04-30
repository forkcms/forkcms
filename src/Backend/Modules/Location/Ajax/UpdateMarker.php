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

        // validate id
        if ($itemId == 0) $this->output(self::BAD_REQUEST, null, BL::err('NonExisting'));

        // validated
        else {
            //update
            $updateData = array(
                'id' => $itemId,
                'lat' => $lat,
                'lng' => $lng,
                'language' => BL::getWorkingLanguage()
            );

            BackendLocationModel::update($updateData);

            // output
            $this->output(self::OK);
        }
    }
}
