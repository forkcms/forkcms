<?php

namespace App\Backend\Modules\Location\Ajax;

use App\Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use App\Backend\Core\Language\Language as BL;
use App\Backend\Modules\Location\Engine\Model as BackendLocationModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is an ajax handler
 */
class UpdateMarker extends BackendBaseAJAXAction
{
    public function execute(): void
    {
        parent::execute();

        // get parameters
        $itemId = trim($this->getRequest()->request->getInt('id'));
        $lat = (float) $this->getRequest()->request->get('lat');
        $lng = (float) $this->getRequest()->request->get('lng');

        // validate id
        if ($itemId === 0) {
            $this->output(Response::HTTP_BAD_REQUEST, null, BL::err('NonExisting'));

            return;
        }

        //update
        $updateData = [
            'id' => $itemId,
            'lat' => $lat,
            'lng' => $lng,
            'language' => BL::getWorkingLanguage(),
        ];

        BackendLocationModel::update($updateData);

        // output
        $this->output(Response::HTTP_OK);
    }
}
